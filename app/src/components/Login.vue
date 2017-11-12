<template>
  <div class="login">
    <div class="title">Welcome To Login</div>
    <i-form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
      <Row class="top" >
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem v-if="flag" label="Name" prop="name">
            <Input v-model="formValidate.name" placeholder="Enter your name" />
          </FormItem>
          <FormItem v-else label="E-mail" prop="mail">
            <Input v-model="formValidate.mail" placeholder="Enter your e-mail" />
          </FormItem>
        </i-col>
      </Row>
      <Row class="top">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem label="PassWord" prop="password">
            <Input v-model="formValidate.password" placeholder="Enter your password" type="password" />
          </FormItem>
        </i-col>
      </Row>
      <Row>
        <i-col :xs="{span: 10, offset: 1}" :sm="{span: 3, offset: 8}" :md="3" :lg="2" offset="8">
          <i-button v-if="flag" type="ghost" @click="switchLogin($event)">使用邮箱登录</i-button>
          <i-button v-else type="ghost" @click="switchLogin($event)">使用账号登录</i-button>
        </i-col>
        <i-col :xs="6" :sm="3" :md="3" :lg="2">
          <i-button type="primary" @click="Login($event)">登录</i-button>
        </i-col>
        <i-col :xs="6" :sm="3" :md="3" :lg="2">
          <i-button type="ghost" @click="ToRegister($event)">注册</i-button>
        </i-col>
      </Row>
    </i-form>
  </div>
</template>

<script>
  export default {
    name: 'login',
    components: {
    },
    data () {
      return {
        status: 'false',
        flag: true,
        State: [],
        formValidate: {
          name: '',
          mail: '',
          password: ''
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
      }
    },
    beforeMount: function () {
      this.status = sessionStorage.status
      if (this.status === 'true') {
        this.$router.push({ path: '/home' })
      }
    },
    methods: {
      switchLogin () {
        event.preventDefault()
        this.flag = !this.flag
      },
      Login () {
        event.preventDefault()
        let formData = new FormData()
        if (this.flag) {
          formData.append('username', this.formValidate.name)
        } else formData.append('email', this.formValidate.mail)
        let createHash = require('create-hash')
        let hash = createHash('sha1')
        hash.update(this.formValidate.password)
        this.formValidate.password = hash.digest('HEX')
        formData.append('password', this.formValidate.password)
        let config = {
          header: {
            'Content-Type': 'multipart/form-data'
          }
        }
        this.$http.post('auth/login', formData, config)
          .then((response) => {
            this.State = response.data
            sessionStorage.status = 'true'
            this.status = sessionStorage.status
            sessionStorage.token = this.State.token
            this.token = sessionStorage.token
            sessionStorage.gender = this.State.gender
            this.gender = sessionStorage.gender
            sessionStorage.nickname = this.State.nickname
            this.nickname = sessionStorage.nickname
            sessionStorage.avatar_name = this.State.avatar
            this.avatar_name = sessionStorage.avatar_name
            if (response.status === 200) {
              this.$Loading.finish()
              this.$router.push({ path: '/home' })
            } else {
              this.$Loading.error()
              alert('未知错误')
            }
          })
          .catch(function (e) {
            alert('密码错误或是该用户未注册，请重新输入')
            console.log(e)
          })
      },
      ToRegister () {
        this.$Loading.start()
        event.preventDefault()
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
</style>
