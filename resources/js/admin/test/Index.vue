<template>
    <div class="row">
        <div class="col-12">

            <j-card title="测试">
                <upload-ali></upload-ali>
            </j-card>

        </div>
    </div>
</template>

<script>

    import UploadAli from "./components/UploadAli";
    export default {
        name: "Index",
        components: {
            UploadAli
        },
        data() {
            return {
                loading: false,
            }
        },
        created() {
        },
        methods: {

        }
    }
</script>
