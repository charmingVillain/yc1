<template>
    <el-form v-loading="loading" ref="goodsForm" :model="model" label-width="120px" class="goods-create-edit">
        <div class="row">
            <div class="col-12">
                <j-card title="基础信息">

                    <el-form-item label="酒吧分类" prop="goods_category_id" label-width="120px">
                        <goods-category-select v-model="model.goods_category_id"></goods-category-select>
                    </el-form-item>

                    <div class="col-6">
                        <el-form-item label="酒吧地址" prop="address" label-width="120px" :rules="[
                    { required : true , min: 2 , max : 64 , message: '酒吧地址称必须，并且长度大于等于2小于等于64',trigger: 'blur'},
                    ]">
                            <el-input v-model="model.address"></el-input>
                        </el-form-item>
                    </div>

                    <div class="col-6">
                        <el-form-item label="酒吧标题" prop="title" label-width="120px" :rules="[
                    { required : true , min: 2 , max : 64 , message: '酒吧标题必须，并且长度大于等于2小于等于64',trigger: 'blur'},
                    ]">
                            <el-input v-model="model.title"></el-input>
                        </el-form-item>
                    </div>
                    <div class="col-6">
                        <el-form-item label="酒吧名称" prop="name" label-width="120px" :rules="[
                    { required : true , min: 2 , max : 64 , message: '商品名称必须，并且长度大于等于2小于等于64',trigger: 'blur'},
                    ]">
                            <el-input v-model="model.name"></el-input>
                        </el-form-item>
                    </div>
                    <div class="col-6">
                        <el-form-item label="酒吧经度" prop="lng" label-width="120px" :rules="[
                    { required : true , min: 2 , max : 64 , message: '酒吧经度，并且长度大于等于2小于等于64',trigger: 'blur'},
                    ]">
                            <el-input v-model="model.lng"></el-input>
                        </el-form-item>
                    </div>
                    <div class="col-6">
                        <el-form-item label="酒吧纬度" prop="lat" label-width="120px" :rules="[
                    { required : true , min: 2 , max : 64 , message: '酒吧纬度，并且长度大于等于2小于等于64',trigger: 'blur'},
                    ]">
                            <el-input v-model="model.lat"></el-input>
                        </el-form-item>
                    </div>

                    <el-form-item label="排序" prop="sort">
                        <el-input-number v-model="model.sort" :min="1" :max="100"></el-input-number>
                    </el-form-item>
                    <el-form-item label="销售数量" prop="sales_number">
                        <el-input-number v-model="model.sales_number" :min="1" :max="100"></el-input-number>
                    </el-form-item>


                </j-card>
            </div>
            <div class="col-12">
                <j-card title="标签信息">
                    <div class="col-12">
                        <el-form-item prop="tags" label="商品标签" :rules="[
                            { required :true, message: '标签必须选择', trigger:'blur'}
                        ]">
                            <el-tag
                              :key="tag"
                              v-for="tag in model.tags"
                              closable
                              :disable-transitions="false"
                              @close="handleClose(tag)">
                              {{tag}}
                            </el-tag>
                            <el-input
                              class="input-new-tag"
                              v-if="inputVisible"
                              v-model="inputValue"
                              ref="saveTagInput"
                              size="small"
                              @keyup.enter.native="handleInputConfirm"
                              @blur="handleInputConfirm"
                            >
                            </el-input>
                            <el-button v-else class="button-new-tag" size="small" @click="showInput">+ 新标签</el-button>
                        </el-form-item>
                    </div>
                </j-card>
            </div>
            <div class="col-12">
                <j-card title="价格信息">
                    <div class="col-6">
                        <el-form-item label="销售价格"
                                      prop="sales_price"
                                      label-width="120px"
                                      :rules="[ { required : true, message : '成本价不能为空', trigger:'blur'},
                                  { validator : validator.isFormatFloat, message : '整数最大8位，小数最大两位', trigger:'blur'},
                                 ]">
                            <el-input v-model="model.sales_price"></el-input>
                        </el-form-item>
                    </div>
                    <div class="col-6">
                        <el-form-item label="市场原价"
                                      prop="market_price"
                                      label-width="120px"
                                      :rules="[
                                      { required : true, message : '成本价不能为空', trigger:'blur'},
                                    { validator : validator.isFormatFloat, message : '整数最大8位，小数最大两位', trigger:'blur'},
                              ]">
                            <el-input v-model="model.market_price"></el-input>
                        </el-form-item>
                    </div>
                </j-card>
            </div>

            <div class="col-12">
                <div>
                    <pre class="text-danger">

                    </pre>
                </div>
                <j-card title="图文描述">
                    <el-form-item prop="file_ids" label="图文信息" :rules="[
                        { required :true, message: '主图必须选择', trigger:'change'}
                    ]">
                        <upload-image :multiple="true" v-model="model.file_ids"></upload-image>
                    </el-form-item>

                    <el-form-item prop="detail" label="商品详情" :rules="[
                        { required:true,message:'图文信息不能为空',trigger:'blur'}
                    ]">
                        <tinymce v-model="model.detail"></tinymce>
                    </el-form-item>
                </j-card>
            </div>

            <div class="col-12">
                <j-card>
                    <el-button :loading="submitLoading" :disabled="isUpdatePrice" @click="submitForm('goodsForm')"
                               type="primary">保存修改
                    </el-button>
                    <el-button @click="resetForm('goodsForm')">重置</el-button>
                </j-card>
            </div>

        </div>
    </el-form>
</template>

