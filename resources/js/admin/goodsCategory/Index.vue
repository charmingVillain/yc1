<template>
    <div class="row">
        <div class="col-xs-12 col-lg-12">
            <j-card title="商品分类">
                <template slot="tools">
                    <create-dialog @refresh="refresh" :item="item" :visible.sync="createVisible" :level="levelVisible"></create-dialog>
                    <el-button type="primary" @click.prevent="showCreateModel">新增</el-button>
                </template>
                <el-table :data="tableData" border  v-loading="loading" row-key="id">
                    <el-table-column
                        prop="id"
                        label="ID"
                        sortable>
                    </el-table-column>
                    <el-table-column
                        prop="name"
                        label="名称"
                        sortable>
                    </el-table-column>

                    <el-table-column  prop="file.url" label="图标">
                        <template slot-scope="scope">
                            <div  v-if="scope.row.file != null">
                                <viewer>
                                    <img :src="scope.row.file.url" style="height: 5rem;width: 5rem;">
                                </viewer>
                            </div>
                            <span v-if="scope.row.file == null">暂无图标</span>
                        </template>
                    </el-table-column>

                    <el-table-column
                        label="操作"
                        width="480">
                        <template slot-scope="scope">
                            <el-button title="新增" type="primary" icon="el-icon-plus" size="mini"
                                       v-if="scope.row.pid == '0'"
                                       @click="showCreateModel(scope.$index, scope.row,'two')"> 新增
                            </el-button>
                            <el-button title="编辑" type="primary" icon="el-icon-edit" size="mini"
                                       @click="showEditModal(scope.$index, scope.row)">编辑
                            </el-button>
                            <el-button title="删除" type="danger" icon="el-icon-delete" size="mini"
                                       @click="del(scope.$index, scope.row)">删除
                            </el-button>
                        </template>
                    </el-table-column>

                </el-table>
            </j-card>
        </div>
    </div>
</template>

<script>
    import CreateDialog from './components/CreateDialog.vue'

    export default {
        name: "Index",
        components: {
            CreateDialog
        },
        data() {
            return {
                routeConfig: routeConfig,
                tableData: [],
                loading: false,
                createVisible: false,
                levelVisible:'one',
                item: {}
            }
        },
        created() {
            this.fetch()
        },
        methods: {
            // 刷新
            refresh() {
                this.fetch()
            },
            fetch() {
                this.loading = true
                ajax.get(routeConfig.index).then(response => {
                    if (Array.isArray(response)) {
                        // arr, pk = 'id', pid = 'pid', child = '_child', root = 0, toString = false
                        this.tableData = helper.array_to_tree(response, undefined, undefined, 'children', 0, true)
                    }
                }).finally(() => {
                    this.loading = false
                })
            },
            // 修改  加载 form表单
            showEditModal(index, data) {
                if (data.pid == 0){
                    this.levelVisible = 'one'
                }else {
                    this.levelVisible = 'two'
                }
                this.item = data
                this.createVisible = true
            },
            // 删除
            del(index, row) {
                console.log(helper.bind_str(routeConfig.destroy, {id: row.id}))
                helper.confirm('确定要删除此菜单').then(_ => {
                    ajax.delete(helper.bind_str(routeConfig.destroy, {id: row.id})).then(response => {
                        helper.alert('删除成功', {type: 'success'})
                        this.refresh()
                    }).finally(() => {
                        this.loading = false
                    })
                })
            },
            // 展示
            showCreateModel(index, row,level='one') {
                console.log(level,'levellevel')
                this.item = {}
                this.levelVisible = level
                this.createVisible = true
            },
        }
    }
</script>
