import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

export default new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '/home',
      name: 'Home',
      component: r => require(['../components/user/Home.vue'], r)
    },
    {
      path: '/admin',
      name: 'Admin',
      component: r => require(['../components/admin/Home.vue'], r)
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
