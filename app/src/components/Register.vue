<template>
  <div class="register">
    <div class="title">Welcome To Register</div>
    <i-form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
      <Row class="buttom" >
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem label="Name" prop="name">
            <Input v-model="formValidate.name" placeholder="Enter your name" />
          </FormItem>
        </i-col>
      </Row>
      <Row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem label="E-mail" prop="mail">
            <Input v-model="formValidate.mail" placeholder="Enter your e-mail" />
          </FormItem>
        </i-col>
      </Row>
      <Row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem label="PassWord" prop="password">
            <Input v-model="formValidate.password" placeholder="Enter your password" type="password" />
          </FormItem>
        </i-col>
      </Row>
      <Row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem label="Nickname" prop="nickname">
            <Input v-model="formValidate.nickname" placeholder="Enter your nickname" />
          </FormItem>
        </i-col>
      </Row>
      <Row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem label="Realname" prop="realname">
            <Input v-model="formValidate.realname" placeholder="Enter your realname" />
          </FormItem>
        </i-col>
      </Row>
      <Row class="buttom">
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem label="Phone" prop="phone">
            <Input v-model="formValidate.phone" placeholder="Enter your mobile phone number" />
          </FormItem>
        </i-col>
      </Row>
      <Row>
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem label="Gender" prop="gender">
            <RadioGroup v-model="formValidate.gender">
              <Radio label="1">Male</Radio>
              <Radio label="0">Female</Radio>
            </RadioGroup>
          </FormItem>
        </i-col>
      </Row>
      <Row>
        <i-col :xs="{span: 18, offset: 3}" :sm="{span: 8, offset: 8}" :md="8" :lg="6" offset="8">
          <FormItem label="Birthday" prop="birthday">
            <DatePicker type="date" placeholder="Select your birthday" v-model="formValidate.date"></DatePicker>
          </FormItem>
        </i-col>
      </Row>
      <Row class="buttom">
        <i-col :xs="{span: 3, offset: 3}" :sm="{span: 4, offset: 6}" :md="{span: 4, offset: 6}" :lg="{span: 2, offset: 7}" offset="8">
          <div class="right">Avatar&nbsp;&nbsp;</div>
        </i-col>
        <i-col :xs="{span: 18}" :sm="{span: 9}" :md="8" :lg="7">
            <form enctype="multipart/form-data">
              <input id="upload-avatar" type="file" @change="BeforeUpload($event)" />
            </form>
        </i-col>
      </Row>
      <Row class="buttom">
        <i-col :xs="{span: 10, offset: 4}" :sm="{span: 3, offset: 9}" :md="3" :lg="2" offset="10">
          <i-button type="ghost" @click="ToLogin($event)">已有账号，登录</i-button>
        </i-col>
        <i-col :xs="6" :sm="5" :md="4" :lg="3">
          <i-button type="primary" @click="Register($event)">&nbsp;&nbsp;&nbsp;注册&nbsp;&nbsp;&nbsp;</i-button>
        </i-col>
      </Row>
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
      return {
        avatar: null,
        formValidate: {
          name: '',
          mail: '',
          password: '',
          nickname: '',
          gender: '1',
          birthday: '',
          date: '',
          realname: '',
          phone: ''
//          avatar: ''
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
          ],
          nickname: [
            { type: 'string', max: 30, message: 'Nickname length must less than 30', trigger: 'blur' }
          ],
          gender: [
          ],
          birthday: [
          ],
          phone: [
            { type: 'string', pattern: /^1(3|4|5|7|8)[0-9]{9}$/, message: 'Please enter right number', trigger: 'blur' }
          ],
          realname: [
            { type: 'string', max: 30, message: 'Please enter right name', trigger: 'blur' }
          ]
        }
      }
    },
    methods: {
      ToLogin () {
        this.$Loading.start()
        event.preventDefault()
        this.$Loading.finish()
        this.$router.push('/login')
      },
      Register () {
        this.$Loading.start()
        event.preventDefault()
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
        if (this.formValidate.birthday !== '') formData.append('birthday', this.formValidate.birthday)
        if (this.formValidate.realname !== '') formData.append('realname', this.formValidate.realname)
        if (this.avatar !== null && this.avatar !== undefined) formData.append('avatar', this.avatar)
        console.log(this.avatar)
        let config = {
          header: {
            'Content-Type': 'multipart/form-data'
          }
        }
        this.$http.post('auth/register', formData, config)
          .then((response) => {
            if (response.status === 200 && response.data[0] === 'success') {
              console.log(response.data)
              this.$Loading.finish()
              this.$router.push('login')
            } else {
              this.$Loading.error()
              alert('未知错误')
            }
          })
          .catch(function (e) {
            alert('用户名或邮箱相同')
            console.log(e)
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















