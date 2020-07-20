import VueRouter from 'vue-router'
import Index from './Index.vue'

Vue.use(VueRouter)

const routes = [
    {
        path: '/admin/menu',
        name: 'Index',
        component: Index
    }
]

const router = new VueRouter({
    routes,
    mode: 'history'
})

export default router
