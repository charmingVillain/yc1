<template>
    <viewer @inited="inited" class="float-left el-upload-list el-upload-list--picture-card upload-image-com"
            :images="images">

        <image-box v-if="showImageBox" class="mr-2 mb-2 float-left" :src="item.url" v-for="(item, index) in fileList"
                   :style="widthAndHeight" :key="index">
            <span @click="showImage(index)" class="el-upload-list__item-preview"><i class="el-icon-zoom-in"></i></span>
            <span v-if="!disabled" @click="del(index)" class="el-upload-list__item-delete"><i class="el-icon-delete"></i></span>
        </image-box>

        <el-upload
                :style="widthAndHeight"
                v-if="showUpload"
                class="float-left"
                :multiple="multiple"
                :class="{'el-upload-dragger-b-none' : showSingle}"
                :on-success="handleSuccess"
                :before-upload="beforeUpload"
                :action="uploadUrl"
                :show-file-list="showFileList"
                :headers="{
                      'X-CSRF-TOKEN': token.content
                    }"
                drag
                name="image"
        >
            <image-box v-if="showSingle" class="mr-0 mb-0" :src="item.url" v-for="(item, index) in fileList"
                       :style="widthAndHeight" :key="index">
                <span @click.prevent.stop="showImage(index)" class="el-upload-list__item-preview"><i
                        class="el-icon-zoom-in"></i></span>
            </image-box>
            <slot>
                <template v-if="!showSingle">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip">只能上传图片文件，且不超过{{ singleFileSize }}M</div>
                </template>
            </slot>
        </el-upload>
        <div class="clearfix"></div>
    </viewer>
</template>

<script>
    import ImageBox from './ImageBox'

    export default {
        components: {
            ImageBox
        },
        props: {
            multiple: {
                type: Boolean,
                default: true
            },
            uploadUrl: {
                type: String,
                default: '/admin/file/image'
            },
            disabled: {
                type: Boolean,
                default: false
            },
            // 单文件大小 单位M
            singleFileSize: {
                type: Number,
                default: 10
            },
            typeAllow: {
                type: Array,
                default() {
                    return ['image/jpeg', 'image/jpg', 'image/gif', 'image/png', 'image/bmp']
                }
            },
            width: {
                type: String,
                default: '360px'
            },
            height: {
                type: String,
                default: '180px'
            },
            value: {
                type: [Number, Array]
            },
            // 自动根据文件id获取图片信息
            enableFileInfo: {
                type: Boolean,
                default: true
            },
            max: {
                type: [Boolean, Number],
                default: false
            },
            showFileList:{
                type: Boolean,
                default: false
            }
        },
        name: "UploadImage",
        data() {
            return {
                listObj: {},
                fileList: [],
                token: document.head.querySelector('meta[name="csrf-token"]'),
                $viewer: null
            }
        },
        computed: {
            images() {
                return this.fileList.map(v => v.url)
            },
            widthAndHeight() {
                return {
                    width: this.width,
                    height: this.height
                }
            },
            showSingle() {
                return !this.multiple && this.fileList.length > 0
            },
            emitInput() {
                if (this.multiple) {
                    return this.fileList.map(v => v.id)
                } else {
                    return _.get(this.fileList, '[0].id')
                }
            },
            // 是否已经达到最大上传图片量
            isMax() {
                return Number.isInteger(this.max) && this.fileList.length >= this.max
            },
            // 展示上传的功能
            showUpload() {
                return !this.disabled && !this.isMax
            },
            showImageBox () {
                return this.multiple || (this.disabled)
            }
        },
        watch: {
            fileList(val) {
                if (this.multiple) {
                    if (!_.isEqual(this.emitInput, this.value)) {
                        this.$emit('input', this.emitInput)
                    }
                    this.$emit('change', val)
                } else {
                    if (!_.isEqual(this.emitInput, this.value)) {
                        this.$emit('input', this.emitInput)
                    }
                    this.$emit('change', _.get(val, '[0]'))
                }
            },
            value: {
                handler(val, oldVal) {
                    this.updateFileList()
                },
                immediate: true
            }
        },
        methods: {
            // 初始化
            inited(viewer) {
                this.$viewer = viewer
            },
            // 预览
            showImage(index) {
                if (this.$viewer) {
                    this.$viewer.view(index)
                }
            },
            // 移除图片
            del(index) {
                this.fileList.splice(index, 1)
            },
            handleSuccess(response, file) {
                if (this.multiple) {
                    if (this.isMax) {
                        return
                    }
                    this.fileList.push(response)
                } else {
                    this.fileList = [response]
                }
                console.log(this.fileList)
            },
            // 设置图片
            setFileList(list) {
                if (Array.isArray(list)) {
                    this.fileList = list
                }
            },
            handleRemove(file) {
                const uid = file.uid
                const objKeyArr = Object.keys(this.listObj)
                for (let i = 0, len = objKeyArr.length; i < len; i++) {
                    if (this.listObj[objKeyArr[i]].uid === uid) {
                        delete this.listObj[objKeyArr[i]]
                        return
                    }
                }
            },
            beforeUpload(file) {
                let size = _.get(file, 'size')
                let size_m = size / (1024 * 1024)
                if (size_m > this.singleFileSize) {
                    helper.alert('最多上传' + this.singleFileSize + 'M的图片')
                    return false
                }
                if (this.typeAllow.indexOf(file.type) < 0) {
                    helper.alert(`${file.type}文件类型不允许`)
                    return false
                }
                if (this.isMax) {
                    helper.alert(`最多上传${this.max}张图片`)
                    return false
                }
            },
            updateFileList() {
                if (!this.enableFileInfo) {
                    return
                }
                let val = this.value
                if (_.isEqual(val, this.emitInput)) {
                    return
                }
                ajax.get('/admin/file/file-info-by-id', {id: val}).then(response => {
                    if (Array.isArray(response)) {
                        this.fileList = response
                    } else {
                        this.fileList = [response]
                    }
                })
            }
        }
    }
</script>
<style lang="scss">
    .upload-image-com {
        .el-upload {
            display: table;
            width: 100%;
            height: 100%;

            .el-upload-dragger {
                display: table-cell;
                vertical-align: middle;
                width: inherit;
                height: inherit;

                .el-icon-upload {
                    margin: 0;
                }
            }
        }

        .el-upload-dragger-b-none {
            .el-upload-dragger {
                border: none;
            }
        }
    }
</style>
