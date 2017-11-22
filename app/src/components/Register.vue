<template>
  <div class="register">
    <div class="title">Welcome To Register</div>
    <i-form ref="registerValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
      <i-row class="buttom" >
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="用户名" prop="name">
            <i-input v-model="formValidate.name" placeholder="Enter your name">
              <span slot="prepend">
                <i-icon :size="16" type="person"></i-icon>
              </span>
            </i-input>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="电子邮箱" prop="mail">
            <i-input v-model="formValidate.mail" placeholder="Enter your e-mail">
              <span slot="prepend">
                <i-icon :size="16" type="email"></i-icon>
              </span>
            </i-input>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="密码" prop="password">
            <i-input v-model="formValidate.password" placeholder="Enter your password" :type="showPassword === true ? 'text' : 'password'">
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
      <i-row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="确认密码" prop="rePassword">
            <i-input v-model="formValidate.rePassword" placeholder="请再次输入密码" type="password">
              <span slot="prepend">
                <i-icon :size="22" type="key"></i-icon>
              </span>
            </i-input>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="昵称" prop="nickname">
            <i-input v-model="formValidate.nickname" placeholder="Enter your nickname">
              <span slot="prepend">
                <i-icon :size="12" type="heart"></i-icon>
              </span>
            </i-input>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="真实姓名" prop="realname">
            <i-input v-model="formValidate.realname" placeholder="Enter your realname">
              <span slot="prepend">
                <i-icon :size="16" type="person"></i-icon>
              </span>
            </i-input>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="手机号码" prop="phone">
            <i-input v-model="formValidate.phone" placeholder="Enter your mobile phone">
              <span slot="prepend">
                <i-icon :size="16" type="android-phone-portrait"></i-icon>
              </span>
            </i-input>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row>
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="性别" prop="gender">
            <i-radio-group v-model="formValidate.gender">
              <i-radio label="0">Male</i-radio>
              <i-radio label="1">Female</i-radio>
            </i-radio-group>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row>
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <i-form-item label="出生日期" prop="birthday">
            <i-date-picker type="date" placeholder="Select your birthday" v-model="formValidate.date"></i-date-picker>
          </i-form-item>
        </i-col>
      </i-row>
      <i-row class="buttom">
        <i-col :xs="{span: 3, offset: 3}" :sm="{span: 4, offset: 6}" :md="{span: 4, offset: 6}" :lg="{span: 2, offset: 7}" offset="8">
          <div class="right">头像&nbsp;&nbsp;</div>
        </i-col>
        <i-col :xs="{span: 18}" :sm="{span: 9}" :md="8" :lg="7">
            <form enctype="multipart/form-data">
              <input id="upload-avatar" type="file" @change="BeforeUpload($event)" />
            </form>
        </i-col>
      </i-row>
      <i-row class="buttom">
        <i-col :xs="{span: 10, offset: 4}" :sm="{span: 3, offset: 9}" :md="3" :lg="2" offset="10">
          <i-button type="ghost" @click="ToLogin($event)">已有账号，登录</i-button>
        </i-col>
        <i-col :xs="6" :sm="5" :md="4" :lg="3">
          <i-button html-type="submit" type="primary" @click="Register()">&nbsp;&nbsp;&nbsp;注册&nbsp;&nbsp;&nbsp;</i-button>
        </i-col>
      </i-row>
    </i-form>
  </div>
</template>

