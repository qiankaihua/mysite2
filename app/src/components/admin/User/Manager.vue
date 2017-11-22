<template>
  <div class="Manager">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item>Profile</i-breadcrumb-item>
        <i-breadcrumb-item href="/admin/manager">UserList</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        <div class="title">用户管理</div>
        <i-row>
          <i-col :span="24" align="right">
            <i-page
              size="small" show-elevator show-sizer show-total
              :current="Pagination.Current"
              :page-size="Pagination.Size"
              :total="Pagination.Total"
              @on-change="Pagination.Current=$event"
              @on-page-size-change="Pagination.Size=$event"
            ></i-page>
          </i-col>
        </i-row>
        <div class="list">
          <i-table
            border
            :columns="columns"
            :data="Users"
            :loading="loading"
          ></i-table>
        </div>
        <div align="right">
          <i-page
            size="small" show-elevator show-sizer show-total
            :current="Pagination.Current"
            :page-size="Pagination.Size"
            :total="Pagination.Total"
            @on-change="Pagination.Current=$event"
            @on-page-size-change="Pagination.Size=$event"
          ></i-page>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  export default {
    name: 'ManagerUser',
    data () {
      return {
        columns: [
          {
            title: '#',
            key: 'id',
            width: 80
          },
          {
            title: '昵称',
            key: 'nickname'
          },
          {
            title: '性别',
            key: 'gender',
            width: 70
          },
          {
            title: '发布博客',
            key: 'blog',
            width: 90
          },
          {
            title: '上传图片',
            key: 'photos',
            width: 90
          },
          {
            title: '创建时间',
            key: 'created_at',
            align: 'center'
          },
          {
            title: '操作',
            align: 'center',
            width: 200,
            render: (h, params) => {
              return h('div', [
                h('i-button', {
                  props: {
                    type: 'primary',
                    size: 'small'
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      this.userShow(params.index)
                    }
                  }
                }, '查看'),
                h('i-button', {
                  props: {
                    type: 'success',
                    size: 'small'
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      this.userEdit(params.index)
                    }
                  }
                }, '编辑'),
                h('i-button', {
                  props: {
                    type: this.getType(params.index),
                    size: 'small'
                  },
                  on: {
                    click: () => {
                      this.userChange(params.index)
                    }
                  }
                }, this.title(params.index))
              ])
            }
          }
        ],
        Users: [],
        loading: false,
        Pagination: {
          Total: 0,
          Current: 1,
          Size: 20
        }
      }
    },
    created () {
      let page = (this.$route.query.page || '').split(',')
      this.Pagination.Current = +page[0] || 1
      this.Pagination.Size = +page[1] || 10
      this.GetUser()
    },
    computed: {},
    watch: {
      Pagination: {
        handler (cur, old) {
          let page = this.Pagination.Current + ',' + this.Pagination.Size
          this.$router.push({ query: { page } })
        },
        deep: true
      }
    },
    methods: {
      GetUser () {
        this.loading = true
        let params = {
          offset: this.Pagination.Size * (this.Pagination.Current - 1),
          limit: this.Pagination.Size,
          token: this.$store.state.auth.token,
          want_deleted: true
        }
        this.$http.get('show/user/list', { params })
          .then(res => {
            this.Pagination.Total = +res.headers['x-total']
            this.Users = res.data
            for (let user of this.Users) {
              user.created_at = moment.unix(user.created_at).format('YYYY-MM-DD HH:mm:ss')
              user.gender = user.gender === 0 ? '男' : '女'
            }
            this.loading = false
          })
          .catch(e => {
            this.loading = false
          })
      },
      userShow (index) {
        this.$router.push({name: 'AdminShowUser', params: {id: this.Users[index].id}})
      },
      userEdit (index) {
        this.$router.push({name: 'AdminEdit', params: {id: this.Users[index].id}})
      },
      userChange (index) {
        let data = {
          'token': this.$store.state.auth.token
        }
        if (this.Users[index].deleted_at === null) {
          this.$http.delete('user/' + this.Users[index].id, {params: {token: this.$store.state.auth.token}})
            .then(r => {
              this.$Notice.success({title: '删除成功'})
              this.GetUser()
            })
            .catch(e => {
              console.log(e)
              switch (e.response.status) {
                case 401:
                  this.$Notice.error({title: '登录状态失效，请重新登录'})
                  this.$router.push({ path: '/login' })
                  this.$Loading.error()
                  break
                case 403:
                  this.$Notice.error({title: '无操作权限'})
                  this.$Loading.error()
                  break
                default :
                  this.$Notice.error({title: '未知错误'})
                  this.$Loading.error()
              }
            })
        } else {
          this.$http.put('user/' + this.Users[index].id, data)
            .then(r => {
              this.$Notice.success({title: '恢复成功'})
              this.GetUser()
            })
            .catch(e => {
              console.log(e)
              switch (e.response.status) {
                case 401:
                  this.$Notice.error({title: '登录状态失效，请重新登录'})
                  this.$router.push({ path: '/login' })
                  this.$Loading.error()
                  break
                case 403:
                  this.$Notice.error({title: '无操作权限'})
                  this.$Loading.error()
                  break
                default :
                  this.$Notice.error({title: '未知错误'})
                  this.$Loading.error()
              }
            })
        }
      },
      title (index) {
        if (this.Users[index].deleted_at === null) return '删除'
        else return '恢复'
      },
      getType (index) {
        if (this.Users[index].deleted_at === null) return 'error'
        else return 'warning'
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
  .list {
    margin: 10px 0;
  }
</style>
