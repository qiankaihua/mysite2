<template>
  <div class="Edit">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item>Profile</i-breadcrumb-item>
        <i-breadcrumb-item>Edit</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        <div class="title">修改个人资料</div>
        <i-form ref="editValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
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
            <i-col span="12" offset="5">
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
            <i-col span="12" offset="5">
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
            <i-col span="12" offset="5">
              <i-form-item label="性别" prop="gender">
                <i-radio-group v-model="formValidate.gender">
                  <i-radio label="0">Male</i-radio>
                  <i-radio label="1">Female</i-radio>
                </i-radio-group>
              </i-form-item>
            </i-col>
          </i-row>
          <i-row>
            <i-col span="12" offset="5">
              <i-form-item label="出生日期" prop="birthday">
                <i-date-picker type="date" placeholder="Select your birthday" v-model="formValidate.date"></i-date-picker>
              </i-form-item>
            </i-col>
          </i-row>
          <i-row class="buttom">
            <i-col span="4" offset="4">
              <div class="right">头像(暂无预览功能)&nbsp;&nbsp;&nbsp;&nbsp;</div>
            </i-col>
            <i-col span="7">
              <form enctype="multipart/form-data">
                <input id="upload-avatar" type="file" @change="BeforeUpload($event)" />
              </form>
            </i-col>
            <i-col span="9">
              <i-avatar v-if="hasOldAvatar" :src="'data:image/*;base64, ' + oldAvatar" size="large" shape="square" class="avatar" />
              <i-avatar v-else icon="person" size="large" shape="square" class="avatar" />
            </i-col>
          </i-row>
          <i-row class="buttom">
            <i-col span="24" style="text-align: center;">
              <i-button html-type="submit" type="primary" @click="Change(that)">提交修改</i-button>
            </i-col>
          </i-row>
        </i-form>
      </div>
    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  import store from 'store'
  export default {
    components: {
    },
    name: 'AdminEdit',
    data () {
      return {
        that: this,
        hasOldAvatar: null,
        avatar: null,
        oldAvatar: null,
        username: this.username,
        email: this.email,
        token: null,
        avatarname: null,
        formValidate: {
          nickname: '',
          gender: '1',
          birthday: '',
          date: '',
          realname: '',
          rePassword: '',
          phone: ''
        },
        ruleValidate: {
          nickname: [
            {type: 'string', max: 30, message: '昵称长度不能超过30', trigger: 'blur'}
          ],
          gender: [],
          birthday: [],
          phone: [
            {type: 'string', pattern: /^1(3|4|5|7|8)[0-9]{9}$/, message: '请输入正确的11位手机号码', trigger: 'blur'}
          ],
          realname: [
            {type: 'string', max: 30, message: '请输入真实姓名', trigger: 'blur'}
          ]
        }
      }
    },
    computed: {
    },
    beforeMount: function () {
      this.$http.get('show/user/' + this.$route.params.id, {params: {token: this.$store.state.auth.token}})
        .then(response => {
          this.username = response.data.username
          this.email = response.data.email
          this.formValidate.nickname = response.data.nickname
          this.formValidate.realname = response.data.realname
          this.formValidate.phone = response.data.phone
          this.formValidate.gender = response.data.gender
          this.formValidate.date = response.data.birthday === null ? null : moment.unix(response.data.birthday).format('YYYY-MM-DD')
          this.avatar_name = response.data.avatar
          this.nickname = response.data.nickname
          if (this.avatar_name !== null && this.avatar_name !== undefined) {
            this.$http.get('show/img/' + this.avatar_name)
              .then((response) => {
                this.oldAvatar = response.data
                this.hasOldAvatar = true
              })
              .catch((e) => {
                sessionStorage.oldAvatar = null
                this.oldAvatar = null
                this.hasOldAvatar = false
                console.log(e)
              })
          } else {
            this.oldAvatar = null
            this.hasOldAvatar = false
          }
        })
        .catch(e => {
          console.log(e)
        })
      this.status = this.$store.state.auth.status
      this.token = this.$store.state.auth.token
    },
    methods: {
      Change: (that) => {
        event.preventDefault()
        that.$refs.editValidate.validate((valid) => {
          if (valid) {
            that.$Loading.start()
            that.formValidate.birthday = moment(that.formValidate.date).unix()
            console.log(that.formValidate.birthday)
            let formData = new FormData()
            formData.append('username', that.formValidate.name)
            if (that.formValidate.nickname !== '') formData.append('nickname', that.formValidate.nickname)
            formData.append('gender', that.formValidate.gender)
            formData.append('email', that.formValidate.mail)
            if (that.formValidate.phone !== '' && that.formValidate.phone !== null) formData.append('phone', that.formValidate.phone)
            if (that.formValidate.birthday !== '' && !isNaN(that.formValidate.birthday)) formData.append('birthday', that.formValidate.birthday)
            if (that.formValidate.realname !== '') formData.append('realname', that.formValidate.realname)
            if (that.avatar !== null && that.avatar !== undefined) formData.append('avatar', that.avatar)
            let config = {
              headers: {
                'Content-Type': 'multipart/form-data',
                'token': that.token
              }
            }
            console.log(that.formValidate.phone)
            that.$http.post('user/' + that.$route.params.id + '/edit', formData, config)
              .then((response) => {
                console.log(response.data)
                that.$Loading.finish()
                that.$Notice.success({ title: '修改成功' })
                that.$http.get('show/user/' + that.$route.params.id, {params: {token: that.$store.state.auth.token}})
                  .then(response => {
                    let auth = {
                      id: response.data.id,
                      nickname: response.data.nickname,
                      gender: response.data.gender,
                      avatar: response.data.avatar
                    }
                    if (that.$store.state.auth.authUser.id === auth.id) {
                      store.set('authUser', auth)
                      that.$store.commit('SET_AUTH_USER', auth)
                    }
                  })
                  .catch(e => {
                    console.log(e)
                  })
                that.$Loading.finish()
                that.$router.push({ name: 'AdminEdit', params: { id: that.$route.params.id } })
              })
              .catch((e) => {
                that.$Loading.error()
                console.log(e)
                switch (e.response.status) {
                  case 422:
                    that.$Notice.error({title: '请确认输入内容是否满足规范'})
                    break
                  case 401:
                    that.$Notice.error({title: '登录状态失效，请重新登录'})
                    that.$router.push({ path: '/login' })
                    that.$Loading.error()
                    break
                  case 403:
                    that.$Notice.error({title: '无操作权限'})
                    that.$Loading.error()
                    break
                  default :
                    that.$Notice.error({title: '未知错误'})
                    that.$Loading.error()
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
  .right {
    text-align: right;
  }
</style>
