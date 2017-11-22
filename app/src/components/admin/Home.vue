<template>
  <div class="Home">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item>Home</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        Welcome To Admin Home!! <br>
        <span class="Nickname">{{nickname}}</span><br>
        管理页面暂不支持手机端访问，请使用电脑或平板来浏览！！
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'AdminHome',
    components: {
    },
    data: () => ({
      status: this.status,
      username: this.username
    }),
    beforeMount: function () {
      this.status = this.$store.state.auth.status
      this.token = this.$store.state.auth.token
      let auth = this.$store.state.auth.authUser
      this.nickname = auth.nickname
    },
    methods: {
    }
  }
</script>

<style scoped>
  .layout-breadcrumb{
    padding: 10px 15px 0;
  }
  .layout-content{
    min-height: 200px;
    margin: 15px;
    overflow: hidden;
    background: #fff;
    border-radius: 4px;
  }
  .layout-content-main{
    padding: 10px;
    min-height: 100vh;
  }
  .Nickname {
    font-style: italic;
    font-size: x-large;
  }
</style>
