<template>
    <div>
        <el-cascader @focus="fetch"
                     :disabled="disabled"
                     :loading="loading"
                     v-model="model"
                     clearable
                     filterable placeholder="请选择"
                     :options="options"
        ></el-cascader>
    </div>

</template>

<script>
    export default {
        name: "GoodsCategorySelect",
        props: {
            value: {
                type: [String, Number],
                default: ''
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                loading: false,
                model: [],
                list: [],
                routeConfig: routeConfig,
            }
        },
        computed: {
            options() {
                return helper.array_to_tree(this.list, 'value', 'pid', 'children')
            },
            lastId() {
                return _.last(this.model)
            }
        },
        watch: {
            lastId(val) {
                if (val !== this.value) {
                    this.$emit('input', val)
                }
            },
            value: {
                handler(val, oldVal) {
                    let isEqual = _.isEqual(val, this.lastId)
                    if (val && !isEqual) {
                        // this.fetch().then(list => {
                        //     this.setModel(list, val)
                        // })

                    } else if(_.isEmpty(val) && !isEqual) {
                        this.model = []
                    }
                },
                immediate: true
            }
        },
        methods: {
            fetch() {
                if (this.list.length) {
                    return Promise.resolve(this.list)
                }
                this.loading = true
                ajax.get(routeConfig.goodsCategory).then(response => {
                    if (Array.isArray(response)) {
                        this.list = response.map(v => {
                            return {
                                value: v.id,
                                label: v.name,
                                pid: v.pid,
                            }
                        })

                        this.list=helper.array_to_tree(this.list,'value','pid','children')

                    }

                }).finally(() => {
                    this.loading = false
                })
            },
            setModel(list, id) {
                let arrayPk = helper.array_pk(list, 'id')
                this.model = this.reModel(arrayPk, id)
            },
            reModel(pkList, id) {
                let temp = [id]
                let item = _.get(pkList, id)
                let pid = _.get(item, 'pid')
                if (pid) {
                    return this.reModel(pkList, pid).concat(temp)
                } else {
                    return temp
                }
            }
        }
    }
</script>
