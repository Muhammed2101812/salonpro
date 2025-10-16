import {createRouter,createWebHistory} from 'vue-router';
const routes=[
{path:'/login',name:'Login',component:()=>import('@/views/Auth/Login.vue')},
{path:'/',name:'Dashboard',component:()=>import('@/views/Dashboard.vue'),meta:{requiresAuth:true}},
{path:'/branches',name:'Branches',component:()=>import('@/views/Branches/Index.vue'),meta:{requiresAuth:true}},
{path:'/customers',name:'Customers',component:()=>import('@/views/Customers/Index.vue'),meta:{requiresAuth:true}},
{path:'/employees',name:'Employees',component:()=>import('@/views/Employees/Index.vue'),meta:{requiresAuth:true}},
];
const router=createRouter({history:createWebHistory(),routes});
router.beforeEach((to,from,next)=>{
const token=localStorage.getItem('auth_token');
if(to.meta.requiresAuth&&!token){next('/login');}else{next();}
});
export default router;
