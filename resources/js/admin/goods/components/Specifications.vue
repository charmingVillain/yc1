<style scoped>
    .el-tag + .el-tag {
        margin-left: 10px;
    }

    .button-new-tag {
        margin-left: 10px;
        height: 32px;
        line-height: 30px;
        padding-top: 0;
        padding-bottom: 0;
    }

    .input-new-tag {
        width: 90px;
        margin-left: 10px;
        vertical-align: bottom;
    }
</style>
<template>
    <div class="row">
        <div class="col-12" v-if="canEditSpecificationKey">
            <el-tag v-for="(label, index) in labels" :key="label.name" closable @close="handleClose(index)">
                {{ label.name }}
            </el-tag>

            <el-input
                    class="input-new-tag"
                    v-if="inputVisible"
                    v-model="specification"
                    ref="saveTagInput"
                    size="small"
                    @keyup.enter.native="addSpecificationKey"
                    @blur="addSpecificationKey"
            >
            </el-input>

            <el-button v-else class="button-new-tag" size="small" @click="showInput" :disabled="disabled">添加属性
            </el-button>
        </div>
        <div v-else class="col-12">
            <el-tag v-for="(label) in labels" :key="label.name">
                {{ label.name }}
            </el-tag>
        </div>

        <div class="col-12">
            <el-form ref="elForm" :model="model">

                <el-form-item :error="tableItemError">
                    <el-table
                            :data="model.tableData"
                    >
                        <el-table-column
                                v-for="(item, index) in lists"
                                :label="item.name"
                                :key="item.name"
                                min-width="200">
                            <template slot-scope="scope">
                                <span v-if="!canEditName"> {{ scope.row.list[index].value }}</span>

                                <el-form-item v-else :key="scope.$index + item.name"
                                              :prop="'tableData.' + scope.$index +  '.list.' + index +  '.value'"
                                              :rules="[
                                                {required: true, message: getListNameMessage(scope.row, index) + '必须', trigger: 'blur' }
                                            ]"
                                >
                                    <el-input v-model.trim="scope.row.list[index].value"></el-input>
                                </el-form-item>
                            </template>
                        </el-table-column>

                        <el-table-column
                                v-if="isSupplier"
                                prop="supplier_price"
                                label="成本价"
                                min-width="180">
                            <template slot-scope="scope">
                                <el-form-item :prop="'tableData.' + scope.$index +  '.supplier_price'" :rules="[
                                    {required: true, message: '成本价必须', trigger: 'blur' },
                                    {validator: validator.isFormatFloat, message: '成本价必须是小数位最多2位的数字', trigger: 'blur' },
                            ]">
                                    <el-input-number :min="0.00" v-model="scope.row.supplier_price"
                                                     controls-position="right"
                                                     :precision="2"></el-input-number>
                                </el-form-item>

                            </template>
                        </el-table-column>

                        <template v-else>

                            <el-table-column
                                    prop="supplier_price"
                                    label="成本价"
                                    min-width="100">
                                <template slot-scope="scope">
                                    <span class="text-danger"> {{scope.row.supplier_price }} </span>
                                </template>
                            </el-table-column>

                            <!--<el-table-column
                                    prop="supplier_sales_price"
                                    label="平台分销价"
                                    min-width="180">
                                <template slot-scope="scope">
                                    <el-form-item :prop="'tableData.' + scope.$index +  '.supplier_sales_price'"
                                                  :rules="[
                                        {required: true, message: '分销价必须', trigger: 'blur' },
                                        {validator: validator.isFormatFloat, message: '分销价必须是小数位最多2位的数字', trigger: 'blur' },

                                    ]">
                                        <el-input-number :min="0.00" v-model="scope.row.supplier_sales_price"
                                                         controls-position="right"
                                                         :precision="2"></el-input-number>
                                    </el-form-item>
                                </template>
                            </el-table-column>-->

                            <el-table-column
                                    prop="sales_price"
                                    label="零售价"
                                    min-width="180">
                                <template slot-scope="scope">
                                    <span v-if="disabled"> {{scope.row.sales_price }} </span>
                                    <el-form-item v-else :prop="'tableData.' + scope.$index +  '.sales_price'" :rules="[
                                        {required: true, message: '零售价必须', trigger: 'blur' },
                                        {validator: validator.isFormatFloat, message: '零售价必须是小数位最多2位的数字', trigger: 'blur' },
                                        { validator : validator.range, min: scope.row.supplier_price, message : '必须大于成本价', trigger:'blur'},
                                    ]">
                                        <el-input-number :disabled="isPlatformUpdatePrice" :min="0.00" v-model="scope.row.sales_price"
                                                         controls-position="right"
                                                         :precision="2"></el-input-number>
                                    </el-form-item>
                                </template>
                            </el-table-column>

                            <el-table-column
                                    v-if="(!isSupplier) && platformId==1"
                                    prop="integral"
                                    label="积分"
                                    min-width="180">
                                <template slot-scope="scope">
                                    <el-form-item :prop="'tableData.' + scope.$index +  '.integral'" :rules="[
                                    {required: true, message: '积分必须', trigger: 'blur' },
                                    {validator: validator.isPositiveInt, message: '积分必须是正整数', trigger: 'blur' },
                            ]">
                                        <el-input-number :disabled="isPlatformUpdatePrice" :min="0.00" v-model="scope.row.integral"
                                                         controls-position="right"
                                                         :precision="2"></el-input-number>
                                    </el-form-item>

                                </template>
                            </el-table-column>
                        </template>

                        <template v-if="isAreaBeforeGoods">
                            <el-table-column
                                    prop="area_before_sale_price"
                                    label="专区售价(扣预付积分)"
                                    min-width="180"
                            >
                                <template slot-scope="scope">
                                    <el-form-item v-if="scope.row.is_area_before_goods" :prop="'tableData.' + scope.$index +  '.area_before_sale_price'" :rules="[
                                        {required: true, message: '零售价必须', trigger: 'blur' },
                                        {validator: validator.isFormatFloat, message: '零售价必须是小数位最多2位的数字', trigger: 'blur' },
                                    ]">
                                        <el-input-number  :min="0.00" v-model="scope.row.area_before_sale_price"
                                                          controls-position="right"
                                                          :precision="2"></el-input-number>
                                    </el-form-item>
                                </template>
                            </el-table-column>
                            <el-table-column
                                    prop="area_before_integral"
                                    label="预付专区积分（扣购物积分）"
                                    min-width="180">
                                <template slot-scope="scope">
                                    <el-form-item v-if="scope.row.is_area_before_goods" :prop="'tableData.' + scope.$index +  '.area_before_integral'" :rules="[
                                    {required: true, message: '积分必须', trigger: 'blur' },
                                    {validator: validator.isPositiveInt, message: '积分必须是正整数', trigger: 'blur' },
                            ]">
                                        <el-input-number :min="0.00" v-model="scope.row.area_before_integral"
                                                         controls-position="right"
                                                         :precision="2"></el-input-number>
                                    </el-form-item>

                                </template>
                            </el-table-column>
                        </template>
                        <template v-if="isCashGoods">
                            <el-table-column
                                prop="cash_sale_price"
                                label="现金专区售价"
                                min-width="180"
                            >
                                <template slot-scope="scope">
                                    <el-form-item v-if="scope.row.is_cash_goods" :prop="'tableData.' + scope.$index +  '.cash_sale_price'" :rules="[
                                        {required: true, message: '现金专区售价必须', trigger: 'blur' },
                                        {validator: validator.isFormatFloat, message: '现金专区售价必须是小数位最多2位的数字', trigger: 'blur' },
                                    ]">
                                        <el-input-number  :min="0.00" v-model="scope.row.cash_sale_price"
                                                          controls-position="right"
                                                          :precision="2"></el-input-number>
                                    </el-form-item>
                                </template>
                            </el-table-column>
                        </template>
                        <el-table-column
                                prop="inventory_number"
                                label="商品库存"
                                min-width="180"
                        >
                            <template slot-scope="scope">
                                <span v-if="!isSupplier"> {{scope.row.inventory_number }} </span>
                                <el-form-item v-else :prop="'tableData.' + scope.$index +  '.inventory_number'" :rules="[
                                {required: true, message: '商品库存必须', trigger: 'blur' },
                                {validator: validator.isFormatFloat, message: '商品库存必须是小数位最多2位的数字', trigger: 'blur' },
                            ]">
                                    <el-input-number :min="0.00" v-model="scope.row.inventory_number"
                                                     controls-position="right"
                                                     :precision="0"></el-input-number>
                                </el-form-item>

                            </template>
                        </el-table-column>
                        <el-table-column
                                prop="weight"
                                label="重量(kg)"
                                min-width="180"
                        >
                            <template slot-scope="scope">
                                <span v-if="!isSupplier"> {{scope.row.weight }} </span>
                                <el-form-item v-else :prop="'tableData.' + scope.$index +  '.weight'" :rules="[
                                {required: true, message: '重量必须', trigger: 'blur' },
                                {validator: validator.isFormatFloat, message: '重量必须是小数位最多3位的数字', decimal: 3, trigger: 'blur' },
                            ]">
                                    <el-input-number :min="0.00" v-model="scope.row.weight" controls-position="right"
                                                     :precision="3"></el-input-number>
                                </el-form-item>

                            </template>
                        </el-table-column>

                        <el-table-column v-if="isSupplier"
                                         min-width="180"
                                         label="操作">
                            <template slot-scope="scope">
                                <el-form-item>
                                    <el-button
                                            size="mini"
                                            type="danger"
                                            @click="handleDelete(scope.$index, scope.row)"
                                            :disabled="disabled">删除
                                    </el-button>
                                    <el-button
                                            v-if="scope.$index == model.tableData.length - 1"
                                            size="mini"
                                            type="primary"
                                            @click="addRow()"
                                            :disabled="disabled">添加
                                    </el-button>
                                </el-form-item>

                            </template>
                        </el-table-column>

                    </el-table>
                </el-form-item>

            </el-form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Specifications",
        props: {
            value: {
                type: Array,
                default() {
                    return []
                }
            },
            disabled: {
                type: Boolean,
                default: false
            },
            needDirty: {
                type: Boolean,
                default: false
            },
            role: {
                type: String,
                default: 'supplier'
            },
            isPlatformUpdatePrice:{
                type:Boolean,
                default:true
            },
            isAreaBeforeGoods:{
                type:Boolean,
                default:false
            },
            isCashGoods:{
                type:Boolean,
                default:false
            },
            platformId:{
                type:Number,
                default:1
            }
        },
        data() {
            return {
                model: {
                    tableData: [],
                },
                specification: '',
                inputVisible: false,
                tableRowExample: {
                    supplier_price: '',
                    sales_price: '',
                    integral:'',
                    //supplier_sales_price: '',
                    inventory_number: '',
                    weight: '',
                    area_before_sale_price: '',
                    cash_sale_price: '',
                    area_before_integral: '',
                    list: []
                },
                validator: validator,
                tableError: '',
                originTableData: []
            }
        },
        computed: {
            // 是不是供应商操作
            isSupplier() {
                return this.role === 'supplier'
            },
            isPlatform() {
                return this.role === 'platform'
            },
            labels() {
                return this.lists
            },
            lists() {
                let first = _.first(this.model.tableData) || []
                return first.list || []
            },
            // 所有的值的组成数组
            listValues() {
                return this.model.tableData.map(v => {
                    let list = _.get(v, 'list')
                    if (Array.isArray(list)) {
                        return list.map(v => v.value)
                    }
                    return []
                }).filter(v => v.length > 0)
            },
            // 是否有重复的属性
            hasSameKeyAndValue() {
                let value_arr = this.listValues.filter(v => {
                    return !v.includes('') // 过滤掉空的值
                }).map(v => {
                    return v.join(',')
                })
                return value_arr.length !== _.uniq(value_arr).length
            },
            tableItemError() {
                let sameError = this.hasSameKeyAndValue ? '不能有相同的属性' : ''
                return this.tableError || sameError
            },
            canEditSpecificationKey() {
                return this.isSupplier
            },
            canEditName() {
                return this.isSupplier
            }
        },
        watch: {
            'model.tableData': {
                handler(val, oldVal) {
                    if (this.needDirty) {
                        this.$emit('isPriceDirty', this.isPriceDirty())
                    }
                    if (!_.isEqual(val, this.value)) {
                        this.$emit('input', val)
                    }
                },
                deep: true
            },
            value: {
                handler(val, oldVal) {
                    if (!_.isEqual(val, this.model.tableData)) {
                        this.originTableData = _.cloneDeep(val)
                        this.model.tableData = val
                    }
                },
                immediate: true
            },
        },
        mounted() {
            if (this.model.tableData.length === 0) {
                this.addRow()
            }
        },
        methods: {
            addSpecificationKey() {
                let specification = this.specification
                if (!specification) {
                    return
                }

                // 验证
                if (this.labels.length >= 3) {
                    helper.alert('属性最多3个')
                    this.inputVisible = false
                    this.specification = ''
                    return
                }

                if (this.labels.map(v => v.name).includes(specification)) {
                    helper.alert('已存在相同的属性')
                    this.inputVisible = false
                    this.specification = ''
                    return
                }

                this.model.tableData.forEach(v => {
                    if (Array.isArray(v.list)) {
                        v.list.push({
                            name: specification,
                            value: ''
                        })
                    } else {
                        v.list = [{
                            name: specification,
                            value: ''
                        }]
                    }
                    return v
                })

                this.inputVisible = false
                this.specification = ''
                if (this.labels.length > 0) {
                    this.tableError = ''
                }
            },

            handleClose(index) {
                this.model.tableData.forEach(v => {
                    if (Array.isArray(v.list)) {
                        v.list.splice(index, 1)
                    } else {
                        v.list = []
                    }
                })
            },

            showInput() {
                this.inputVisible = true;
                this.$nextTick(_ => {
                    this.$refs.saveTagInput.$refs.input.focus();
                });
            },

            addRow() {
                let item = _.cloneDeep(this.tableRowExample)
                item.list = this.lists.map(v => {
                    return {
                        name: v.name,
                        value: ''
                    }
                })
                this.model.tableData.push(item)
            },

            isValidate() {
                return new Promise((resolve, reject) => {


                    if (this.lists < 1) {
                        this.tableError = '请至少添加一个属性'
                    } else {
                        this.tableError = ''
                    }

                    if (this.lists < 1) {
                        reject(false)
                    }

                    if (this.hasSameKeyAndValue) {
                        reject(false)
                    }

                    this.$refs.elForm.validate(result => {
                        if (result) {
                            resolve(result)
                        } else {
                            reject(result)
                        }
                    })
                })
            },
            getListNameMessage(row, index) {
                return _.get(row, `list[${index}].name`)
            },
            handleDelete(index) {
                if (this.model.tableData.length <= 1) {
                    helper.alert('至少保留一条数据')
                    return
                }
                this.model.tableData.splice(index, 1)
            },
            // 是否修改了
            isPriceDirty() {
                let keys = ['sales_price', 'supplier_price']

                return !_.isEqual(this.originTableData.map(v => {
                    let temp = {}
                    keys.forEach(key => {
                        temp[key] = parseFloat(_.get(v, key))
                    })
                    return {
                        temp
                    }
                }), this.value.map(v => {
                    let temp = {}
                    keys.forEach(key => {
                        temp[key] = parseFloat(_.get(v, key))
                    })
                    return {
                        temp
                    }
                }))
            }
        }
    }
</script>
