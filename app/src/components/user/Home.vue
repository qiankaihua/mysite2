<template>
  <div id="user-index">
    <div class="container">
      <i-row type="flex" class="sidebar" :class="{'layout-hide-text': Hide === true}">
        <i-col :xs="SpanLeftXS" :sm="SpanLeftSM" :md="SpanLeftMD" :lg="SpanLeftLG" class="layout-menu-left">
          <i-menu active-name="2" theme="dark" width="auto" :open-names="['1']" @on-select="$router.push({ name: $event })">
            <div class="layout-header">
              <i-avatar v-if="avatar" :src="'data:image/*;base64, ' + avatar" size="default" />
              <i-avatar v-else icon="person" size="default" />
              <i-button type="text" @click="toggleClick()" class="right-switch-button">
                <i-icon type="navicon" color="white" size="32"></i-icon>
              </i-button>
            </div>
            <i-menu-item name="1">
              <i-icon type="folder" :size="iconSize"></i-icon>
              <span class="layout-text">Categories</span>
            </i-menu-item>
            <i-menu-item name="2">
              <i-icon type="document-text" :size="iconSize"></i-icon>
              <span class="layout-text">Blogs</span>
            </i-menu-item>
            <i-menu-item name="3">
              <i-icon type="images" :size="iconSize"></i-icon>
              <span class="layout-text">Albums</span>
            </i-menu-item>
            <i-menu-item name="4">
              <i-icon type="image" :size="iconSize"></i-icon>
              <span class="layout-text">Photos</span>
            </i-menu-item>
            <i-menu-item name="5">
              <i-icon type="ios-people" :size="iconSize"></i-icon>
              <span class="layout-text">Users</span>
            </i-menu-item>
            <i-menu-item name="AdminHome" >
              <i-icon type="android-settings" :size="iconSize"></i-icon>
              <span class="layout-text">Manager</span>
            </i-menu-item>
            <i-menu-item name="7">
              <i-icon type="help" :size="iconSize"></i-icon>
              <span class="layout-text">About</span>
            </i-menu-item>
            <i-button v-if="status" @click="Logout" class="layout-text logout" type="error">Logout</i-button>
            <i-button v-else @click="ToLogin" class="layout-text logout" type="info">Login</i-button>
          </i-menu>
        </i-col>
        <i-col :xs="24-SpanLeftXS" :sm="24-SpanLeftSM" :md="24-SpanLeftMD" :lg="24-SpanLeftLG" class="under"></i-col>
      </i-row>
      <i-row class="child-container">
        <i-col :xs="{span: 19, offset: 5 }" :sm="{span: 24-SpanLeftSM, offset: SpanLeftSM}" :md="{span: 24-SpanLeftMD, offset: SpanLeftMD}" :lg="{span: 24-SpanLeftLG, offset: SpanLeftLG}">
          <span>abcdefghijklmnopqrstuvwxyz abcdefghijklmnopqrstuvwxyz abcdefghijklmnopqrstuvwxyz abcdefghijklmnopqrstuvwxyz</span>
        </i-col>
      </i-row>
    </div>
    <i-back-top :height="20" :bottom="50"></i-back-top>
  </div>
</template>

<script>
  import store from 'store'
  export default {
    name: 'userHome',
    components: {
    },
    data () {
      return {
        Hide: true,
        SpanLeftXS: 5,
        SpanLeftSM: 2,
        SpanLeftMD: 2,
        SpanLeftLG: 2,
        status: true, // this.status,
        username: this.username,
        avatar: this.avatar
      }
    },
    beforeMount: function () {
      this.$http.get('addtime', {params: {token: this.$store.state.auth.token}})
        .then((response) => {
          if (response.data === false) {
            this.status = false
            store.set('status', false)
            this.$store.commit('SET_STATUS', false)
          } else {
            this.status = true
            this.$store.commit('SET_STATUS', true)
            store.set('status', true)
            this.token = this.$store.state.auth.token
            let auth = this.$store.state.auth.authUser
            this.nickname = auth.nickname
            this.avatar_name = auth.avatar
            if (this.avatar_name != null) {
              this.$http.get('show/img/' + this.avatar_name)
                .then((response) => {
                  this.avatar = response.data
                })
                .catch(function (e) {
                  sessionStorage.avatar = null
                  this.avatar = null
                  console.log(e)
                })
            } else {
              this.avatar = null
            }
          }
        })
        .catch((e) => {
          this.status = false
        })
      document.title = '首页' + window.title_suf
    },
    computed: {
      iconSize () {
        return this.spanLeft === 5 ? 14 : 24
      }
    },
    methods: {
      Logout: function () {
        event.preventDefault()
        localStorage.clear()
        this.$store.commit('SET_API_TOKEN', '')
        this.$store.commit('SET_AUTH_USER', {})
        this.$store.commit('SET_STATUS', false)
        this.$router.push('/login')
      },
      ToLogin () {
        this.$Loading.start()
        this.$Loading.finish()
        this.$router.push('/login')
      },
      toggleClick () {
        this.Hide = !this.Hide
        this.SpanLeftXS = this.SpanLeftXS === 18 ? 5 : 18
        this.SpanLeftSM = this.SpanLeftSM === 5 ? 2 : 5
        this.SpanLeftMD = this.SpanLeftMD === 5 ? 2 : 5
        this.SpanLeftLG = this.SpanLeftLG === 4 ? 2 : 4
      }
    }
  }
</script>

<style scoped>
  #user-index {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    z-index: auto;
  }
  .container {
    top: 0;
    left: 0;
    right: 0;
    min-height: 100vh;
    z-index: 999;
  }
  .sidebar {
    overflow: hidden;
    text-align: left;
  }
  .layout-menu-left{
    transition:width 0.5s;
    z-index: 999;
    background: #464c5b;
    min-height:100vh;
    position: fixed;
  }
  .layout-header {
    color: gray;
  }
  .right-switch-button {
    float: right;
    text-align: center;
    border: none;
    padding: 5px;
  }
  .layout-text {
    overflow: hidden;
    position: absolute;
  }
  .layout-hide-text .layout-text {
    display: none;
  }
  .under {
    z-index: -1;
    background-color: #000;
  }
  .logout {
    width: 80%;
    margin-left: 10%;
    margin-top: 10px;
  }
  .child-container {
    word-wrap: break-word;
    text-align: left;
  }
</style>
