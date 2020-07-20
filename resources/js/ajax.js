/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
import _ from 'lodash'

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// 请求拦截
window.axios.interceptors.request.use((config) => {
    // 删除一下空的params
    for (let k in config.params) {
        if (!config.params[k] && config.params[k] !== 0) {
            delete config.params[k]
        }
    }
    return config
}, (error) => {
    return Promise.reject(error)
})

// 响应拦截
window.axios.interceptors.response.use((response) => {
    return response
}, (error) => {
    return handleError(error)
})


export function handleError(error) {
    // if (error.response.status == 401) {
    //     // 自动刷新一下token
    //     if (error.config.url !== '/api/oauth/token' && !error.config.refreshToken) {
    //         return post('/api/oauth/refresh-token').then(data => {
    //             let config = _.clone(error.config, true)
    //             setToken(data.access_token)
    //             config.refreshToken = 1
    //             return window.axios(config)
    //         })
    //     }
    // }
    const status = _.get(error, 'response.status')
    if (status === 520) {
        helper.alert(_.get(error, 'response.data.message'))
    } else if (status === 403) {
        helper.alert('你无权访问，请联系管理员：' + _.get(error, 'request.responseURL'))
    } else if (status === 401) {
        helper.confirm('登陆信息已过期，请重新登陆').then(() => {
            window.location.href = '/admin/login'
        })
    } else if(status === 422) {
        let errors =  _.get(error, 'response.data.errors')
        helper.alert(_.first(_.first(_.values(errors))) || `数据验证未通过`)
    } else {
        helper.alert(`状态码:${status} ` + _.get(error, 'response.data.message'))
    }
    return Promise.reject(error)
}

// get请求
const get = (url, params = {}, options = {}) => {
    options.params = params
    return axios.get(url, options).then(response => {
        return response.data
    })
}

// post 请求
function post(url, data = {}, params = {}, options = {}) {
    options.params = params
    return axios.post(url, data, options).then(response => {
        return response.data
    })
}

// put 请求
function put(url, data = {}, params = {}, options = {}) {
    options.params = params
    return axios.put(url, data, options).then(response => {
        return response.data
    })
}

// 删除请求
function del(url, params = {}, options = {}) {
    options.params = params
    return axios.delete(url, options).then(response => {
        return response.data
    })
}

// 表格请求
function table(url, params, options) {
    return get.apply(this, arguments)
}

window.ajax = {
    get,
    delete: del,
    post,
    put,
    table
}


export default ajax