<script>

    import Tinymce from '@/components/Tinymce'
    import GoodsCategorySelect from './GoodsCategorySelect.vue'

    export default {
        name: "Create",
        components: {
            Tinymce,
            GoodsCategorySelect
        },
        data() {
            return {
                activeName: '',
                model: {
                    detail: '',
                    file_ids: [],
                    name: "",
                    address: "",
                    lng: 0.00,
                    lat: 0.00,
                    sort: 1,
                    sales_price: 1,
                    market_price: 1,
                    sales_number: 1,
                    goods_category_id: '',
                    tags: []
                },
                updateAuditStatus: '',
                config: {},
                loading: false,
                submitLoading: false,
                id: this.$route.params.id,
                validator: validator,
                freightConfigCreateUrl: routeConfig.freightConfigCreateUrl,
                specificationsPriceDirty: false,
                inputVisible: false,
                inputValue: ''
            }
        },
        created() {
            this.fetch()
        },
        watch: {},
        computed: {
            //检查价格修改
            isUpdatePrice() {
                if (this.id && [3, 4].includes(this.updateAuditStatus)) {
                    let keys = ['supplier_price']
                    let currentValues = this.model.platforms.map(platform => {
                        let temp = {}
                        keys.forEach(key => {
                            temp[key] = parseFloat(_.get(platform, key))
                        })
                        return temp
                    })


                } else {
                    return false
                }
            },
        },
        methods: {
            handleClose(tag) {
                this.model.tags.splice(this.model.tags.indexOf(tag), 1);
            },
            showInput() {
                this.inputVisible = true;
                this.$nextTick(_ => {
                    this.$refs.saveTagInput.$refs.input.focus();
                });
            },
            handleInputConfirm() {
                let inputValue = this.inputValue;
                console.log(inputValue)
                if (inputValue) {
                    this.model.tags.push(inputValue);
                }
                this.inputVisible = false;
                this.inputValue = '';
            },
            fetch() {
                this.loading = true
                let p = []

                if (this.id) {
                    p[1] = ajax.get(helper.bind_str(routeConfig.show, {id: this.id})).then(response => {
                        this.bindEditData(response)
                    })
                }

                Promise.all(p).finally(() => {
                    this.loading = false
                })
            },
            //提交表单
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (!valid) {
                        return
                    }
                    this.submitLoading = true
                    let data = _.cloneDeep(this.model)
                    let p = Promise.resolve(true)
                    p.then(() => {
                        this.submit(data)
                    }).finally(() => {
                        this.submitLoading = false
                    })
                });

            },
            //提交数据
            submit(data) {
                if (this.id) { // 编辑功能
                    this.update(this.id, data).then(response => {
                        helper.confirm('保存成功', undefined, {type: 'success'}).then(() => {
                            this.$router.push({name: 'Index'})
                        })
                    }).finally(() => {
                        this.submitLoading = false
                    }).catch(() => {
                        this.submitLoading = false
                    })
                } else {
                    // 新增时 默认给 售价|供应商零售价 设置为供应商成本价， 后台可用修改售价
                    ajax.post(routeConfig.store, data).then(response => {
                        helper.confirm('新增成功', undefined, {type: 'success'}).then(() => {
                            this.$router.push({name: 'Index'})
                        })
                    }).finally(() => {
                        this.submitLoading = false
                    })
                }
            },
            //绑定编辑数据
            bindEditData(response) {
                this.$nextTick(function () {
                    this.model = _.cloneDeep(response)
                    this.model.goods_category_id = _.get(response,'goods_category_id')
                    let files = _.get(response, 'files')
                    if (Array.isArray(files)) {
                        this.model.file_ids = files.map(v => v.id)
                    }
                })


            },
            //重置表单
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
            update(id, data) {
                return ajax.put(helper.bind_str(routeConfig.update, {id}), data)
            },

            isPriceDirty(value) {
                this.specificationsPriceDirty = value
            },
            getSupplierShop() {
                ajax.get(routeConfig.supplierShop).then(response => {
                    if (!response.id) {
                        helper.alert('此账户没有申请供应商,不能添加商品！');
                        this.$router.push({name: 'Index'})
                    }
                })
            },
            //获取商品类目平台
            checkSelectEnable(item, value) {
                if (item.checked && this.model.goods_category_id) {
                    this.getGoodsCategoryPlatforms().then(() => {
                        if (Array.isArray(this.platforms)) {
                            if (this.platforms.length > 0) {
                                let platforms = this.platforms.map(v => {
                                    return v.id;
                                })
                                // 如果时编辑，做编辑得赋值
                                if (!(platforms.includes(item.platform_id))) {
                                    this.$alert('该平台无此商品分类，请修改分类！', {type: 'error'})
                                    this.$set(item, 'checked', false)
                                } else {
                                    this.$set(item, 'checked', value)
                                }
                            } else {
                                this.$alert('该平台无此商品分类，请修改分类！', {type: 'error'})
                                this.$set(item, 'checked', false)
                            }
                        }
                    })

                }
            },
            //获取商品类目平台
            getGoodsCategoryPlatforms() {
                return new Promise((resolve, reject) => {
                    ajaxOptions.getGoodsCategory().then(response => {
                        this.platforms = _.compact(response.map(v => {
                            if (v.value == this.model.goods_category_id) {
                                return v.platform
                            }
                        }))[0]
                    }).finally(() => {
                        resolve(true)
                    })
                })

            }
        }
    }
</script>

<style>
    .platform-price .el-tabs__nav .is-top {
        line-height: 0px;
    }
    .el-tag + .el-tag {
        margin-left: 10px;
    }
    .button-new-tag {
        margin-left: 10px;
        height: 32px;
        line-height: 30px;
        padding-top: 0;
        padding-bottom: 0;
    }
    .input-new-tag {
        width: 90px;
        margin-left: 10px;
        vertical-align: bottom;
    }
</style>
