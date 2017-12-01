<template>
  <div class="ShowBlogList">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item>Blog</i-breadcrumb-item>
        <i-breadcrumb-item>ShowList</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        <div class="title">博文管理</div>
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
            :data="Blogs"
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
  import store from 'store'
  export default {
    name: 'AdminBlogList',
    data () {
      return {
        columns: [
          {
            title: '#',
            key: 'show_id',
            width: 80,
            fixed: 'left'
          },
          {
            title: '博客标题',
            key: 'title',
            width: 200
          },
          {
            title: '博客分类',
            key: 'category_title',
            width: 150
          },
          {
            title: 'Star',
            key: 'star',
            width: 90
          },
          {
            title: '标签',
            width: 300,
            render: (h, params) => {
              let tags = this.Blogs[params.index].tag
              let tt = []
              for (let tag of tags) {
                tt.push(
                  h('Tag', {
                    style: {
                    },
                    props: {
//                      type: 'border',
                      color: 'blue'
                    }
                  }, tag)
                )
              }
              return h('div', tt)
            }
          },
          {
            title: '发布时间',
            key: 'created_at',
            align: 'center',
            width: 200
          },
          {
            title: '更新时间',
            key: 'updated_at',
            align: 'center',
            width: 200
          },
          {
            title: '操作',
            align: 'center',
            width: 200,
            fixed: 'right',
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
                      this.blogShow(params.index)
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
                      this.blogEdit(params.index)
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
                      this.blogChange(params.index)
                    }
                  }
                }, this.title(params.index))
              ])
            }
          }
        ],
        Blogs: [],
        loading: false,
        Pagination: {
          Total: 0,
          Current: 1,
          Size: 10
        }
      }
    },
    created () {
      let page = (this.$route.query.page || '').split(',')
      this.Pagination.Current = +page[0] || 1
      this.Pagination.Size = +page[1] || 10
      if (this.$store.state.auth.authUser.id === 1) {
        this.columns.splice(1, 0, {
          title: '发布用户',
          key: 'user_nickname',
          width: 200
        })
        this.columns.splice(1, 0, {
          title: '发布用户ID',
          key: 'user_id',
          width: 100
        })
      }
      this.GetBlog()
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
    beforeMount: function () {
    },
    methods: {
      GetBlog () {
        this.loading = true
        let params = {
          offset: this.Pagination.Size * (this.Pagination.Current - 1),
          limit: this.Pagination.Size,
          token: this.$store.state.auth.token,
          want_deleted: true
        }
        if (this.$store.state.auth.authUser.id !== 1) params['user_id'] = this.$store.state.auth.authUser.id
        this.$http.get('show/blog/list', { params })
          .then(res => {
            this.Pagination.Total = +res.headers['x-total']
            this.Blogs = res.data
            let count = this.Pagination.Size * (this.Pagination.Current - 1)
            for (let blog of this.Blogs) {
              count += 1
              blog.show_id = count
              blog.user_id = blog.user.id
              blog.user_nickname = blog.user.nickname
              blog.blog_tag = ''
              for (let tag of blog.tag) {
                blog.blog_tag = blog.blog_tag + tag + '; '
              }
              if (blog.category === null) blog.category_title = '默认分组'
              else blog.category_title = blog.category.title
              blog.created_at = moment.unix(blog.created_at).format('YYYY-MM-DD HH:mm:ss')
              blog.updated_at = moment.unix(blog.updated_at).format('YYYY-MM-DD HH:mm:ss')
            }
            this.loading = false
          })
          .catch(e => {
            this.loading = false
          })
      },
      blogShow (index) {
        this.$router.push({name: 'AdminShowBlogDetail', params: {id: this.Blogs[index].id}})
      },
      blogEdit (index) {
        this.$http.get('show/blog/' + this.Blogs[index].id, {params: {token: this.$store.state.auth.token}})
          .then(r => {
            store.set('BlogContents', r.data.contents)
            this.$router.push({name: 'AdminEditBlog', params: {id: this.Blogs[index].id}})
          })
          .catch(e => {
            console.log(e)
          })
      },
      blogChange (index) {
        let data = {
          'token': this.$store.state.auth.token
        }
        if (this.Blogs[index].deleted_at === null) {
          this.$http.delete('blog/' + this.Blogs[index].id, {params: {token: this.$store.state.auth.token}})
            .then(r => {
              this.$Notice.success({title: '删除成功'})
              this.GetBlog()
            })
            .catch(e => {
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
          this.$http.put('blog/' + this.Blogs[index].id, data)
            .then(r => {
              this.$Notice.success({title: '恢复成功'})
              this.GetBlog()
            })
            .catch(e => {
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
        if (this.Blogs[index].deleted_at === null) return '删除'
        else return '恢复'
      },
      getType (index) {
        if (this.Blogs[index].deleted_at === null) return 'error'
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
