<template>
  <div id="app">
    <router-view></router-view>
    <i-footer :key="key"></i-footer>
  </div>
</template>

<script>
  import iFooter from './components/Footer'
  export default {
    name: 'app',
    components: {
      iFooter
    },
    beforeMount: function () {
      this.$store.commit('SET_API_TOKEN', localStorage.token === undefined ? '' : JSON.parse(localStorage.token))
      this.$store.commit('SET_AUTH_USER', localStorage.authUser === undefined ? {} : JSON.parse(localStorage.authUser))
      this.$store.commit('SET_STATUS', localStorage.status === undefined ? false : JSON.parse(localStorage.status))
    },
    data () {
      return {
      }
    },
    computed: {
      key () {
        // add key to <router-view> to avoid re-use component
        console.log('re-generate key')
        return this.$route.name !== undefined
          ? this.$route.name + +new Date()
          : this.$route + +new Date()
      }
    },
    methods: {
    }
  }
</script>

<style scoped>
#app {
  background-color: #f0f0f0;
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}
</style>
