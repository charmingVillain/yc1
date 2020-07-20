<style lang="scss">
    .cascade-address {
        .el-input__suffix {
            right: 2px;
            display: none;
        }

        &:hover {
            .el-input__suffix {
                display: block;
            }
        }
    }

    .address-box-tabs {
        .el-button + .el-button {
            margin-left: 5px;
        }

        .el-button {
            margin: 5px;
        }
    }
</style>
<template>
    <el-popover
            placement="bottom-start"
            width="600"
            trigger="manual"
            :disabled="disabled"
            @show="initProvince"
            v-model="show"
            v-clickoutside="hide"
            class="cascade-address"
    >
        <template slot="reference">
            <el-input @click.native="showPopover" clearable readonly :placeholder="placeholder" :value="inputText">
                <span class="el-input__suffix" v-if="!disabled && clearable && inputText" @click.prevent.stop="clear"
                      slot="suffix">
                  <span class="el-input__suffix-inner">
                    <i class="el-input__icon el-icon-circle-close el-input__clear"></i>
                  </span>
                </span>
            </el-input>
        </template>
        <el-tabs class="address-box-tabs" v-model="activeName">
            <el-tab-pane label="省" name="0">
                <div class="text-center" v-if="provinces.length == 0">
                    <i class="el-icon-loading"></i>加载中
                </div>
                <el-button plain v-for="item in provinces" :key="item.value" @click.prevent="setModel(item, 0)"
                           :class="getBtnClass(provinceNo == item.value)">{{ item.text }}
                </el-button>
            </el-tab-pane>
            <el-tab-pane v-if="level > 1" :disabled="cities.length == 0" label="市" name="1">
                <div class="text-center" v-if="cities.length == 0">
                    <i class="el-icon-loading"></i>加载中
                </div>
                <el-button plain v-for="item in cities" :key="item.value" @click.prevent="setModel(item, 1)"
                           :class="getBtnClass(cityNo == item.value)">{{ item.text }}
                </el-button>
            </el-tab-pane>
            <el-tab-pane v-if="level > 2" :disabled="districts.length == 0" label="区" name="2">
                <div class="text-center" v-if="districts.length == 0">
                    <i class="el-icon-loading"></i>加载中
                </div>
                <el-button plain v-for="item in districts" :key="item.value" @click.prevent="setModel(item, 2)"
                           :class="getBtnClass(districtNo == item.value)">{{ item.text }}
                </el-button>
            </el-tab-pane>
            <el-tab-pane v-if="level > 3" :disabled="streets.length == 0" label="街道" name="3">
                <div class="text-center" v-if="streets.length == 0">
                    <i class="el-icon-loading"></i>加载中
                </div>
                <el-button plain v-for="item in streets" :key="item.value" @click.prevent="setModel(item, 3)"
                           class="btn"
                           :class="getBtnClass(streetNo == item.value)">{{ item.text }}
                </el-button>
            </el-tab-pane>
        </el-tabs>
    </el-popover>
</template>

