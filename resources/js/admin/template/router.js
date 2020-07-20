import VueRouter from 'vue-router'
Vue.use(VueRouter)
const routes = [
    {
        path: '/admin/template',
        name: 'Index',
        component: ()=>import('./Index.vue')
    },
    {
        path: '/admin/template/create',
        name: 'Create',
        component: ()=>import('./components/CreateOrEdit')
    },
    {
        path: '/admin/template/:id/edit',
        name: 'Edit',
        component: ()=>import('./components/CreateOrEdit')
    }
]

const router = new VueRouter({
    routes,
    mode: 'history'
})

export default router
