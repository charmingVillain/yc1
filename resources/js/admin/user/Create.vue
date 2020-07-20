<template>
    <div class="row" v-loading="loading">
        <div class="col-12">
            <j-card :title="title">
                <el-form :model="model" ref="ruleForm" label-width="120px">

                    <el-form-item
                            label="成员账号"
                            prop="name"
                            :rules="nameRules"
                    >
                        <span v-if="isEdit"> {{ model.name }}</span>
                        <el-input v-else v-model="model.name"></el-input>
                    </el-form-item>

                    <el-form-item
                            label="邮箱"
                            prop="email"
                            :rules="emailRules"
                    >
                        <span v-if="isEdit"> {{ model.email }}</span>
                        <el-input v-else v-model="model.email"></el-input>
                    </el-form-item>


                    <el-form-item
                            label="姓名"
                            prop="nickname"
                            :rules="[
                                {required: true, message: '姓名必须', trigger: 'blur'}
                            ]"
                    >
                        <el-input v-model="model.nickname"></el-input>
                    </el-form-item>

                    <el-form-item
                            label="密码"
                            prop="password"
                            :rules="passwordRules"
                    >
                        <el-input type="password" v-model="model.password"></el-input>
                    </el-form-item>

                    <el-form-item
                            v-if="!isEdit"
                            label="重复密码"
                            prop="re_password"
                            :rules="[
                                {required: true, message: '重复密码必须', trigger: 'blur'},
                                {validator: validator.same, value: model.password, message: '密码不相同', trigger: 'blur'}
                            ]"
                    >
                        <el-input type="password" v-model="model.re_password"></el-input>
                    </el-form-item>
                </el-form>
            </j-card>
        </div>

        <div class="col-12">
            <j-card>
                <el-button type="primary" :loading="submitLoading" @click="submitForm('ruleForm')">
                    {{ isEdit ? '修改' : '立即创建' }}
                </el-button>
            </j-card>
        </div>
    </div>
</template>
<script>

    export default {
        name: 'Create',
        data() {
            return {
                id: this.$route.params.id,
                model: {
                    email: '',
                    name: '',
                    nickname: '',
                    password: '',
                    re_password: '',
                    phone: '',
                    role: '',
                    department: '',
                },
                loading: false,
                submitLoading: false,
                validator: validator
            }
        },
        computed: {
            isEdit() {
                return !!this.id
            },
            title() {
                return this.isEdit ? '编辑用户' : '新增用户'
            },
            nameRules() {
                let temp = [
                    {validator: validator.alpha_dash, message: '用户名必须是字母数字', trigger: 'blur'}
                ]
                if (!this.isEdit) {
                    temp.push({required: true, min: 2, max: 50, message: '用户名必须2~50位', trigger: 'blur'})
                }
                return temp
            },
            emailRules() {
                let temp = []
                if (!this.isEdit) {
                    temp.push({required: true, type: 'email', message: '邮箱必须，且是正确的邮箱', trigger: 'blur'})
                }
                return temp
            },
            phoneRules() {
                let temp = []
                if (!this.isEdit) {
                    temp.push({required: true, message: '联系电话必须', trigger: 'blur'})
                    temp.push({validator: validator.is_phone, message: '联系电话格式不正确', trigger: 'blur'})
                }
                return temp
            },
            passwordRules() {
                let temp = []
                if (!this.isEdit) {
                    temp.push({required: true, min: 8, max: 20, message: '密码必须8-20位', trigger: 'blur'})
                } else {
                    temp.push({min: 8, max: 20, message: '密码必须8-20位', trigger: 'blur'})
                }
                return temp
            }
        },
        created() {
            this.fetch()
        },
        methods: {
            fetch() {
                if (this.id) {
                    this.loading = true
                    ajax.get(helper.bind_str(routeConfig.show, {id: this.id})).then(response => {
                        this.bindModel(response)
                    }).finally(() => {
                        this.loading = false
                    })
                }
            },
            submitForm(form) {
                this.$refs[form].validate(result => {
                    if (!result) {
                        return
                    }
                    if (this.id) {
                        this.update(this.id)
                    } else {
                        this.store()
                    }
                })
            },
            bindModel(response) {
                this.model.name = _.get(response, 'name')
                this.model.email = _.get(response, 'email')
                this.model.nickname = _.get(response, 'nickname')
                this.model.role = _.get(response, 'role')
                this.model.department = _.get(response, 'department')
                this.model.phone = _.get(response, 'phone')
            },
            store() {
                this.submitLoading = true
                ajax.post(routeConfig.store, this.model).then(response => {
                    helper.confirm('新增成功').then(() => {
                        this.$router.push({name: 'Index'})
                    })
                }).finally(() => {
                    this.submitLoading = false
                })
            },
            update(id) {
                let temp = (admin_password = '') => {
                    this.submitLoading = true
                    let data = _.cloneDeep(this.model)
                    if (admin_password) {
                        data.admin_password = admin_password
                    }
                    ajax.put(helper.bind_str(routeConfig.update, {id}), data).then(response => {
                        helper.confirm('编辑成功').then(() => {
                            this.$router.push({name: 'Index'})
                        })
                    }).finally(() => {
                        this.submitLoading = false
                    })
                }

                if (this.model.password) {
                    this.$prompt('请输入管理员账号密码', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        inputPattern: /.{8,20}/,
                        inputType: 'password',
                        inputErrorMessage: '账号密码必须是8到20位'
                    }).then(({ value }) => {
                        temp(value)
                    })
                } else {
                    temp()
                }


            }
        }
    }
</script>
