import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

export default new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'Index',
      component: r => require(['../components/Index.vue'], r)
    },
    {
      path: '/home',
      name: 'Home',
      component: r => require(['../components/user/Home.vue'], r)
    },
    {
      path: '/admin',
      name: 'AdminLayout',
      component: r => require(['../components/admin/Layout.vue'], r),
      children: [
        {
          path: 'info',
          name: 'AdminHome',
          component: r => require(['../components/admin/Home.vue'], r)
        },
        {
          path: 'edit',
          name: 'AdminEdit',
          component: r => require(['../components/admin/User/Edit.vue'], r)
        },
        {
          path: 'reset',
          name: 'ResetPwd',
          component: r => require(['../components/admin/User/ResetPwd.vue'], r)
        },
        {
          path: 'manager',
          name: 'ManagerUser',
          component: r => require(['../components/admin/User/Manager.vue'], r)
        }
      ]
    },
    {
      path: '/login',
      name: 'Login',
      component: r => require(['../components/Login.vue'], r)
    },
    {
      path: '/register',
      name: 'Register',
      component: r => require(['../components/Register.vue'], r)
    }
  ]
})
