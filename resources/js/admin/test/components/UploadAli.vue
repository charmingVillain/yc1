<template>
    <el-upload
        class="upload-demo"
        action="https://jsonplaceholder.typicode.com/posts/"
        :http-request="httpRequest"
        :on-preview="handlePreview"
        :on-remove="handleRemove"
        :before-remove="beforeRemove"
        multiple
        :limit="limit"
        :on-exceed="handleExceed"
        :file-list="fileList">
        <el-button size="small" type="primary">阿里大文件上传</el-button>
    </el-upload>
</template>

<script>
    import OSS from 'ali-oss'
    import cookie from 'js-cookie'
    import moment from 'moment'
    export default {
        name: "UploadAli",
        data() {
            return {
                fileList: [],
                limit: 3,
            }
        },
        methods: {
            handleRemove(file, fileList) {
                console.log(file, fileList);
            },
            handlePreview(file) {
                console.log(file);
            },
            handleExceed(files, fileList) {
                this.$message.warning(`当前限制选择 ${this.limit} 个文件，本次选择了 ${files.length} 个文件，共选择了 ${files.length + fileList.length} 个文件`);
            },
            // 移除
            beforeRemove(file, fileList) {
                return this.$confirm(`确定移除 ${file.name}？`);
            },
            // 获取配置
            getConfig() {
                let cookie_key = 'ali_sts_oss_config';
                let config = cookie.get(cookie_key)
                if (config) {
                    return Promise.resolve(JSON.parse(config))
                }
                return ajax.get('/api/ali/sts').then(response => {
                    let config = {
                        region: response.Region,
                        accessKeyId: response.AccessKeyId,
                        accessKeySecret: response.AccessKeySecret,
                        stsToken: response.SecurityToken,
                        bucket: response.Bucket
                    }
                    let date = moment(response.Expiration).toDate()
                    cookie.set(cookie_key, config, {expires: date})
                    return config;
                })
            },
            getClient() {
                return this.getConfig().then(config => {
                    return new OSS(config);
                })
            },
            httpRequest(request) {
                let tempCheckpoint;
                let file = request.file


                return;
                let name = file.name
                this.getClient().then((client) => {
                    let opts = {
                        parallel: 4,
                        partSize: 1024 * 1024,
                        progress: function (p, cpt, res) {
                            tempCheckpoint = cpt
                            let e = {
                                percent: Math.ceil(p * 100),
                                file
                            }
                            if (parseInt(p) === 1) {
                                e.status = 'success'
                            }
                            request.onProgress(e)
                        }
                    }

                    let callback = {}

                    client.multipartUpload(name, file, opts).then(res => {
                        return client.getObjectUrl(name);
                    }).then((url) => {
                        this.fileList.push({
                            name,
                            url
                        })
                    }).catch((err) => {
                        throw err
                    });
                })
            },
            handleSuccess(file, res) {
                console.log(res)
            },
        }
    }
</script>
