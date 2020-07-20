import VueRouter from 'vue-router'
Vue.use(VueRouter)
const routes = [
    {
        path: '/admin/goods',
        name: 'Index',
        component: ()=>import('./Index.vue')
    },
    {
        path: '/admin/goods/create',
            name: 'Create',
        component: ()=>import('./components/CreateOrEdit')
    },
    {
        path: '/admin/goods/:id/edit',
        name: 'Edit',
        component: ()=>import('./components/CreateOrEdit')
    }
]

const router = new VueRouter({
    routes,
    mode: 'history'
})

export default router
