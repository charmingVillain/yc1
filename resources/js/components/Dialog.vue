<template>
    <el-dialog
            :title="title"
            :visible.sync="innerVisible"
            :width="width"
            :before-close="beforeClose"
            :append-to-body="appendToBody">
        <slot></slot>
        <span slot="footer" v-if="$slots.footer" class="dialog-footer">
            <slot name="footer">

            </slot>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        name: "Dialog",
        props: {
            beforeClose: {
                type: Function,
                default:(done) => {
                    done()
                }
            },
            width: {
                type: String,
                default: '800px'
            },
            visible: {
                type: Boolean,
                default: false
            },
            title: {
                type: String,
                default: ''
            },
            appendToBody:{
                type:Boolean,
                default:false
            }
        },
        data() {
            return {
                innerVisible: this.visible
            }
        },
        watch: {
            innerVisible(val) {
                this.$emit('update:visible', val)
            },
            visible(val) {
                if (val !== this.innerVisible) {
                    this.innerVisible = val
                }
            }
        }
    }
</script>
