<template>
  <div id="user-index">
    <div class="container">
      <Row type="flex" class="sidebar" :class="{'layout-hide-text': Hide === true}">
        <i-col :xs="SpanLeftXS" :sm="SpanLeftSM" :md="SpanLeftMD" :lg="SpanLeftLG" class="layout-menu-left">
          <Menu active-name="2" theme="dark" width="auto" :open-names="['1']">
            <div class="layout-header">
              <i-avatar v-if="avatar" :src="'data:image/png;base64, ' + avatar" size="default" />
              <i-avatar v-else icon="person" size="default" />
              <Button type="text" @click="toggleClick()" class="right-switch-button">
                <Icon type="navicon" color="white" size="32"></Icon>
              </Button>
            </div>
            <MenuItem name="1">
              <Icon type="folder" :size="iconSize"></Icon>
              <span class="layout-text">Categories</span>
            </MenuItem>
            <MenuItem name="2">
              <Icon type="document-text" :size="iconSize"></Icon>
              <span class="layout-text">Blogs</span>
            </MenuItem>
            <MenuItem name="3">
              <Icon type="images" :size="iconSize"></Icon>
              <span class="layout-text">Albums</span>
            </MenuItem>
            <MenuItem name="4">
              <Icon type="image" :size="iconSize"></Icon>
              <span class="layout-text">Photos</span>
            </MenuItem>
            <MenuItem name="5">
              <Icon type="ios-people" :size="iconSize"></Icon>
              <span class="layout-text">Users</span>
            </MenuItem>
            <MenuItem name="6">
              <Icon type="android-settings" :size="iconSize"></Icon>
              <span class="layout-text">Manager</span>
            </MenuItem>
            <MenuItem name="7">
              <Icon type="help" :size="iconSize"></Icon>
              <span class="layout-text">About</span>
            </MenuItem>
            <i-button @click="Logout" class="layout-text logout" type="error">Logout</i-button>
          </Menu>
        </i-col>
        <i-col :xs="24-SpanLeftXS" :sm="24-SpanLeftSM" :md="24-SpanLeftMD" :lg="24-SpanLeftLG" class="under"></i-col>
      </Row>
      <Row class="child-container">
        <i-col :xs="{span: 19, offset: 5 }" :sm="{span: 24-SpanLeftSM, offset: SpanLeftSM}" :md="{span: 24-SpanLeftMD, offset: SpanLeftMD}" :lg="{span: 24-SpanLeftLG, offset: SpanLeftLG}">
          <span>abcdefghijklmnopqrstuvwxyz abcdefghijklmnopqrstuvwxyz abcdefghijklmnopqrstuvwxyz abcdefghijklmnopqrstuvwxyz</span>
        </i-col>
      </Row>
    </div>
  </div>
</template>

<script>
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
        SpanLeftLG: 1,
        status: this.status,
        username: this.username,
        avatar: this.avatar
      }
    },
    beforeMount: function () {
      this.status = sessionStorage.status
      this.token = sessionStorage.token
      this.nickname = sessionStorage.nickname
      if (sessionStorage.avatar_name !== undefined) this.avatar_name = sessionStorage.avatar_name
      else this.avatar_name = null
      this.gender = sessionStorage.gender
      if (this.status !== 'true') {
        this.$router.push({path: '/Login'})
        this.$Loading.error()
      }
      if (this.avatar_name !== null) {
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
    },
    computed: {
      iconSize () {
        return this.spanLeft === 5 ? 14 : 24
      }
    },
    methods: {
      Logout: function () {
        event.preventDefault()
        URL.revokeObjectURL(sessionStorage.avatar)
        sessionStorage.clear()
        this.$router.push('/login')
      },
      toggleClick () {
        this.Hide = !this.Hide
        this.SpanLeftXS = this.SpanLeftXS === 18 ? 5 : 18
        this.SpanLeftSM = this.SpanLeftSM === 5 ? 2 : 5
        this.SpanLeftMD = this.SpanLeftMD === 5 ? 2 : 5
        this.SpanLeftLG = this.SpanLeftLG === 4 ? 1 : 4
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
