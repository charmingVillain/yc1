<template>
    <j-dialog :visible.sync="innerVisible" :title="header">

        <el-form ref="createForm" :model="model" label-width="80px">
            <el-form-item
                    :rules="[
                        {required: true, message: '角色名称必须', trigger: 'blur'},
                        {validator: alpha_dash, message: '角色名称只能是字母和数字-_', trigger: 'blur'},
                        {min: 2, max: 100, message: '角色名称字数在2~100之间', trigger: 'blur'},
                    ]"
                    prop="name"
                    label="角色名称">
                <el-input v-model="model.name"></el-input>
            </el-form-item>

            <el-form-item
                    :rules="[
                        {required: true, message: '保护对象必须', trigger: 'blur'},
                        {validator: alpha_dash, message: '保护对象只能是字母和数字-_', trigger: 'blur'},
                        {min: 2, max: 100, message: '保护对象字数在2~100之间', trigger: 'blur'},
                    ]"
                    prop="guard_name"
                    label="保护对象">
                <role-guard-select v-model="model.guard_name"></role-guard-select>
            </el-form-item>

            <el-form-item
                    :rules="[
                        {required: true, message: '描述必须', trigger: 'blur'},
                        {min: 2, max: 200, message: '描述字数最大200', trigger: 'blur'},
                    ]"
                    prop="desc" label="描述">
                <el-input v-model="model.desc"></el-input>
            </el-form-item>

        </el-form>

        <span slot="footer" class="dialog-footer">
            <el-button @click.prevent="innerVisible = false">取 消</el-button>
            <el-button type="primary" :loading="createLoading" @click="submit">确 定</el-button>
        </span>
    </j-dialog>


</template>

<script>
    import RoleGuardSelect from '@/components/RoleGuardSelect'

    export default {
        name: "createDialog",
        components: {
            RoleGuardSelect
        },
        props: {
            visible: {
                type: Boolean,
                default: false
            },
            item: {
                type: Object,
                default: {}
            }
        },
        data() {
            return {
                loading: false,
                innerVisible: this.visible,
                model: {
                    id: '',
                    name: '',
                    guard_name: '',
                    desc: '',
                },
                createLoading: false,

                options: []
            }
        },
        watch: {
            innerVisible(val) {
                this.$emit('update:visible', val)
            },
            visible(val) {
                if (val !== this.innerVisible) {
                    this.innerVisible = val
                    this.resetFields()
                }
                if (val) {
                    this.bindData()
                }
            }
        },
        computed: {
            header() {
                return this.isEdit ? '角色新增' : '角色编辑'
            },
        },
        methods: {
            alpha_dash: validator.alpha_dash,
            submit() {
                this.$refs.createForm.validate(result => {
                    if (!result) {
                        return
                    }
                    let data = this.model
                    if (data.id) {
                        this.createLoading = true
                        ajax.put(helper.bind_str(routeConfig.update, {id: data.id}), data).then(response => {
                            helper.alert('修改成功', {type: 'success'})
                            this.$emit('refresh', true)
                            this.innerVisible = false
                        }).finally(() => {
                            this.createLoading = false
                        })
                    } else {
                        this.createLoading = true
                        ajax.post(routeConfig.store, data).then(response => {
                            helper.alert('新增成功', {type: 'success'})
                            this.$emit('refresh', true)
                            this.innerVisible = false
                        }).finally(() => {
                            this.resetFields()
                            this.createLoading = false
                        })
                    }
                })

            },
            bindData() {
                this.$nextTick(() => {
                    this.model.id = _.get(this.item, 'id')
                    this.model.guard_name = _.get(this.item, 'guard_name')
                    this.model.name = _.get(this.item, 'name')
                    this.model.desc = _.get(this.item, 'desc')
                })
            },
            resetFields() {
                if (this.$refs.createForm) {
                    this.$refs.createForm.resetFields()
                }
            }
        }
    }
</script>