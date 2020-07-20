import VueRouter from 'vue-router'
Vue.use(VueRouter)
const routes = [
    {
        path: '/admin/user',
        name: 'Index',
        component: ()=>import('./Index.vue')
    },
    {
        path: '/admin/user/create',
        name: 'Create',
        component: ()=>import('./Create.vue')
    },
    {
        path: '/admin/user/:id/edit',
        name: 'Edit',
        component: ()=>import('./Create.vue')
    }
]

const router = new VueRouter({
    routes,
    mode: 'history'
})

export default router
