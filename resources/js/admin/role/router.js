import VueRouter from 'vue-router'
import Index from './Index.vue'
import Menu from './Menu.vue'

Vue.use(VueRouter)

const routes = [
    {
        path: '/admin/role',
        name: 'Index',
        component: Index
    },
    {
        path: '/admin/role/:id/menu',
        name: 'Menu',
        component: Menu
    }
]

const router = new VueRouter({
    routes,
    mode: 'history'
})

export default router
