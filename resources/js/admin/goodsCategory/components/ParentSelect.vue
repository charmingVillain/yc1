<template>
    <el-select @focus="focus" v-model="innerValue" filterable placeholder="请选择">
        <el-option
                v-for="item in options"
                :key="item.id"
                :label="item.name"
                :value="item.id">
        </el-option>
    </el-select>
</template>

<script>
    export default {
        name: "ParentSelect",
        props: {
            value: {
                type: [String, Number]
            }
        },
        data() {
            return {
                options: [],
                innerValue: ''
            }
        },
        watch: {
            innerValue(val, oldVal) {
                if (val !== this.value) {
                    this.$emit('input', val)
                }
            },
            value(val, oldVal) {
                if (val || val === 0) {
                    this.fetch().then(() => {
                        this.innerValue = parseInt(val)
                    })
                }
            }
        },
        methods: {
            focus() {
                this.fetch()
            },
            fetch() {
                this.loading = true
                return ajax.get(routeConfig.parentZero).then(response => {
                    if (Array.isArray(response)) {
                        this.options = response
                    }
                }).finally(() => {
                    this.loading = false
                })
            }
        }
    }
</script>
