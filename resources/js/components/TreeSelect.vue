<template>
    <div class="tree-select">
        <el-popover
                placement="bottom-start"
                width="500"
                trigger="manual"
                :disabled="disabled"
                @show="init"
                v-model="show"
                v-clickoutside="hidePopover"
                class="tree-select-popover"
                ref="popover"
        >
            <template slot="reference">
                <el-input @click.native="showPopover" readonly :placeholder="placeholder" :value="inputText">
        <span class="el-input__suffix" v-if="!disabled && clearable && inputText" @click.prevent.stop="clear"
              slot="suffix">
          <span class="el-input__suffix-inner">
            <i class="el-input__icon el-icon-circle-close el-input__clear"></i>
          </span>
        </span>
                </el-input>
            </template>

            <el-scrollbar
                    tag="ul"
                    wrap-class="el-select-dropdown__wrap"
                    view-class="el-select-dropdown__list"
                    ref="scrollbar"
            >
                <el-tree
                        :data="data"
                        node-key="value"
                        highlight-current
                        :props="props"
                        @node-click="nodeClick"
                        default-expand-all
                        ref="elTree"
                        :expand-on-click-node="false">
                </el-tree>
            </el-scrollbar>
        </el-popover>
    </div>
</template>
<script>
    import clickoutside from '@/directive/clickoutside'
    import Emitter from '@/emitter.js'
    import scrollIntoView from '@/scroll-into-view'

    export default {
        directives: {clickoutside},
        mixins: [Emitter],
        props: {
            disabled: {
                type: Boolean,
                default: false
            },
            placeholder: {
                type: String,
                default: '请选择'
            },
            clearable: {
                type: Boolean,
                default: false
            },
            data: {
                type: Array,
                default() {
                    return []
                }
            },
            props: {
                type: Object,
                default() {
                    return {label: 'text'}
                }
            },
            value: {
                type: [String, Number, Boolean],
                default: ''
            }
        },
        data() {
            return {
                model: '',
                show: false,
                nodeData: {},
                currentComponent: ''
            }
        },
        created() {
            if (this.value || this.value == 0) {
                this.watchValue(this.value)
            }
        },
        computed: {
            inputText() {
                return _.get(this.nodeData, 'text') || ''
            },
            innerValue() {
                return _.get(this.nodeData, 'value')
            }
        },
        watch: {
            innerValue(val, oldVal) {
                if (val === oldVal || val === this.value) {
                    return
                }
                this.$emit('input', val)
                this.$nextTick(_ => {
                    this.dispatch('ElFormItem', 'el.form.change', val)
                })
            },
            value: {
                handler(val, oldVal) {
                    if (val === oldVal || val === this.innerValue) {
                        return
                    }
                    this.watchValue(val)
                }
            },
            data(val, oldVal) { // tree数据比 value 慢的处理的选中处理
                if (this.value) {
                    if (this.$refs.elTree) {
                        this.$nextTick(() => {
                            this.$refs.elTree && this.$refs.elTree.setCurrentKey(this.value || null)
                            this.nodeData = _.get(this.$refs.elTree.getNode(this.value), 'data') || {}
                        })
                    }
                }
            }
        },
        methods: {
            showPopover() {
                this.show = true
            },
            hidePopover() {
                this.show = false
            },
            // 节点点击
            nodeClick(data, node, vueComponent) {
                this.currentComponent = vueComponent
                this.nodeData = data
                this.show = false
            },
            init() {
                this.$nextTick(() => {
                    // 滚动到选中的地方
                    if (this.$refs.elTree) {
                        const target = this.currentComponent ? this.currentComponent.$el : ''
                        const menu = this.$refs.scrollbar.$el.querySelector('.el-select-dropdown__wrap')
                        target && menu && scrollIntoView(menu, target)
                    }
                    this.$refs.scrollbar && this.$refs.scrollbar.handleScroll()
                })
            },
            clear() {
                this.nodeData = {}
            },
            // 数据变化了设置展示的值
            watchValue(val) {
                this.$nextTick(() => {
                    this.$refs.elTree && this.$refs.elTree.setCurrentKey(val || null)
                    if (val || val == 0) {
                        if (this.$refs.elTree) {
                            this.nodeData = _.get(this.$refs.elTree.getNode(val), 'data') || {}
                        }
                    } else {
                        this.nodeData = {}
                    }
                })
            }
        }
    }
</script>