<script>
  import moment from 'moment'
  export default {
    components: {
    },
    name: 'Register',
    data () {
      const valideRePassword = (rule, value, callback) => {
        if (value !== this.formValidate.password) {
          callback(new Error('两次输入密码不一致'))
        } else {
          callback()
        }
      }
      return {
        showPassword: false,
        avatar: null,
        formValidate: {
          name: '',
          mail: '',
          password: '',
          nickname: '',
          gender: '0',
          birthday: '',
          date: '',
          realname: '',
          rePassword: '',
          phone: ''
//          avatar: ''
        },
        ruleValidate: {
          name: [
            { required: true, message: '用户名不能为空', trigger: 'blur' },
            { pattern: /^[a-zA-Z][a-zA-Z0-9_]{5,19}$/, message: '用户名只能由6-20位的字母,数字,下划线组成', trigger: 'blur' },
            { pattern: /^[a-zA-Z]/, message: '用户名只能由字母开头', trigger: 'blur' }
          ],
          mail: [
            { required: true, message: '邮箱不能为空', trigger: 'blur' },
            { type: 'email', message: '不合法的邮箱格式', trigger: 'blur' }
          ],
          password: [
            { required: true, message: '密码不能为空', trigger: 'blur' },
            { pattern: /^[\x21-\x7e]{6,20}$/, message: '密码长度必须在6到20位之间', trigger: 'blur' },
            { pattern: /^[a-zA-Z]/, message: '密码必须由字母开头', trigger: 'blur' }
          ],
          rePassword: [
            { required: true, message: '重复密码不能为空', trigger: 'blur' },
            { validator: valideRePassword, trigger: 'blur' }
          ],
          nickname: [
            { type: 'string', max: 30, message: '昵称长度不能超过30', trigger: 'blur' }
          ],
          gender: [
          ],
          birthday: [
          ],
          phone: [
            { type: 'string', pattern: /^1(3|4|5|7|8)[0-9]{9}$/, message: '请输入正确的11位手机号码', trigger: 'blur' }
          ],
          realname: [
            { type: 'string', max: 30, message: '请输入真实姓名', trigger: 'blur' }
          ]
        }
      }
    },
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
      document.title = '注册' + window.title_suf
    },
    methods: {
      ToLogin () {
        this.$Loading.start()
        this.$Loading.finish()
        this.$router.push('/login')
      },
      Register () {
        event.preventDefault()
        this.$refs.registerValidate.validate((valid) => {
          if (valid) {
            this.$Loading.start()
            this.formValidate.birthday = moment(this.formValidate.date).unix()
            console.log(this.formValidate.birthday)
            let formData = new FormData()
            formData.append('username', this.formValidate.name)
            let createHash = require('create-hash')
            let hash = createHash('sha1')
            hash.update(this.formValidate.password)
            this.formValidate.password = hash.digest('HEX')
            formData.append('password', this.formValidate.password)
            if (this.formValidate.nickname !== '') formData.append('nickname', this.formValidate.nickname)
            formData.append('gender', this.formValidate.gender)
            formData.append('email', this.formValidate.mail)
            if (this.formValidate.phone !== '') formData.append('phone', this.formValidate.phone)
            console.log(this.formValidate.phone)
            if (this.formValidate.birthday !== '' && !isNaN(this.formValidate.birthday)) formData.append('birthday', this.formValidate.birthday)
            if (this.formValidate.realname !== '') formData.append('realname', this.formValidate.realname)
            if (this.avatar !== null && this.avatar !== undefined) formData.append('avatar', this.avatar)
            console.log(this.avatar)
            let config = {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }
            this.$http.post('auth/register', formData, config)
              .then((response) => {
                console.log(response.data)
                this.$Loading.finish()
                this.$router.push('login')
              })
              .catch((e) => {
                this.formValidate.password = ''
                this.formValidate.rePassword = ''
                this.$Loading.error()
                console.log(e)
                switch (e.response.status) {
                  case 422:
                    this.$Notice.error({title: '用户名或邮箱已被使用'})
                    break
                  default :
                    this.$Notice.error({title: '未知错误'})
                }
              })
          }
        })
      },
      BeforeUpload (event) {
        this.avatar = event.target.files[0]
        console.log(this.avatar)
        return false
      }
    }
  }
</script>

<style scoped>
  .register {
    min-height: calc(100vh - 100px);
  }
  .title {
    height: 20vh;
    line-height: 20vh;
    text-align: center;
    font-size: x-large;
  }
  .buttom {
    margin-bottom: 20px;
  }
  .right {
    text-align: right;
  }
</style>















