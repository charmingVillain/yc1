<template>
    <div class="row" v-loading="loading">

        <div class="col-12">
            <j-card :title="role.name + ' 的菜单权限'">

                <div class="col-12 text-warning mb-2">
                    角色描述: {{ role.description }}
                </div>

                <el-tree
                    ref="elTree"
                    :data="data"
                    show-checkbox
                    node-key="id"
                    :default-checked-keys="defaultCheckedKeys"
                >
                </el-tree>
            </j-card>
        </div>

        <div class="col-12">
            <j-card>
                <el-button type="primary" :loading="submitLoading" @click="submit">
                    修改菜单权限
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
                    re_password: ''
                },
                response: {},
                loading: false,
                submitLoading: false,
                validator: validator
            }
        },
        computed: {
            data() {
                let all = _.cloneDeep(this.all)
                if (Array.isArray(all)) {
                    return helper.array_to_tree(all.map(v => {
                        v.label = v.name
                        v.text = v.name
                        v.value = v.id
                        return v
                    }), 'id', 'pid', 'children', '0', true)
                }
            },
            all() {
                return _.get(this.response, 'all')
            },
            // 默认选择的数据
            defaultCheckedKeys() {
                let temp = _.get(this.response, 'checked_ids')
                if (Array.isArray(temp)) { // 过滤掉有
                    let groupBy = _.uniq(_.map(this.all, 'pid'))
                    return temp.filter(v => groupBy.indexOf(v) < 0)
                }
                return []
            },
            role() {
                return _.get(this.response, 'role') || {}
            }
        },
        created() {
            this.fetch()
        },
        methods: {
            fetch() {
                if (this.id) {
                    this.loading = true
                    ajax.get(helper.bind_str(routeConfig.menu, {id: this.id})).then(response => {
                        this.response = response
                    }).finally(() => {
                        this.loading = false
                    })
                }
            },
            submit() {
                this.submitLoading = true
                let checked_nodes = this.$refs.elTree.getCheckedNodes(false, true)
                if (Array.isArray(checked_nodes)) {
                    let data = {
                        ids: checked_nodes.map(v => v.id)
                    }
                    ajax.put(helper.bind_str(routeConfig.updateMenu, {id: this.id}), data).then(response => {
                        helper.confirm('设置成功', undefined, {
                            type: 'success'
                        })
                    }).finally(() => {
                        this.submitLoading = false
                    })
                }
            }
        }
    }
</script>
