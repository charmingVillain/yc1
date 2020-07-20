import md5 from 'md5'
import ajax from './ajax'
import * as helper from './helper'
let options = {}

// 获取下拉框
const getOptions = (url, value = 'value', text = 'text', params = {}, dealResponse = (response) => {
    const data = response
    let temp = []
    if (Array.isArray(data)) {
        data.forEach(v => {
            temp.push({
                text: v[text],
                value: v[value]
            })
        })
    }
    return temp
}) => {
    let uuid = md5(url + JSON.stringify(params))
    if (options[uuid]) {
        return Promise.resolve(options[uuid])
    }
    return ajax.get(url, params).then(response => {
        let temp = dealResponse(response)
        if (temp.length > 0) {
            options[uuid] = temp
        }
        return temp
    })
}


// 获取角色的所有保护类型
export function getRoleGuards() {
    return getOptions('/api/role/guards', 'value', 'text', undefined, (response)=> {
        if (Array.isArray(response)) {
            return response
        }
    })
}

