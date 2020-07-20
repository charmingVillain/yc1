<template>
    <div class="row">

        <div class="col-12">
            <j-card title="搜索">
                <el-form ref="elForm" :inline="true" :model="search">
                    <el-form-item prop="name">
                        <el-input v-model="search.name" placeholder="成员账号"></el-input>
                    </el-form-item>

                    <el-form-item prop="nickname">
                        <el-input v-model="search.nickname" placeholder="姓名"></el-input>
                    </el-form-item>

                    <el-form-item prop="email">
                        <el-input v-model="search.email" placeholder="邮箱"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-button :loading="searchLoading" type="primary" @click.prevent="fetch()">搜索</el-button>
                    </el-form-item>

                    <el-form-item>
                        <el-button @click.prevent="reset">重置</el-button>
                    </el-form-item>
                </el-form>
            </j-card>
        </div>

        <div class="col-xs-12 col-lg-12">
            <j-card title="用户管理">

                <roles-dialog :user="item" @refresh="refresh" :visible.sync="rolesVisible"></roles-dialog>

                <template slot="tools">
                    <el-button type="primary" @click.prevent="toCreate">新增</el-button>
                </template>

                <j-table ref="JTable" :url="routeConfig.index">

                    <el-table-column
                            prop="id"
                            label="ID"
                    >
                    </el-table-column>

                    <el-table-column
                            prop="name"
                            label="账号"
                            min-width="150">
                    </el-table-column>

                    <el-table-column
                            prop="nickname"
                            label="姓名"
                            min-width="100">
                    </el-table-column>

                    <el-table-column
                            prop="email"
                            label="邮箱"
                            min-width="150">
                    </el-table-column>

                    <el-table-column
                        prop="role_text"
                        label="权限角色"
                        min-width="150">
                        <template slot-scope="scope">
                            <el-tag :key="item.id" v-for="item in scope.row.roles || []">{{ item.description }}</el-tag>
                        </template>
                    </el-table-column>

                    <el-table-column
                            prop="created_at"
                            label="新增时间"
                            min-width="180"
                    >
                    </el-table-column>

                    <el-table-column width="200px" label="操作">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    @click="handleEdit(scope.$index, scope.row)">编辑
                            </el-button>

                            <el-button
                                size="mini"
                                type="primary"
                                @click="setRole(scope.row)">分配角色
                            </el-button>

                        </template>
                    </el-table-column>

                </j-table>

            </j-card>
        </div>
    </div>
</template>

<script>
    import RolesDialog from './components/RolesDialog'

    export default {
        name: "Index",
        components: {
            RolesDialog
        },
        data() {
            return {
                loading: false,
                createVisible: false,
                item: {},
                routeConfig: routeConfig,
                searchLoading: false,
                search: {
                    name: '',
                    email: '',
                    department: '',
                    nickname: ''
                },
                rolesVisible: false
            }
        },
        created() {
        },
        methods: {
            fetch(autoPage = true) {
                if (this.$refs.JTable) {
                    this.searchLoading = true
                    this.$refs.JTable.search(this.search, autoPage).finally(() => {
                        this.searchLoading = false
                    })
                }
            },
            refresh() {
                this.fetch(false)
            },
            // 新增
            toCreate() {
                this.$router.push({name: 'Create'})
            },
            handleEdit(index, row) {
                this.$router.push({name: 'Edit', params: {id: row.id}})
            },
            handleDelete(index, row) {
                helper.confirm('确定要删除此菜单').then(_ => {
                    ajax.delete(helper.bind_str(routeConfig.destroy, {id: row.id})).then(response => {
                        helper.alert('删除成功', {type: 'success'})
                        this.fetch()
                    })
                })
            },
            reset() {
                this.$refs.elForm.resetFields()
            },
            changeStatus(row, status) {
                this.$set(row, 'statusDisabled', true)
                ajax.put(helper.bind_str(routeConfig.updateStatus, {id: row.id}), {status}).then(response => {
                    helper.alert('修改启用成功', {type: 'success'})
                }).finally(() => {
                    this.$set(row, 'statusDisabled', false)
                })
            },
            setRole(row) {
                this.item = row
                this.rolesVisible = true
            }
        }
    }
</script>
