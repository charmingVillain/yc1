<template>
    <div class="el-upload-list__item is-success">
        <div>
            <img :src="src" class="el-upload-list__item-thumbnail">
            <i class="el-icon-close"></i><i class="el-icon-close-tip">按 delete 键可删除</i>
        </div>
        <span class="el-upload-list__item-actions">
            <slot>

            </slot>
        </span>
    </div>
</template>

<script>
    export default {
        name: "ImageBox",
        props: {
            src: {
                type: String,
                required: true
            },
            item: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        }
    }
</script>
