<template>
    <div class="row">
        <div class="col-xs-12 col-lg-12">
            <j-card title="模版管理">

                <template slot="tools">
                    <el-button type="primary" @click.prevent="create">新增</el-button>
                </template>
                <j-table ref="JTable" :url="routeConfig.index" :page-size="10" :page-sizes="[10,20, 50, 100, 200, 500]">
                    <el-table-column prop="title" label="标题" sortable></el-table-column>
                    <el-table-column prop="templates_id" label="模版id" sortable></el-table-column>
                    <el-table-column prop="content" label="内容" sortable></el-table-column>
                    <el-table-column prop="remark" label="备注" sortable></el-table-column>
                    <el-table-column label="图片">
                        <template slot-scope="scope">
                            <viewer>
                                <img style="width: 100px; height: 100px" :src="scope.row.file.url"/>
                            </viewer>
                            <span v-if="scope.row.file == null">暂无图标</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" header-align="center">
                        <template slot-scope="scope">
                            <div>
                                <el-button type="primary" size="mini" @click="edit(scope.row)">编辑</el-button>
                            </div>
                            <div>
                                <el-button type="danger" size="mini" @click="del(scope.row)">删除</el-button>
                            </div>
                        </template>
                    </el-table-column>
                </j-table>

            </j-card>
        </div>

    </div>
</template>

<script>
    export default {
        name: "Index",
        components: {},
        data() {
            return {
                loading: false,
                item: {},
                routeConfig: routeConfig,
                searchLoading: false,
                search: {
                    name: '',
                },
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
            //单个商品删除
            del(row) {
                helper.confirm('确定要删除此模版？').then(_ => {
                    ajax.delete(helper.bind_str(routeConfig.destroy, {id: row.id})).then(response => {
                        helper.alert('删除成功', {type: 'success'})
                        this.fetch()
                    }).finally(() => {
                        this.loading = false
                    })
                })
            },

            // 新增
            create() {
                this.$router.push({name: 'Create'})
            },
            edit(row) {
                this.$router.push({name: 'Edit', params: {id: row.id}})
            },
            reset() {
                this.$refs.elForm.resetFields()
            },
        }
    }
</script>

<style>
    .item {
        margin-top: 10px;
        margin-right: 40px;
    }
</style>
