<template>
  <div class="login">
    <div class="title">Welcome To Login</div>
    <i-form ref="loginValidate" :model="formValidate" :rules="ruleValidate" :label-width="80" onsubmit="return false">
      <i-row class="top" >
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item v-if="flag" label="Name" prop="name">
            <i-input v-model="formValidate.name" placeholder="Enter your name">
              <span slot="prepend">
                <i-icon :size="16" type="person"></i-icon>
              </span>
            </i-input>
          </i-form-item>
          <i-form-item v-else label="E-mail" prop="mail">
            <i-input v-model="formValidate.mail" placeholder="Enter your e-mail">
              <span slot="prepend">
                <i-icon :size="16" type="email"></i-icon>
              </span>
            </i-input>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row class="top">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="PassWord" prop="password">
            <i-input v-model="formValidate.password" placeholder="Enter your password" :type="showPassword ? 'text' : 'password'">
              <span slot="prepend">
                <i-icon :size="16" type="locked"></i-icon>
              </span>
              <span slot="append">
                <i-button style="padding: 3px 10px" @click="showPassword = !showPassword">
                  <i-icon :size="18" style="color:#2D8CF0" type="eye" v-if="showPassword"></i-icon>
                  <i-icon :size="18" type="eye" v-else></i-icon>
                </i-button>
              </span>
            </i-input>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row class="top">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item prop="remember">
            <i-checkbox v-model="formValidate.remember">记住我</i-checkbox>
            <span style="color:grey">请不要在公共电脑勾选此项</span>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row>
        <i-col :xs="{span: 10, offset: 1}" :sm="{span: 3, offset: 8}" :md="3" :lg="2" offset="8">
          <i-button v-if="flag" type="ghost" @click="switchLogin()">使用邮箱登录</i-button>
          <i-button v-else type="ghost" @click="switchLogin()">使用账号登录</i-button>
        </i-col>
        <i-col :xs="6" :sm="3" :md="3" :lg="2">
          <i-button html-type="submit" type="primary" @click="Login()">登录</i-button>
        </i-col>
        <i-col :xs="6" :sm="3" :md="3" :lg="2">
          <i-button type="ghost" @click="ToRegister()">立即注册</i-button>
        </i-col>
      </i-row>
    </i-form>
    <i-row class="Home">
      <i-col span="24">
        <i-button @click="ToHome">View without Login!!!</i-button>
      </i-col>
    </i-row>
  </div>
</template>

<script>
  import store from 'store'
  import iView from 'iview'
  export default {
    name: 'login',
    components: {
    },
    data: () => ({
      status: 'false',
      showPassword: false,
      flag: true,
      State: [],
      formValidate: {
        name: '',
        mail: '',
        password: '',
        remember: false
      },
      ruleValidate: {
        name: [
          { required: true, message: 'The name cannot be empty', trigger: 'blur' },
          { pattern: /^[a-zA-Z][a-zA-Z0-9_]{5,19}$/, message: 'Name length must between 6 and 20 with letter, number and underline(_)', trigger: 'blur' },
          { pattern: /^[a-zA-Z]/, message: 'Name must begin with letter', trigger: 'blur' }
        ],
        mail: [
          { required: true, message: 'Mailbox cannot be empty', trigger: 'blur' },
          { type: 'email', message: 'Incorrect email format', trigger: 'blur' }
        ],
        password: [
          { required: true, message: 'Password cannot be empty', trigger: 'blur' },
          { pattern: /^[\x21-\x7e]{6,20}$/, message: 'Password length must between 6 and 20', trigger: 'blur' },
          { pattern: /^[a-zA-Z]/, message: 'Password must begin with letter', trigger: 'blur' }
        ]
      }
    }),
    beforeMount: function () {
      this.$http.get('check', {params: {token: this.$store.state.auth.token}})
        .then((response) => {
          this.$Notice.success({title: '登陆成功'})
          this.$router.push({ path: '/home' })
          this.$Loading.finish()
        })
        .catch((e) => {
          console.log(e)
        })
      document.title = '登录' + window.title_suf
    },
    methods: {
      switchLogin () {
        this.flag = !this.flag
      },
      Login () {
        event.preventDefault()
        this.$refs.loginValidate.validate((valid) => {
          if (valid) {
            this.$Loading.start()
            let formData = new FormData()
            if (this.flag) {
              formData.append('username', this.formValidate.name)
            } else formData.append('email', this.formValidate.mail)
            let createHash = require('create-hash')
            let hash = createHash('sha1')
            hash.update(this.formValidate.password)
            this.formValidate.password = hash.digest('HEX')
            formData.append('password', this.formValidate.password)
            formData.append('remember', this.formValidate.remember)
            let config = {
              header: {
                'Content-Type': 'multipart/form-data'
              }
            }
            this.$http.post('auth/login', formData, config)
              .then(response => {
                let auth = {
                  id: response.data.id,
                  nickname: response.data.nickname,
                  gender: response.data.gender,
                  avatar: response.data.avatar
                }
                localStorage.clear()
                store.set('token', response.data.token)
                store.set('authUser', auth)
                store.set('status', true)
                this.$store.commit('SET_API_TOKEN', response.data.token)
                this.$store.commit('SET_AUTH_USER', auth)
                this.$store.commit('SET_STATUS', true)
                this.$Loading.finish()
                this.$Notice.success({ title: '登录成功' })
                this.$router.push({ path: '/home' })
              })
              .catch(e => {
                iView.LoadingBar.error()
                switch (e.response.status) {
                  case 401:
                    if (this.flag === true) iView.Notice.error({title: '用户名或密码错误'})
                    else iView.Notice.error({title: '邮箱或密码错误'})
                    break
                  case 404:
                    if (!this.flag) iView.Notice.error({title: '该邮箱未注册'})
                    else iView.Notice.error({title: '该用户名未注册'})
                }
                this.formValidate.password = ''
                console.log(e)
              })
          }
        })
      },
      ToHome () {
        this.$Loading.start()
        this.$Loading.finish()
        this.$router.push({ path: '/home' })
      },
      ToRegister () {
        this.$Loading.start()
        this.$Loading.finish()
        this.$router.push({ path: '/register' })
      }
    }
  }
</script>

<style scoped>
  .login {
    min-height: calc(100vh - 100px);
  }
  .top {
    margin-bottom: 20px;
  }
  .title {
    height: 20vh;
    line-height: 20vh;
    text-align: center;
    font-size: x-large;
  }
  .Home {
    margin-top: 10px;
    text-align: center;
  }
</style>
