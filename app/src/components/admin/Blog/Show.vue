<template>
  <div class="ShowBlogList">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item href="/admin/blog/list">Blog</i-breadcrumb-item>
        <i-breadcrumb-item>Show</i-breadcrumb-item>
      </i-breadcrumb>
      <div>
        NO Page!!
      </div>
    </div>

  </div>
</template>

<script>
    export default {
      name: '',
      data: function () {
        return {
        }
      },
      computed: {},
      beforeMount: function () {
      },
      methods: {}
    }
</script>

<style scoped>
  .layout-breadcrumb{
    padding: 10px 15px 0;
  }
  .editorContainer {
    width: 90%;
    height: 80vh;
    margin-left: 5%;
    margin-top: 20px;
    margin-bottom: 20px;
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
</style>
