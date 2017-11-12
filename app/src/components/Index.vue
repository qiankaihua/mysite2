<template>
  <div class="Index"></div>
</template>

<script>
  export default {
    name: 'NavBar',
    data () {
      return {
        status: this.status,
        username: this.username,
        avatar: this.avatar
      }
    },
    Mounted: function () {
      this.status = sessionStorage.status
      this.username = sessionStorage.username
      this.avatar = sessionStorage.avatar
    },
    methods: {
    }
  }
</script>

<style scoped>

</style>
