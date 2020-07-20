import ajax from './ajax'
import * as ajaxOptions from './ajaxOptions'
import * as validator from './validator'
import * as helper from './helper'
import Vue from 'vue'
import ElementUI from 'element-ui'
import 'viewerjs/dist/viewer.css'
import Viewer from 'v-viewer'
import globalComponents from "./globalComponents"
// import _ from 'lodash'
// window._ = _;
window._ = require('lodash');

window.ajaxOptions = ajaxOptions
window.Vue = Vue;
window.helper = helper
window.validator = validator;

// use plugins
Vue.use(ElementUI)
Vue.use(Viewer)
//
for(let k in globalComponents) {
    Vue.component(k, globalComponents[k]);
}

require('./filters');

// import Tinymce from './components/Tinymce'
// window.tinymce = Tinymce

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.ajax = ajax
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
