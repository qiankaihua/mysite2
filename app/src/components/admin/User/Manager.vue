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
    data: (that = this) => ({
      Users: [],
      loading: false,
      Pagination: {
        Total: 0,
        Current: 1,
        Size: 20
      },
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
          render: (h, params) => {
            let x = 1
            if (x === 1) {
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
                      that.Show()
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
                      that.Edit(params.index)
                    }
                  }
                }, '编辑'),
                h('i-button', {
                  props: {
                    type: 'error',
                    size: 'small'
                  },
                  on: {
                    click: () => {
                      that.Delete(params.index)
                    }
                  }
                }, '删除')
              ])
            } else {
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
                      that.Show()
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
                      that.Edit(params.index)
                    }
                  }
                }, '编辑'),
                h('i-button', {
                  props: {
                    type: 'error',
                    size: 'small'
                  },
                  on: {
                    click: () => {
                      that.Restore(params.index)
                    }
                  }
                }, '恢复')
              ])
            }
          }
        }
      ]
    }),
    created () {
      let page = (this.$route.query.page || '').split(',')
      this.Pagination.Current = +page[0] || 1
      this.Pagination.Size = +page[1] || 10
      console.log('created')
      console.log(page)
      this.GetUser()
    },
    computed: {},
    watch: {
      Pagination: {
        handler (cur, old) {
          let page = this.Pagination.Current + ',' + this.Pagination.Size
          console.log(page)
          this.$router.push({ query: { page } })
          this.GetUser()
        },
        deep: true
      }
    },
    methods: {
      GetUser () {
        console.log(this.Pagination)
        this.loading = true
        let params = {
          offset: this.Pagination.Size * (this.Pagination.Current - 1),
          limit: this.Pagination.Size,
          token: this.$store.state.auth.token
        }
        this.$http.get('show/user/list', { params })
          .then(res => {
            console.log(res)
            this.Pagination.Total = +res.headers['x-total']
            this.Users = res.data
            for (let user of this.Users) {
              user.created_at = moment.unix(user.created_at).format('YYYY-MM-DD HH:mm:ss')
              user.gender = user.gender === 0 ? '男' : '女'
            }
            console.log(this.Users)
            this.loading = false
          })
          .catch(e => {
            this.loading = false
          })
      },
      Show () {
      },
      Edit () {
      },
      Delete () {
      },
      Restore () {
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
