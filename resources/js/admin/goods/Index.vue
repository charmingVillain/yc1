<template>
    <div class="row">
        <div class="col-12">
            <j-card title="搜索">
                <el-form ref="elForm" :inline="true" :model="search">
                    <el-form-item prop="name">
                        <el-input v-model="search.name" placeholder="酒吧名称/酒吧编号"></el-input>
                    </el-form-item>

                    <el-form-item prop="goodsType">
                        <el-cascader v-model="search.goods_categories" placeholder="酒吧分类"
                                     :options="goodsCategories"
                                     :props="{ multiple: true, checkStrictly: true }"
                                     clearable></el-cascader>
                    </el-form-item>
                    <el-form-item>
                        <el-button :loading="searchLoading" type="primary" @click.prevent="fetch()"
                                   native-type='submit'> 搜索
                        </el-button>
                    </el-form-item>
                    <el-form-item>
                        <el-button @click.prevent="reset">重置</el-button>
                    </el-form-item>
                </el-form>
            </j-card>
        </div>
        <div class="col-xs-12 col-lg-12">
            <j-card title="酒吧管理">

                <template slot="tools">
                    <el-button type="primary" @click.prevent="create">新增</el-button>
                </template>

                <j-table ref="JTable" :url="routeConfig.index" :page-size="10" :page-sizes="[10,20, 50, 100, 200, 500]"
                         @selection-change="handleSelectionChange">
                    <!--                    <el-table-column type="selection" width="55"></el-table-column>-->
                    <el-table-column prop="goods_code" label="酒吧编号" sortable></el-table-column>
                    <el-table-column prop="goods_img" label="酒吧图片">
                        <template slot-scope="scope">
                            <viewer>
                                <img style="width: 100px; height: 100px" :src="scope.row.goods_img"/>
                            </viewer>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="酒吧名称" sortable></el-table-column>
                    <el-table-column label="酒吧分类" sortable>
                        <template slot-scope="scope">
                            <span>{{scope.row.goods_category.name}}</span>
                        </template>
                    </el-table-column>

                    <el-table-column prop="tags" label="标签" sortable>
                        <template slot-scope="scope">
                            <div v-for="item in scope.row.tags">
                                <span>{{item}}</span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="sales_price" label="销售价格" sortable></el-table-column>
                    <el-table-column prop="market_price" label="市场原价" sortable></el-table-column>

                    <el-table-column prop="sort" label="排序" sortable></el-table-column>
<!--                    <el-table-column prop="sales_number" label="销量" sortable></el-table-column>-->

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
                checkList: {},
                loading: false,
                createVisible: false,
                item: {},
                routeConfig: routeConfig,
                searchLoading: false,
                search: {
                    name: '',
                    goods_categories: '',
                },
                category: {},
                multipleSelection: [],
                statistics: [],
                messageType: '',
                errorsGoodsNames: [],
                handle: '',
                goodsCategories: [],
            }
        },
        created() {
            this.getGoodsCategories()
        },


        methods: {
            // 初始化
            // inited(viewer) {
            //     this.$viewer = viewer
            // },
            fetch(autoPage = true) {
                if (this.$refs.JTable) {
                    this.searchLoading = true
                    this.search.goods_category_ids = _.uniq(_.flatten(this.search.goods_categories))
                    this.$refs.JTable.search(this.search, autoPage).finally(() => {
                        this.searchLoading = false
                    })
                }
            },
            //单个商品删除
            del(row) {
                helper.confirm('确定要删除此商品？').then(_ => {
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
            handleSelectionChange(val) {
                this.multipleSelection = val;
            },
            reset() {
                this.$refs.elForm.resetFields()
            },
            getGoodsCategories() {
                ajax.get(routeConfig.goodsCategory).then(response => {
                    if (Array.isArray(response)) {
                        this.goodsCategories = _.cloneDeep(response).map(v => {
                            return {
                                value: v.id,
                                label: v.name,
                                pid: v.pid,
                            }
                        })
                    }
                    this.goodsCategories = helper.array_to_tree(this.goodsCategories, 'value', 'pid', 'children')
                }).finally(() => {
                    this.loading = false
                })
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
