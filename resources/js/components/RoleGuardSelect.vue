<template>
    <el-select @focus="fetch" :disabled="disabled" :loading="loading" v-model="model" filterable placeholder="请选择">
        <el-option
                v-for="item in options"
                :key="item.value"
                :disabled="item.disabled"
                :label="item.text"
                :value="item.value">
        </el-option>
    </el-select>
</template>

<script>
    import {getRoleGuards} from '@/ajaxOptions'

    export default {
        name: "FreightConfigSelect",
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
                model: this.value,
                options: []
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
                    if (val) {
                        this.fetch()
                    }
                    this.model = val
                },
                immediate: true
            }
        },
        methods: {
            fetch() {
                this.loading = true
                return getRoleGuards().then(response => {
                    this.options = response
                }).finally(() => {
                    this.loading = false
                })
            }
        }
    }
</script>