<template>
    <j-dialog :visible.sync="innerVisible" :title="title">

        <el-form :model="model" ref="elForm" label-width="100px">
            <el-form-item label="名称" prop="name" :rules="[
            { required: true, message: '请输入名称', trigger: 'blur' },]"
            >
                <el-input v-model="model.name"></el-input>
            </el-form-item>

            <el-form-item label="排序" prop="sort" :rules="[
            { required: true, message: '请输入排序号', trigger: 'blur' },]"
            >
                <el-input v-model="model.sort" placeholder="数字越小排序越靠前"></el-input>
            </el-form-item>


            <el-form-item label="分类上级" prop="pid" v-if="level == 'two'">
                <parent-select v-model="model.pid" filterable></parent-select>
            </el-form-item>

            <el-form-item label="分类图片">
                <upload-image v-model="model.file_id" :multiple="false"></upload-image>
            </el-form-item>

        </el-form>


        <span slot="footer" class="dialog-footer">
            <el-button type="primary" :loading="loading" @click.prevent="submit">
                {{ id ? '保存' : '新增' }}
            </el-button>
            <el-button @click="innerVisible = false">关闭</el-button>
        </span>
    </j-dialog>
</template>

<script>
    import ParentSelect from './ParentSelect.vue'

    export default {
        name: "CreateDialog",
        components: {
            ParentSelect
        },
        props: {
            visible: {
                type: Boolean,
                default: false
            },
            level: {
                type: String,
                default: ''
            },
            item: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        created() {
            // this.platformLists()
        },
        data() {
            return {
                innerVisible: this.visible,
                html: '',
                loading: false,
                showParent: false,
                model: {
                    name: '',
                    pid: '',
                    sort: 1,
                    file_id: 0,
                },
                options: []
            }
        },
        computed: {
            id() {
                return _.get(this.item, 'id')
            },
            title() {
                return this.id ? '编辑分类' : '新增分类'
            }
        },
        watch: {
            innerVisible(val) {
                this.$emit('update:visible', val)
            },
            visible(val) {
                if (val !== this.innerVisible) {
                    this.innerVisible = val
                }
                if (val) {
                    this.bindEditData()
                }
            }
        },
        methods: {
            submit() {
                let model = _.cloneDeep(this.model)
                if (this.id) {
                    this.update()
                } else {
                    this.create()
                }
            },
            handleCheckedCitiesChange(value) {
                console.log(value,'---------');
            },
            create() {
                this.$refs.elForm.validate((result) => {
                    if (!result) {
                        return
                    }
                    let data = _.cloneDeep(this.model)
                    if (!data.pid) {
                        delete data.pid
                    }
                    this.loading = true
                    ajax.post(routeConfig.store, data).then(response => {
                        this.innerVisible = false
                        this.$emit('refresh', response)
                    }).finally(() => {
                        this.loading = false
                    })
                })
            },
            update() {
                this.$refs.elForm.validate((result) => {
                    if (!result) {
                        return
                    }
                    let data = this.model
                    this.loading = true
                    ajax.put(helper.bind_str(routeConfig.update, {id: this.id}), data).then(response => {
                        this.innerVisible = false
                        this.$emit('refresh', response)
                    }).finally(() => {
                        this.loading = false
                    })
                })
            },
            // 回填数据
            bindEditData() {
                this.$nextTick(() => {
                    if (this.id) {
                        this.showParent = true
                        this.model.name = _.get(this.item, 'name')
                        this.model.pid = _.get(this.item, 'pid')
                        this.model.sort = _.get(this.item, 'sort')
                        this.model.file_id = _.get(this.item, 'file_id')
                    } else {
                        this.showParent = false
                        this.$refs.elForm.resetFields()
                    }
                })
            }
        }
    }
</script>
