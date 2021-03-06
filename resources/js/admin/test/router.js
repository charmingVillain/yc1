import VueRouter from 'vue-router'
Vue.use(VueRouter)
const routes = [
    {
        path: '/admin/test',
        name: 'Index',
        component: ()=>import('./Index.vue')
    }
]

const router = new VueRouter({
    routes,
    mode: 'history'
})

export default router
