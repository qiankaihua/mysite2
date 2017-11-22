<template>
  <div class="Index">
    <i-row>
      <i-col :xs="{span: 5, offset: 13}" :sm="{span: 2, offset: 19}" :md="{span: 2, offset: 20}" :lg="{span: 2, offset: 20}">
        <i-button type="primary" class="width:100%" @click="ToLogin()">Login</i-button>
      </i-col>
      <i-col :xs="{span: 6}" :sm="{span: 3}" :md="{span: 2}" :lg="{span: 2}">
        <i-button type="primary" class="width:100%" @click="ToRegister()">Register</i-button>
      </i-col>
    </i-row>
    <i-row class="main-head">
      <i-col span="24">To Be Continue...</i-col>
    </i-row>
    <i-row>
      <i-col class="home" span="24">
        <i-button class="home" type="info" @click="ToHome">Click Me To Go Home</i-button>
      </i-col>
    </i-row>
  </div>
</template>

<script>
  export default {
    components: {
    },
    data () {
      return {
      }
    },
    methods: {
      ToLogin () {
        this.$Loading.start()
        this.$Loading.finish()
        this.$router.push('/login')
      },
      ToRegister () {
        this.$Loading.start()
        this.$Loading.finish()
        this.$router.push({ path: '/register' })
      },
      ToHome () {
        this.$Loading.start()
        this.$Loading.finish()
        this.$router.push({ name: 'Home' })
      }
    }
  }
</script>

<style scoped>
  .Index {
    background-image: url(../assets/1.jpeg);
    height: 100vh;
    width: 100%;
    background-size: cover;
    background-position: center;
  }
  /*@media screen and (min-width: 768px) {*/
    /*.main-head {*/
      /*font-size: 50px !important;*/
    /*}*/
  /*}*/
  .home {
    text-align: center;
  }
  .main-head {
    text-align: center;
    margin-top: 12vh;
    font-size: 5vh;
    height: 45vh;
    /*vertical-align: middle;*/
    line-height: 45vh;
    color: #6A5ACD;
  }
</style>