<script>
    import clickoutside from '@/directive/clickoutside'

    export default {
        name: 'el-address-box',
        directives: {clickoutside},
        props: {
            level: {
                type: Number,
                default: 3
            },
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
            changeOnSelect: {
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
            }
        },
        data() {
            return {
                provinces: [],
                cities: [],
                districts: [],
                streets: [],
                model: [],
                activeName: '0',
                props: {
                    value: 'value',
                    label: 'text',
                    children: 'children'
                },
                show: false
            }
        },
        computed: {
            // 文本字段
            inputText() {
                let temp = []
                if (this.provinceItem && _.get(this.provinceItem, 'text')) {
                    temp.push(_.get(this.provinceItem, 'text'))
                }
                if (this.cityItem && _.get(this.cityItem, 'text')) {
                    temp.push(_.get(this.cityItem, 'text'))
                }
                if (this.districtItem && _.get(this.districtItem, 'text')) {
                    temp.push(_.get(this.districtItem, 'text'))
                }
                if (this.streetItem && _.get(this.streetItem, 'text')) {
                    temp.push(_.get(this.streetItem, 'text'))
                }
                if (this.changeOnSelect) {
                    return temp.join('/')
                } else {
                    if (temp.length >= this.level) {
                        return temp.join('/')
                    }
                    return ''
                }
            },
            // 省编号
            provinceNo() {
                return _.get(this.model, '[0].value') || ''
            },
            // 市编号
            cityNo() {
                return _.get(this.model, '[1].value') || ''
            },
            // 区编号
            districtNo() {
                return _.get(this.model, '[2].value') || ''
            },
            // 街道编号
            streetNo() {
                return _.get(this.model, '[3].value') || ''
            },
            // 省对象
            provinceItem() {
                return this.getTextByValue(this.provinceNo, this.provinces)
            },
            // 市对象
            cityItem() {
                return this.getTextByValue(this.cityNo, this.cities)
            },
            // 区对象
            districtItem() {
                return this.getTextByValue(this.districtNo, this.districts)
            },
            // 街道对象
            streetItem() {
                return this.getTextByValue(this.streetNo, this.streets)
            }
        },
        watch: {
            model(val, oldVal) {
                let ids = val.map(v => v.value)
                ids.noRefresh = true
                val.noRefresh = true
                if (this.changeOnSelect) {
                    this.$emit('input', ids)
                    this.$emit('change', val)
                } else {
                    // 清空和选择完
                    if (this.model.length === 0 || this.model.length >= this.level) {
                        this.$emit('input', ids)
                        this.$emit('change', val)
                    }
                }
            },
            value(val) {
                if (!val.hasOwnProperty('noRefresh')) {
                    // 如果有值就自动选择
                    this.init()
                    // 清空了值就清空值
                    if (Array.isArray(val)) {
                        let temp = []
                        val.forEach(v => {
                            temp.push({
                                text: '',
                                value: v
                            })
                        })
                        this.model = temp
                    }
                }
            }
        },
        created() {
            this.init()
        },
        methods: {
            initProvince() {
                if (this.provinces.length > 0) {
                    return
                }
                this.getAddressData({value: 100000}).then(arr => {
                    this.provinces = arr
                })
            },
            init() {
                if (Array.isArray(this.value) && this.value.length > 0) {
                    this.initProvince()
                    this.value.forEach((v, index) => {
                        this.setModel({value: v}, index, this.next)
                    })
                }
            },
            // 设置模型的值
            setModel(item, index, next = this.next) {
                this.$set(this.model, index, item)
                this.model.splice(index + 1, this.model.length - index)
                // 获取下一级的数据
                if (index === 0) {
                    this.cities = []
                    this.districts = []
                    this.streets = []
                    if (item.value) {
                        this.getAddressData({value: item.value}).then(arr => {
                            this.cities = arr
                        })
                    }
                } else if (index === 1) {
                    this.districts = []
                    this.streets = []
                    this.getAddressData({value: item.value}).then(arr => {
                        this.districts = arr
                    })
                } else if (index === 2) {
                    this.streets = []
                    this.getAddressData({value: item.value}).then(arr => {
                        this.streets = arr
                    })
                }
                next(index)
            },
            next(level) {
                if (this.level <= level + 1) {
                    this.hide()
                    this.activeName = '0'
                } else {
                    this.activeName = String(level + 1)
                }
            },
            hide() {
                if (this.changeOnSelect) {
                    this.show = false
                } else {
                    if (this.model.length === 0 || this.model.length >= this.level) {
                        this.show = false
                    }
                }
            },
            getTextByValue(value, arr) {
                if (value && Array.isArray(arr) && arr.length > 0) {
                    for (let v of arr) {
                        if (v.value == value) {
                            return v
                        }
                    }
                }
                return ''
            },
            getBtnClass(flag = false) {
                return flag ? 'el-button--success' : 'el-button--default'
            },
            showPopover() {
                this.show = true
            },
            clear() {
                this.model = []
                this.activeName = '0'
                this.cities = []
                this.districts = []
                this.streets = []
            },
            getAddressData({value = 100000}) {
                return ajax.get('/api/area-new', {parent_no: value}).then(response => {
                    if (Array.isArray(response)) {
                        return response.map(v => {
                            return {
                                value: v.value,
                                text: v.text
                            }
                        })
                    }
                    return []
                })
            }
        }
    }
</script>
