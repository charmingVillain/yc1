<template>
    <div>
        <el-table
            :data="data"
            v-loading="loading"
            border
            :height="height"
            :default-expand-all="defaultExpandAll"
            @selection-change="handleSelectionChange"
            :row-class-name="rowClassName"
        >
            <slot></slot>
        </el-table>
        <div class="clearfix"></div>
        <div class="float-right mt-2">
            <j-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :total="total"
                :current-page="page"
                :page-sizes="pageSizes"
                :page-size="pageSize"

            >
            </j-pagination>
        </div>
    </div>
</template>

<script>
    export default {
        name: "JTable",
        props: {
            url: {
                type: String,
                required: true
            },
            params: {
                type: Object,
                default() {
                    return {}
                }
            },
            pageSizes: {
                type: Array,
                default() {
                    return [20, 50, 100, 200, 500]
                }
            },
            pageSize: {
                type: Number,
                default: 20
            },
            currentPage: {
                type: Number,
                default: 1
            },
            initLoading: {
                type: Boolean,
                default: true
            },
            rowClassName: {
                type: Function,
                default: () => {
                    return ''
                }
            },
            height: {
                type: [Number, String]
            },
            defaultExpandAll: {
                type: Boolean,
                default: false
            },
            history: {
                type: Boolean,
                default: true
            }
        },
        data() {
            let queryPage = 0;

            if (this.$route) {
                queryPage = parseInt(this.$route.query.page)
            }
            return {
                data: [],
                total: 0,
                innerParams: _.cloneDeep(this.params),
                innerPageSize: this.pageSize,
                page: queryPage || this.currentPage,
                loading: false,
                oldInnerParams: {}
            }
        },
        mounted() {
            if (this.initLoading) {
                this.fetchList()
            } else {
                this.$emit('mounted', true)
            }
        },
        methods: {
            // 获取数据
            fetchList(autoPage = true) {
                if (!this.url) {
                    throw Error('请求地址不存在')
                }
                this.innerParams.pageSize = this.innerPageSize
                this.innerParams.page = this.page
                let headers = {}

                // 这里是为了简化请求总数
                if (autoPage && this.isSameParamsExceptPage(this.innerParams)) {
                    headers['page-simple'] = true
                } else {
                    headers['page-simple'] = false
                }
                this.oldInnerParams = _.cloneDeep(this.innerParams)
                // 判断搜索条件有无变化 如何没有变化就不需要请求总页数
                this.loading = true
                let query = _.get(this.$route, 'query')
                return ajax.table(this.url, _.merge(query, this.innerParams), {headers}).then(response => {
                    if (Number.isInteger(response.total)) {
                        this.total = response.total
                        this.$emit('setTotal', this.total)
                    }
                    if (Array.isArray(response.data)) {
                        this.data = response.data
                    }
                    return {
                        total: this.total,
                        data: this.data
                    }
                }).finally(_ => {
                    this.loading = false
                })
            },
            // 判断除去page 是不是搜索参数都相同
            isSameParamsExceptPage(innerParams) {
                this.oldInnerParams.page = innerParams.page
                return _.isEqual(this.oldInnerParams, innerParams)
            },
            // 分页大小变化
            handleSizeChange(val) {
                this.innerPageSize = val
                this.fetchList()
                this.replaceUrl()
            },
            // 页数变化
            handleCurrentChange(val) {
                this.page = val
                this.fetchList()
                this.replaceUrl()
            },
            /**
             *
             * @param data Object 请求参数对象
             * @param autoPage Bool 自动计算是否需要分页
             * @returns {*}
             */
            search(data, autoPage = true) {
                this.innerParams = _.mergeWith(this.innerParams, data, (objValue, srcValue) => {
                    if (_.isArray(srcValue)) {
                        return srcValue
                    }
                })
                // 将搜索参数写入url
                this.page = 1
                this.replaceUrl()
                let p = this.fetchList(autoPage)
                return p
            },
            loadData(data, autoPage = true) {
                this.innerParams = _.mergeWith(this.innerParams, data, (objValue, srcValue) => {
                    if (_.isArray(srcValue)) {
                        return srcValue
                    }
                })
                // 将搜索参数写入url
                let p = this.fetchList(autoPage)
                this.replaceUrl()
                return p
            },
            replaceUrl() {
                if (this.$route) {
                    let query = helper.filterObj(this.innerParams)
                    query._time = moment().valueOf()
                    this.$router.push({path: this.$route.path, params: this.$route.params, query: query})
                }
            },
            /**
             * 刷新列表
             * @returns {*}
             */
            refresh() {
                return this.fetchList(false)
            },
            // 多选
            handleSelectionChange(rows) {
                this.$emit('selection-change', rows)
            }
        }
    }
</script>
