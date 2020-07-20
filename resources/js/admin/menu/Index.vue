<template>
    <div class="row">
        <div class="col-xs-12 col-lg-12">
            <!-- Approach -->
            <j-card v-loading="loading" class="card shadow mb-4" title="菜单管理" small="可拖动排序">
                <template slot="tools">
                    <el-dialog
                        :title="model.id ? '编辑': '新增'"
                        :visible.sync="showCreateModal"
                        width="800px"
                    >
                        <el-form ref="createForm"
                                 :model="model" label-width="80px">

                            <el-form-item
                                :rules="[
                                            {required: true, message: '菜单路径必须', trigger: 'change'}
                                        ]"
                                prop="uri"
                                label="菜单路径">
                                <el-select filterable style="width: 100%" v-model="model.uri">
                                    <el-option v-for="item in routeList" :key="item" :value="item"
                                               v-text="item"></el-option>
                                </el-select>
                            </el-form-item>

                            <el-form-item
                                :rules="[
                                            {required: true, message: '菜单名称必须', trigger: 'blur'},
                                            {min: 2, max: 100, message: '菜单名称字数在2~100之间', trigger: 'blur'},
                                        ]"
                                prop="name"
                                label="菜单名称">
                                <el-input v-model="model.name"></el-input>
                            </el-form-item>

                            <el-form-item
                                :rules="[
                                            {required: true, type: 'number', message: '上级菜单必须', trigger: 'change'}
                                        ]"
                                prop="pid"
                                label="菜单路径">
                                <tree-select :data="treeData" v-model="model.pid">
                                </tree-select>
                            </el-form-item>

                            <el-form-item
                                :rules="[
                                            {max: 200, message: '图标字数最大200', trigger: 'blur'},
                                        ]"
                                prop="icon"
                                label="图标">
                                <el-input v-model="model.icon"></el-input>
                            </el-form-item>

                            <el-form-item
                                prop="is_ajax"
                                label="ajax访问">
                                <el-checkbox v-model="model.is_ajax"></el-checkbox>
                            </el-form-item>

                        </el-form>

                        <span slot="footer" class="dialog-footer">
                                <el-button @click.prevent="showCreateModal = false">取 消</el-button>
                                <el-button type="primary" :loading="createLoading" @click="create">确 定</el-button>
                            </span>
                    </el-dialog>

                    <el-button type="primary" @click.prevent="showCreateModalFunc()">添加根菜单</el-button>
                </template>

                <el-input
                    class="mb-2"
                    placeholder="输入关键字进行过滤"
                    v-model="filterText">
                </el-input>

                <el-tree
                    ref="tree"
                    :data="list"
                    node-key="id"
                    default-expand-all
                    :expand-on-click-node="false"
                    draggable
                    @node-drop="nodeDrop"
                    :filter-node-method="filterNode"
                >
                            <span slot-scope="{ node, data }">

                                <span>{{ node.label }}</span>

                                <span class="ml-3">
                                    <el-button
                                        type="text"
                                        size="mini"
                                        @click="() => showCreateModalFunc(data.id)">
                                        添加
                                    </el-button>

                                    <el-button
                                        type="text"
                                        size="mini"
                                        @click="() => showEditModal(data)">
                                        修改
                                    </el-button>

                                    <el-button
                                        type="text"
                                        size="mini"
                                        @click="() => del(data)">
                                        删除
                                    </el-button>

                                     <el-popover
                                         placement="top-start"
                                         title="后端地址"
                                         width="200"
                                         trigger="hover"
                                         :content="data.uri">
                                        <el-button
                                            type="text"
                                            slot="reference"
                                            size="mini"
                                        >
                                            <i class="el-icon-info"></i>
                                        </el-button>
                                    </el-popover>
                                </span>
                          </span>
                </el-tree>


            </j-card>
        </div>
    </div>
</template>

<script>

    export default {
        name: "Index",
        data() {
            return {
                model: {
                    id: '',
                    name: '',
                    uri: '',
                    pid: '',
                    icon: '',
                    guard_name: 'admin',
                    is_ajax: false
                },
                loading: false,
                searchLoading: false,
                createLoading: false,
                showCreateModal: false,
                list: [],
                filterText: ''
            }
        },
        computed: {
            routeList() {
                if (Array.isArray(window.routeList)) {
                    return window.routeList.map(v => v.value)
                }
                return []
            },
            treeData() {
                return [
                    {text: '根菜单', value: 0, children: this.list}
                ]
            }
        },
        watch: {
            filterText(val) {
                this.$refs.tree.filter(val);
            }
        },
        created() {
            this.fetch()
        },
        methods: {
            alpha_dash: validator.alpha_dash,
            filterNode(value, data) {
                if (!value) return true;
                return data.label.indexOf(value) !== -1;
            },
            // 新增
            create() {
                this.$refs.createForm.validate((result) => {
                    if (!result) {
                        return false
                    }
                    this.createLoading = true
                    if (this.model.id) {
                        console.log(routeConfig.update)
                        ajax.put('/admin/menu/' + this.model.id, this.model).then(_ => {
                            helper.alert('编辑成功', {type: 'success'})
                            this.showCreateModal = false
                            this.$refs.createForm.resetFields()
                            this.refresh()
                        }).finally(_ => {
                            this.createLoading = false
                        })
                    } else {
                        ajax.post(routeConfig.store, this.model).then(_ => {
                            helper.alert('新增成功', {type: 'success'})
                            this.showCreateModal = false
                            this.$refs.createForm.resetFields()
                            this.refresh()
                        }).finally(_ => {
                            this.createLoading = false
                        })
                    }
                })
            },
            // 新增展示
            showCreateModalFunc(pid = 0) {
                this.model.id = ''
                this.model.name = ''
                this.model.uri = ''
                this.model.icon = ''
                this.model.pid = pid
                this.model.is_ajax = false
                this.showCreateModal = true
                this.clearModalValidate()
            },
            clearModalValidate() {
                if (this.$refs.createForm) {
                    this.$nextTick(() => {
                        this.$refs.createForm.clearValidate()
                    })
                }
            },
            // 编辑展示
            showEditModal(row) {
                console.log(row.is_ajax,'row编辑展示')
                this.model = _.cloneDeep(row)
                if (row.is_ajax){
                    this.model.is_ajax = true
                }
                this.showCreateModal = true
                this.clearModalValidate()
            },
            // 删除
            del(row) {
                helper.confirm('确定要删除此菜单').then(_ => {
                    ajax.delete(helper.bind_str(routeConfig.destroy, {id: row.id})).then(_ => {
                        helper.alert('删除成功', {type: 'success'})
                        this.refresh()
                    })
                })
            },
            // 拖动排序
            nodeDrop(draggingNode, dropNode, dropType, ev) {
                ajax.put(helper.bind_str(routeConfig.sort, {id: draggingNode.data.id}), {
                    end_id: dropNode.data.id,
                    type: dropType
                }).then(response => {
                    helper.alert('排序成功', {type: 'success'})
                    this.refresh()
                })
            },
            // 刷新
            refresh() {
                this.fetch()
            },
            // 获取列表
            fetch() {
                this.loading = true
                ajax.get(routeConfig.index).then(response => {
                    if (Array.isArray(response)) {
                        this.list = helper.array_to_tree(response.map(v => {
                            v.label = v.name
                            v.text = v.name
                            v.value = v.id
                            return v
                        }), 'id', 'pid', 'children', '0', true)
                    }
                }).finally(_ => {
                    this.loading = false
                })
            },
        }
    }
</script>
