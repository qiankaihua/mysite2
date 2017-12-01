<template>
  <div class="ResetPwd">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item>Profile</i-breadcrumb-item>
        <i-breadcrumb-item>Reset</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        <div class="title">修改密码</div>
        <i-form ref="resetValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
          <i-row class="buttom" >
            <i-col span="12" offset="5">
              <i-form-item label="用户名" prop="name">
                <i-input v-model="username" placeholder="Enter your name" disabled>
              <span slot="prepend">
                <i-icon :size="16" type="person"></i-icon>
              </span>
                </i-input>
              </i-form-item>
            </i-col>
          </i-row>
          <i-row class="buttom">
            <i-col span="12" offset="5">
              <i-form-item label="电子邮箱" prop="email">
                <i-input v-model="email" placeholder="Enter your e-mail" disabled>
              <span slot="prepend">
                <i-icon :size="16" type="email"></i-icon>
              </span>
                </i-input>
              </i-form-item>
            </i-col>
          </i-row>
          <i-row class="buttom">
            <i-col span="12" offset="5">
              <i-form-item label="原密码" prop="password_old">
                <i-input v-model="formValidate.password_old" placeholder="Enter your old password" :type="showOldPassword === true ? 'text' : 'password'">
              <span slot="prepend">
                <i-icon :size="16" type="locked"></i-icon>
              </span>
                  <span slot="append">
                <i-button style="padding: 3px 10px" @click="showOldPassword = !showOldPassword">
                  <i-icon :size="18" style="color:#2D8CF0" type="eye" v-if="showOldPassword"></i-icon>
                  <i-icon :size="18" type="eye" v-else></i-icon>
                </i-button>
              </span>
                </i-input>
              </i-form-item>
            </i-col>
          </i-row>
          <i-row class="buttom">
            <i-col span="12" offset="5">
              <i-form-item label="新密码" prop="password_new">
                <i-input v-model="formValidate.password_new" placeholder="Enter your new password" :type="showNewPassword === true ? 'text' : 'password'">
              <span slot="prepend">
                <i-icon :size="16" type="locked"></i-icon>
              </span>
                  <span slot="append">
                <i-button style="padding: 3px 10px" @click="showNewPassword = !showNewPassword">
                  <i-icon :size="18" style="color:#2D8CF0" type="eye" v-if="showNewPassword"></i-icon>
                  <i-icon :size="18" type="eye" v-else></i-icon>
                </i-button>
              </span>
                </i-input>
              </i-form-item>
            </i-col>
          </i-row>
          <i-row class="buttom">
            <i-col span="12" offset="5">
              <i-form-item label="确认密码" prop="rePassword">
                <i-input v-model="formValidate.rePassword" placeholder="Enter your new password again" type="password">
              <span slot="prepend">
                <i-icon :size="22" type="key"></i-icon>
              </span>
                </i-input>
              </i-form-item>
            </i-col>
          </i-row>
          <i-row class="buttom">
            <i-col span="24" style="text-align: center;">
              <i-button html-type="submit" type="primary" @click="Reset">修改密码</i-button>
            </i-col>
          </i-row>
        </i-form>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'ResetPwd',
    data () {
      const valideRePassword = (rule, value, callback) => {
        if (value !== this.formValidate.password_new) {
          callback(new Error('两次输入密码不一致'))
        } else {
          callback()
        }
      }
      const valideNotSame = (rule, value, callback) => {
        if (value === this.formValidate.password_old) {
          callback(new Error('新密码不能与原密码相同'))
        } else {
          callback()
        }
      }
      return {
        username: '',
        email: '',
        token: '',
        user_id: '',
        showOldPassword: false,
        showNewPassword: false,
        avatar: null,
        formValidate: {
          password_new: '',
          password_old: '',
          rePassword: ''
        },
        ruleValidate: {
          password_old: [
            {required: true, message: '原密码不能为空', trigger: 'blur'},
            {pattern: /^[\x21-\x7e]{6,20}$/, message: '原密码长度必须在6到20位之间', trigger: 'blur'},
            {pattern: /^[a-zA-Z]/, message: '原密码必须由字母开头', trigger: 'blur'}
          ],
          password_new: [
            {required: true, message: '新密码不能为空', trigger: 'blur'},
            {pattern: /^[\x21-\x7e]{6,20}$/, message: '新密码长度必须在6到20位之间', trigger: 'blur'},
            {pattern: /^[a-zA-Z]/, message: '新密码必须由字母开头', trigger: 'blur'},
            {validator: valideNotSame, trigger: 'blur'}
          ],
          rePassword: [
            {required: true, message: '重复密码不能为空', trigger: 'blur'},
            {validator: valideRePassword, trigger: 'blur'}
          ]
        }
      }
    },
    computed: {},
    beforeMount: function () {
      this.$http.get('show/user/' + this.$store.state.auth.authUser.id, {params: {token: this.$store.state.auth.token}})
        .then(response => {
          this.username = response.data.username
          this.email = response.data.email
        })
        .catch(e => {
          console.log(e)
        })
      this.status = this.$store.state.auth.status
      this.token = this.$store.state.auth.token
      this.user_id = this.$store.state.auth.authUser.id
    },
    methods: {
      Reset () {
        event.preventDefault()
        this.$refs.resetValidate.validate((valid) => {
          if (valid) {
            this.$Loading.start()
            let createHash = require('create-hash')
            let hash = createHash('sha1')
            hash.update(this.formValidate.password_old)
            this.formValidate.password_old = hash.digest('HEX')
            let createHash2 = require('create-hash')
            let hash2 = createHash2('sha1')
            hash2.update(this.formValidate.password_new)
            this.formValidate.password_new = hash2.digest('HEX')
            this.$http.put('user/' + this.user_id + '/security', {'token': this.token, 'password_old': this.formValidate.password_old, 'password_new': this.formValidate.password_new})
              .then(response => {
                localStorage.clear()
                this.$store.commit('SET_API_TOKEN', '')
                this.$store.commit('SET_AUTH_USER', {})
                this.$store.commit('SET_STATUS', false)
                this.$Notice.success({title: '修改成功'})
                this.$Loading.finish()
                this.$router.push({ 'name': 'Login' })
              })
              .catch(e => {
                this.$Loading.error()
                switch (e.response.status) {
                  case 404:
                    this.$Notice.error({title: '系统无此用户'})
                    this.$router.push({ path: '/login' })
                    this.$Loading.error()
                    break
                  case 422:
                    this.$Notice.error({title: '登录状态失效，请重新登录'})
                    this.$router.push({ path: '/login' })
                    this.$Loading.error()
                    break
                  case 403:
                    this.$Notice.error({title: '没有修改权限'})
                    this.$router.push({ path: '/login' })
                    this.$Loading.error()
                    break
                  default :
                    this.$Notice.error({title: '未知错误'})
                    this.$router.push({ path: '/login' })
                    this.$Loading.error()
                }
              })
          }
        })
      }
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
  .buttom {
    margin-bottom: 20px;
  }
</style>
