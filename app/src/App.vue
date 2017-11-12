<template>
  <div id="app">
    <router-view></router-view>
    <i-footer></i-footer>
  </div>
</template>

<script>
  import iFooter from './components/Footer'
  export default {
    name: 'app',
    components: {
      iFooter
    },
    data () {
      return {
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
  text-align: center;
  color: #2c3e50;
}
</style>
