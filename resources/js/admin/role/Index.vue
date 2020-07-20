<template>
    <div class="row">

        <div class="col-12">
            <j-card title="搜索">
                <el-form ref="elForm" :inline="true" :model="formInline">
                    <el-form-item prop="name">
                        <el-input v-model="formInline.name" placeholder="角色名称"></el-input>
                    </el-form-item>
                    <el-form-item prop="guard_name">
                        <role-guard-select v-model="formInline.guard_name" placeholder="保护对象"></role-guard-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button :loading="searchLoading" type="primary" @click.prevent="search" native-type='submit'>
                            搜索
                        </el-button>
                    </el-form-item>
                    <el-form-item>
                        <el-button @click.prevent="reset">重置</el-button>
                    </el-form-item>
                </el-form>
            </j-card>
        </div>

        <div class="col-12">
            <j-card class="card shadow mb-4" title="角色管理">
                <template slot="tools">
                    <create-dialog :item="item" @refresh="refresh" :visible.sync="showCreateModal"></create-dialog>
                    <el-button type="primary" @click.prevent="showCreateModalFunc">新增</el-button>
                </template>
                <j-table ref="JTable" @selection-change="handleSelectionChange" :url="url">
                    <el-table-column
                            prop="id"
                            label="编号"
                            width="50">
                    </el-table-column>
                    <el-table-column
                            prop="name"
                            label="角色名"
                            width="180">
                    </el-table-column>
                    <el-table-column
                            prop="guard_name_text"
                            label="保护对象"
                            width="180">
                    </el-table-column>
                    <el-table-column
                            prop="desc"
                            label="描述"
                            min-width="200"
                    >
                    </el-table-column>
                    <el-table-column
                            prop="created_at"
                            label="创建日期"
                            width="180">
                    </el-table-column>
                    <el-table-column
                            prop="updated_at"
                            label="修改日期"
                            width="180">
                    </el-table-column>
                    <el-table-column
                            label="操作"
                            width="200">
                        <template slot-scope="scope">
                            <el-button title="编辑" @click.prevent="showEditModal(scope.row)" type="primary"
                                       icon="el-icon-edit" size="mini"></el-button>

                            <el-button v-if="scope.row.guard_name == 'admin' || scope.row.guard_name == 'shop_member'"
                                       title="菜单权限"
                                       @click.prevent="menu(scope.row)" type="info"
                                       icon="el-icon-s-check" size="mini"></el-button>
                        </template>
                    </el-table-column>
                </j-table>
            </j-card>
        </div>


    </div>
</template>

<script>
    import CreateDialog from './components/CreateDialog'
    import RoleGuardSelect from '@/components/RoleGuardSelect'
    export default {
        name: "Index",
        components: {
            CreateDialog,
            RoleGuardSelect
        },
        data() {
            return {
                formInline: {
                    name: '',
                    guard_name: '',
                },
                item: {},
                searchLoading: false,
                createLoading: false,
                showCreateModal: false,
                url: routeConfig.index,
            }
        },
        created() {
        },
        methods: {
            alpha_dash: validator.alpha_dash,
            // 搜索
            search: function () {
                var autoPage = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
                this.searchLoading = true
                var that = this
                this.$refs.JTable.search(this.formInline, autoPage).finally(function () {
                    that.searchLoading = false
                })
            },
            // 刷新 要取总页数, 不重新定位到第一页
            refresh() {
                this.searchLoading = true
                this.$refs.JTable.refresh().finally(() => {
                    this.searchLoading = false
                })
            },
            // 重置
            reset: function () {
                this.$refs.elForm.resetFields()
            },
            // 新增展示
            showCreateModalFunc() {
                this.item = {}
                this.showCreateModal = true
            },
            // 编辑展示
            showEditModal(row) {
                this.item = row
                this.showCreateModal = true
            },
            // 删除
            del: function (row) {
                var that = this
                helper.confirm('确定要删除此角色').then(function () {
                    ajax.delete(helper.bind_str(routeConfig.destroy, {id: row.id})).then(function () {
                        helper.alert('删除成功', {type: 'success'})
                        that.refresh()
                    })
                })
            },
            menu(row) {
                this.$router.push({name: 'Menu', params: {id: row.id, guard_name: row.guard_name}})
            },
            handleSelectionChange(rows) {
                console.log(rows)
            }
        }
    }
</script>
