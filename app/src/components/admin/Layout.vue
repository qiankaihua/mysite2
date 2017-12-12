<template>
  <div id="admin-index">
    <div class="layout">
      <i-row type="flex" class="sidebar">
        <i-col span="5" class="layout-menu-left">
          <i-menu theme="dark" width="auto" :open-names="[]"
                  :active-name="$route.name"
                  @on-select="$router.push({ name: $event, params: {id: $store.state.auth.authUser.id} })"
                  :default-active="$route.name" :accordion=true>
            <div class="layout-logo-left">To Be Continue</div>
            <i-menu-item name="Home" :key="1107">
              <i-icon type="home" :size="iconSize"></i-icon>
              <span class="layout-text">回到首页</span>
            </i-menu-item>
            <i-submenu v-for="(sidebar, idx) in SideBar" :name="sidebar.name" :key="idx">
              <template slot="title">
                <i-icon :type="sidebar.icon"></i-icon>
                {{ sidebar.title }}
              </template>
              <i-menu-item v-for="(bar, idx) in sidebar.items" :name="bar.to.name" :key="idx" v-if="bar.show">
                <i-icon :type="bar.icon" :size="iconSize"></i-icon>
                <span class="layout-text">{{ bar.title }}</span>
              </i-menu-item>
            </i-submenu>
          </i-menu>
          <i-button v-if="status" @click="Logout" class="layout-text logout" type="error">Logout</i-button>
        </i-col>
        <i-col span="19" offset="5">
          <div class="layout-header">
            <i-avatar v-if="avatar" :src="avatar" size="large" shape="square" class="avatar" />
            <i-avatar v-else icon="person" size="large" shape="square" class="avatar" />
            <span class="Nickname">{{ nickname }}</span>
          </div>
          <router-view></router-view>
        </i-col>
      </i-row>
    </div>
    <i-back-top :height="20" :bottom="50"></i-back-top>
  </div>
</template>

<script>
  import axios from 'axios'
  export default {
    name: 'AdminLayout',
    components: {
    },
    data () {
      return {
        iconSize: 18,
        status: this.status,
        username: this.username,
        avatar: this.avatar,
        SideBar: [
          {
            name: 'user',
            icon: 'person',
            title: '个人信息维护',
            items: [
              {to: {name: 'AdminEdit'}, title: '完善资料', show: true},
              {to: {name: 'ResetPwd'}, title: '修改密码', show: true},
              {to: {name: 'ManagerUser'}, title: '管理用户', show: false}
            ]
          },
          {
            name: 'blog',
            icon: 'document-text',
            title: '博客相关',
            items: [
              {to: {name: 'AddBlog'}, title: '发表新博文', show: true},
              {to: {name: 'AdminShowBlog'}, title: '博文列表', show: true},
              {to: {name: 'AdminCategoryList'}, title: '博文分类', show: true}
            ]
          },
          {
            name: 'image',
            icon: 'images',
            title: '照片相关',
            items: [
              {to: {name: 'AdminAlbumList'}, title: '查看相册', show: true},
              {to: {name: ''}, title: '新建相册', show: true},
              {to: {name: 'AddPhoto'}, title: '上传图像', show: true}
            ]
          }
        ]
      }
    },
    computed: {
    },
    beforeMount: function () {
      this.$http.get('check', {params: {token: this.$store.state.auth.token}})
        .then((response) => {
        })
        .catch((e) => {
          console.log(e)
          switch (e.response.status) {
            case 404:
              this.$Notice.error({title: '系统无此用户'})
              this.$router.push({ path: '/login' })
              this.$Loading.error()
              break
            case 401:
              this.$Notice.error({title: '登录状态失效，请重新登录'})
              this.$router.push({ path: '/login' })
              this.$Loading.error()
              break
            default :
              this.$Notice.error({title: '未知错误'})
              this.$router.push({ path: '/login' })
              this.$Loading.error()
          }
        })
      this.status = this.$store.state.auth.status
      this.token = this.$store.state.auth.token
      let auth = this.$store.state.auth.authUser
      this.nickname = auth.nickname
      this.avatar_name = auth.avatar
      if (auth.id === 1) this.SideBar[0].items[2].show = true
      if (this.avatar_name !== null && this.avatar_name !== undefined) {
        this.avatar = axios.defaults.baseURL + 'show/img/' + this.avatar_name
      } else {
        this.avatar = null
      }
      document.title = '管理中心' + window.title_suf
    },
    methods: {
      ToHome: function () {
        event.preventDefault()
        this.$Loading.start()
        this.$Loading.finish()
        this.$router.push({ path: '/login' })
      },
      Logout: function () {
        event.preventDefault()
        localStorage.clear()
        this.$store.commit('SET_API_TOKEN', '')
        this.$store.commit('SET_AUTH_USER', {})
        this.$store.commit('SET_STATUS', false)
        this.$router.push('/login')
      }
    }
  }
</script>

<style scoped>
  #admin-index {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    color: #2c3e50;
  }
  .layout{
    border: 1px solid #d7dde4;
    background: #f5f7f9;
    position: relative;
  }
  .layout-menu-left{
    position: fixed;
    height: 100vh;
    background: #464c5b;
    z-index: 999;
  }
  .layout-header{

    height: 60px;
    background: #fff;
    box-shadow: 0 1px 1px rgba(0,0,0,.1);
  }
  .layout-logo-left{
    text-align: center;
    line-height: 30px;
    width: 90%;
    height: 30px;
    background: #5b6270;
    border-radius: 3px;
    margin: 15px auto;
    color: lightblue;
  }
  .avatar {
    margin-top: 10px;
    margin-left: 10px;
  }
  .logout {
    width: 80%;
    margin-left: 10%;
    margin-top: 10px;
  }
  .Nickname {
    font-size: large;
  }
</style>
