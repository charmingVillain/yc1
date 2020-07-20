<template>
    <el-form v-loading="loading" ref="goodsForm" :model="model" label-width="120px" class="goods-create-edit">
        <div class="row">
            <div class="col-12">
                <j-card title="基础信息">
                    <div class="col-6">
                        <el-form-item label="模版ID" prop="templates_id" label-width="120px">
                            <el-input v-model="model.templates_id"></el-input>
                        </el-form-item>
                    </div>
                    <div class="col-6">
                        <el-form-item label="标题" prop="title" label-width="120px">
                            <el-input v-model="model.title"></el-input>
                        </el-form-item>
                    </div>

                    <div class="col-6">
                        <el-form-item label="内容 " prop="content" label-width="120px">
                            <el-input v-model="model.content"></el-input>
                        </el-form-item>
                    </div>
                    <div class="col-6">
                        <el-form-item label=" 备注" prop="remark" label-width="120px">
                            <el-input v-model="model.remark"></el-input>
                        </el-form-item>
                    </div>
                    <div class="col-6">
                        <el-form-item label="链接" prop="url" label-width="120px">
                            <el-input v-model="model.url"></el-input>
                        </el-form-item>
                    </div>
                </j-card>
            </div>
            <div class="col-12">
                <j-card title="图文描述">
                    <el-form-item prop="file_id" label="图文信息">
                        <upload-image :multiple="false" v-model="model.file_id"></upload-image>
                    </el-form-item>

                </j-card>
            </div>

            <div class="col-12">
                <j-card>
                    <el-button :loading="submitLoading"   @click="submitForm('goodsForm')"
                               type="primary">保存修改
                    </el-button>
                    <el-button @click="resetForm('goodsForm')">重置</el-button>
                </j-card>
            </div>

        </div>
    </el-form>
</template>

<script>

    export default {
        name: "Create",
        data() {
            return {
                activeName: '',
                model: {
                    title: '',
                    content: '',
                    remark: "",
                    url: "",
                    file_id: 0,
                    templates_id: '',
                },
                loading: false,
                submitLoading: false,
                id: this.$route.params.id,
                validator: validator,
            }
        },
        created() {
            this.fetch()
        },
        methods: {
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
                    let file = _.get(response, 'file')
                    if (file) {
                        this.model.file_id = _.get(file, 'id')
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
