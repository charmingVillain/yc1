<template>
    <div class="card shadow mb-4">
        <div v-if="title || $slots.header" class="card-header py-3">
            <slot name="header">
                <h6 class="m-0 font-weight-bold text-primary" :class="{'float-left': $slots.tools}">
                    <span style="font-size: 1rem;" :class="{'el-button el-button--text': $slots.tools}" >{{ title }}</span>
                    <small v-if="small" class="bg-gradient-warning">{{ small }}</small>
                </h6>
                <div class="float-right" v-if="$slots.tools">
                    <slot name="tools"></slot>
                </div>
            </slot>
        </div>
        <div class="card-body">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Card",
        props: {
            title: {
                type: String,
                default: ''
            },
            small: {
                type: String,
                default: ''
            },
        }
    }
</script>