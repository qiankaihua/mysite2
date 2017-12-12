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
          <i-col :span="6" v-if="isAdmin">
            <i-select v-model="user_id" class="select-user" @on-change="ChangeUser">
              <i-option v-for="item in users" :value="item.user_id" :key="item.user_id" :label="item.nickname">
                <span>{{ item.nickname }}</span>
                <span class="blog-sum">{{ item.blog_sum }}</span>
              </i-option>
            </i-select>
          </i-col>
          <i-col :span="6">
            <i-select v-model="category_id" class="select-user" @on-change="ChangeCategory" :disabled="disable">
              <i-option v-for="item in category" :value="item.category_id" :key="item.category_id" :label="item.title">
                <span>{{ item.title }}</span>
                <span class="blog-sum">{{ item.blog_sum }}</span>
              </i-option>
            </i-select>
          </i-col>
        </i-row>
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
        isAdmin: false,
        disable: true,
        user_id: 0,
        users: [],
        category_id: -1,
        category: [],
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
      this.user_id = localStorage.userCategory === undefined ? this.$store.state.auth.authUser.id : JSON.parse(localStorage.userCategory)
      this.GetCategory()
      this.category_id = localStorage.blogCategory === undefined ? -1 : JSON.parse(localStorage.blogCategory)
      if (this.$store.state.auth.authUser.id === 1) {
        this.GetUser()
        this.isAdmin = true
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
    methods: {
      GetUser: function () {
        let params = {
          token: this.$store.state.auth.token,
          want_deleted: true
        }
        this.users.push({
          user_id: 0,
          nickname: '查看全部',
          blog_sum: null
        })
        this.$http.get('show/user/list', { params })
          .then(res => {
            let Users = res.data
            for (let user of Users) {
              this.users.push({
                nickname: user.nickname,
                blog_sum: user.blog,
                user_id: user.id
              })
            }
          })
          .catch(e => {
            console.log(e)
          })
      },
      GetCategory: function () {
        this.category = []
        this.category.push({
          category_id: -1,
          title: '查看全部',
          blog_sum: null
        })
        this.category.push({
          category_id: 0,
          title: '查看未分类',
          blog_sum: null
        })
        if (this.user_id === 0) {
          this.disable = true
          return
        }
        this.disable = false
        let params = {
          user_id: this.user_id,
          token: this.$store.state.auth.token,
          want_deleted: true
        }
        this.$http.get('show/category/list', { params })
          .then(res => {
            this.category[1].blog_sum = res.data.nocate
            let Cates = res.data.category
            for (let cate of Cates) {
              this.category.push({
                title: cate.title,
                blog_sum: cate.total,
                category_id: cate.id
              })
            }
          })
          .catch(e => {
            console.log(e)
          })
      },
      GetBlog () {
        this.loading = true
        let params = {
          offset: this.Pagination.Size * (this.Pagination.Current - 1),
          limit: this.Pagination.Size,
          token: this.$store.state.auth.token,
          want_deleted: true
        }
        if (this.user_id !== 0) params['user_id'] = this.user_id
        if (this.category_id !== -1) params['category_id'] = this.category_id
        if (this.$store.state.auth.authUser.id !== 1) params['user_id'] = this.$store.state.auth.authUser.id
        this.$http.get('show/blog/list', { params })
          .then(res => {
            if (this.category_id !== -1) this.Pagination.Total = +res.headers['seleted']
            else this.Pagination.Total = +res.headers['x-total']
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
        this.$router.push({name: 'AdminEditBlog', params: {id: this.Blogs[index].id}})
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
      },
      ChangeUser: function () {
        store.set('userCategory', this.user_id)
        this.GetCategory()
        this.GetBlog()
      },
      ChangeCategory: function () {
        store.set('blogCategory', this.category_id)
        this.GetBlog()
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
  .select-user {
    width: 200px;
    margin-left: 30px;
  }
  .blog-sum {
    float: right;
    color: #cccccc;
  }
</style>
