<template>
  <div class="OtherUserDetail">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item>Profile</i-breadcrumb-item>
        <i-breadcrumb-item href="/admin/manager">UserList</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        <div class="title">待更新</div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'OtherUserDetail',
    data: () => ({}),
    computed: {},
    methods: {}
  }
</script>

<style scoped>
  .OtherUserDetail {
  }
  .layout-breadcrumb{
    padding: 10px 15px 0;
  }
  .layout-content{
    min-height: 200px;
    margin: 15px;
    background: #fff;
    border-radius: 4px;
  }
  .layout-content-main{
    padding: 10px;
    min-height: 100vh;
  }
  .title {
    height: 10vh;
    line-height: 10vh;
    text-align: center;
    font-size: x-large;
  }
  .list {
    margin: 10px 0;
  }
</style>
