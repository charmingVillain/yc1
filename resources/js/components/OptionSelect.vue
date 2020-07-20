<template>
    <el-select @focus="fetch" :loading="loading" v-model="model" @change="change" filterable clearable :placeholder="placeholder">
        <el-option
                v-for="item in computedOptions"
                :disabled="item.disabled"
                :key="item.value"
                :label="item.text"
                :value="item.value">
        </el-option>
    </el-select>
</template>

<script>
    export default {
        name: "DictionarySelect",
        props: {
            value: {
                type: [String, Number],
                default: ''
            },
            params: {
                type: Object,
                default: () => {
                    return {}
                }
            },
            placeholder: {
                type: String,
                default: '请选择'
            },
            allow: {
                type: [Array],
                default: () => {
                    return []
                }
            },
            method: {
                type: String,
                require: true
            }
        },
        data() {
            return {
                loading: false,
                model: this.value,
                options: []
            }
        },
        computed: {
            computedOptions() {
                if (Array.isArray(this.allow) && this.allow.length > 0) {
                    let t = _.cloneDeep(this.options).map(v => {
                        v.disabled = !this.allow.includes(v.value)
                        return v
                    })
                    return _.sortBy(t, 'disabled')
                }else {
                    return this.options
                }
            }
        },
        watch: {
            model(val) {
                if (val !== this.value) {
                    this.$emit('input', val)
                }
            },
            value: {
                handler(val, oldVal) {
                    if (val || val === 0) {
                        this.fetch()
                        this.model = val
                    } else {
                        this.model = ''
                    }
                },
                immediate: true
            }
        },
        methods: {
            fetch() {
                this.loading = true
                let P = ajaxOptions[this.method]
                if (!_.isFunction(P)) {
                    helper.alert('method 必须时 ajaxOptions 中的方法')
                    return
                }
                return P(this.params).then(response => {
                    this.options = response
                }).finally(() => {
                    this.loading = false
                })
            },
            change(val) {
                this.$emit('change', val)
            }
        }
    }
</script>
