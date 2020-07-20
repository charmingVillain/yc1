<template>
    <j-dialog :visible.sync="innerVisible" :title="header">

        <div v-loading="loading">
            <el-checkbox-group v-model="model.roles">

                <el-tooltip v-for="role in roles" :key="role.id" :content="role.description" placement="top">
                    <el-checkbox :label="role.id">
                        {{ role.name }}
                    </el-checkbox>
                </el-tooltip>
            </el-checkbox-group>
        </div>


        <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click.prevent="submit">
                确定
            </el-button>
            <el-button @click="innerVisible = false">关闭</el-button>
        </span>
    </j-dialog>
</template>

<script>
    export default {
        name: "AddressProvinceAndCity",
        props: {
            header: {
                type: String,
                default: '角色分配'
            },
            visible: {
                type: Boolean,
                default: false
            },
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                loading: false,
                innerVisible: this.visible,
                roles: [],
                model: {
                    roles: []
                }
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
                    this.fetch()
                    this.bindData()
                }
            }
        },
        computed: {},
        methods: {
            fetch() {
                if (this.roles.length > 0) {
                    return Promise.resolve(this.roles)
                }
                this.loading = true
                return ajax.get(helper.bind_str(routeConfig.roles, {id: this.user.id})).then(response => {
                    if (Array.isArray(response)) {
                        this.roles = _.get(response, 'roles')
                        this.all = _.get(response, 'all')
                    }
                    return this.roles
                }).finally(() => {
                    this.loading = false
                })
            },

            submit() {
                let data = this.model
                data.user_id = this.user.id
                ajax.put(routeConfig.setRoles, data).then(response => {
                    this.$emit('refresh', true)
                    this.innerVisible = false
                    helper.alert('设置成功', {type: 'success'})
                })
            },
            bindData() {
                this.$nextTick(() => {
                    if (Array.isArray(this.user.roles)) {
                        this.model.roles = this.user.roles.map(v => v.id)
                    }
                })
            }
        }
    }
</script>
