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
      name: 'HomeLayout',
      component: r => require(['../components/user/Layout.vue'], r),
      children: [
        {
          path: 'index',
          name: 'Home',
          component: r => require(['../components/user/Home.vue'], r)
        },
        {
          path: 'user/:id',
          name: 'UserDetail',
          component: r => require(['../components/user/User/ShowDetail.vue'], r)
        }
      ]
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
          path: '/user/:id/edit',
          name: 'AdminEdit',
          component: r => require(['../components/admin/User/Edit.vue'], r)
        },
        {
          path: 'reset',
          name: 'ResetPwd',
          component: r => require(['../components/admin/User/ResetPwd.vue'], r)
        },
        {
          path: 'user/manager',
          name: 'ManagerUser',
          component: r => require(['../components/admin/User/Manager.vue'], r)
        },
        {
          path: 'manager/:id/user',
          name: 'AdminShowUser',
          component: r => require(['../components/admin/User/OtherUserDetail.vue'], r)
        },
        {
          path: 'blog/post',
          name: 'AddBlog',
          component: r => require(['../components/admin/Blog/Add.vue'], r)
        },
        {
          path: 'blog/list',
          name: 'AdminShowBlog',
          component: r => require(['../components/admin/Blog/ShowList.vue'], r)
        },
        {
          path: 'blog/:id/edit',
          name: 'AdminEditBlog',
          component: r => require(['../components/admin/Blog/Edit.vue'], r)
        },
        {
          path: 'blog/:id/',
          name: 'AdminShowBlogDetail',
          component: r => require(['../components/admin/Blog/Show.vue'], r)
        },
        {
          path: 'blogCategory/list',
          name: 'AdminCategoryList',
          component: r => require(['../components/admin/Blog/CategoryList.vue'], r)
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
