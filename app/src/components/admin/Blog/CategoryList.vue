<template>
  <div class="CategoryList">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item>Blog</i-breadcrumb-item>
        <i-breadcrumb-item>CategoryList</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        <div class="title">文章分类管理</div>
        <i-row>
          <i-col :span="12" v-if="isAdmin">
            <i-select v-model="user_id" class="select-user" @on-change="ChangeUser">
              <i-option v-for="item in users" :value="item.user_id" :key="item.user_id" :label="item.nickname" :disabled="item.disable">
                <span>{{ item.nickname }}</span>
                <span class="category-sum">{{ item.category_sum }}</span>
              </i-option>
            </i-select>
          </i-col>
          <i-col :offset="12" align="right">
            <i-button @click="modal = !modal" type="primary" class="add-button">添加分类</i-button>
            <i-modal v-model="modal">
              <p slot="header">
                <span>添加文章分类</span>
              </p>
              <div style="text-align:center">
                <span style="margin-right: 20px;">标题</span>
                <i-input v-model="newTitle" placeholder="输入分类标题" style="width: 70%"></i-input>
              </div>
              <div slot="footer">
                <i-button @click="AddCategory" type="primary" class="add-button">确定添加</i-button>
              </div>
            </i-modal>
          </i-col>
          <i-col>
            <i-modal v-model="modal2">
              <p slot="header">
                <span>修改分类</span>
              </p>
              <div style="text-align:center">
                <span style="margin-right: 20px;">标题</span>
                <i-input v-model="editTitle" placeholder="请选择分类" style="width: 70%"></i-input>
              </div>
              <div slot="footer">
                <i-button @click="EditCategory" type="warning" class="add-button">确定修改</i-button>
              </div>
            </i-modal>
          </i-col>
          <i-col>
            <i-modal v-model="modal3">
              <p slot="header">
                <span>移动文章</span>
              </p>
              <div style="text-align:center">
                <span style="margin-right: 20px;">选择分类：</span>
                <i-select v-model="moveToCategory" class="select-category" :disabled="disable">
                  <i-option v-for="item in CategoryChange" :value="item.value" :key="item.value" :label="item.label">
                    <span>{{ item.label }}</span>
                    <span class="sum-blog-per-cate">{{ item.total }}</span>
                  </i-option>
                </i-select>
              </div>
              <div slot="footer">
                <i-button @click="EditBlogCategory" type="warning" class="add-button">确定修改</i-button>
              </div>
            </i-modal>
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
        <i-row :key="idx" v-for="(cate, idx) in Category">
          <i-col span="16" align="center" offset="4">
            <i-card class="cate-card">
              <p slot="title">
                <i-icon type="quote"></i-icon>
                {{cate.title}}
              </p>
              <p slot="extra" class="blog-sum">
                {{cate.total}}
                <i-button v-if="cate.id" @click="ShowEditCategory(cate.id)" type="primary" class="cate-button">修改</i-button>
                <i-button v-if="cate.id" @click="DeleteCategory(cate.id)" type="error" class="cate-button">删除</i-button>
              </p>
              <ul class="cate-ul">
                <li v-for="blog in cate.blog" class="cate-li">
                  <a @click="ToBlogDetail(blog.id)">{{ blog['title'] }}</a>
                  <i-button type="primary" shape="circle" size="small" class="edit-blog" @click="ShowModal3(blog.id)">移至</i-button>
                  <hr>
                </li>
              </ul>
              <p class="create-date" v-if="cate.created_at">创建于{{cate.created_at}}</p>
              <i-button v-if="cate.total > 5" class="more-blog" @click="ShowCategoryDetail(cate.id)">查看更多</i-button>
            </i-card>
          </i-col>
        </i-row>
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
  import store from 'store'
  import moment from 'moment'
  export default {
    components: {
    },
    name: '',
    data () {
      return {
        isAdmin: false,
        modal: false,
        modal2: false,
        modal3: false,
        disable: true,
        newTitle: '',
        editTitle: '',
        CategoryChange: [],
        moveToCategory: 0,
        movedBlog: 0,
        editId: 0,
        users: [
          {
            user_id: 0,
            category_sum: null,
            nickname: '选择用户',
            disable: true
          }
        ],
        user_id: 1,
        Category: [],
        Pagination: {
          Total: 0,
          Current: 1,
          Size: 10
        }
      }
    },
    created () {
      let auth = this.$store.state.auth.authUser
      if (auth.id === 1) {
        this.isAdmin = true
        this.user_id = localStorage.userCategory === undefined ? auth.id : JSON.parse(localStorage.userCategory)
      } else {
        this.isAdmin = false
        this.user_id = auth.id
      }
      this.GetUser()
      let page = (this.$route.query.page || '').split(',')
      this.Pagination.Current = +page[0] || 1
      this.Pagination.Size = +page[1] || 10
      this.GetCategory()
    },
    watch: {
      Pagination: {
        handler (cur, old) {
          let page = this.Pagination.Current + ',' + this.Pagination.Size
          this.$router.push({query: {page}})
        },
        deep: true
      }
    },
    computed: {},
    methods: {
      GetCategory: function () {
        let params = {
          user_id: this.user_id,
          offset: this.Pagination.Size * (this.Pagination.Current - 1),
          limit: this.Pagination.Size,
          token: this.$store.state.auth.token
        }
        this.$http.get('show/category/list', {params})
          .then(res => {
            this.Category = []
            this.Category.push({
              id: 0,
              title: '默认分组',
              total: res.data.nocate,
              created_at: '',
              blog: res.data.nocate_blog
            })
            this.Pagination.Total = +res.headers['x-total']
            let Cates = res.data.category
            for (let cate of Cates) {
              this.Category.push({
                id: cate.id,
                title: cate.title,
                total: cate.total,
                blog: cate.blog,
                created_at: moment.unix(cate.created_at).format('YYYY-MM-DD HH:mm:ss')
              })
            }
          })
          .catch(e => {
            console.log(e)
          })
      },
      AddCategory: function () {
        let config = {
          headers: {
            'Content-Type': 'multipart/form-data',
            'token': this.$store.state.auth.token
          }
        }
        let formData = new FormData()
        formData.append('title', this.newTitle)
        this.$http.post('/category/add', formData, config)
          .then(r => {
            this.$Notice.success({title: '添加成功'})
            this.newTitle = ''
            this.modal = false
            this.GetCategory()
          })
          .catch(e => {
            switch (e.response.status) {
              case 401:
                this.$Notice.error({title: '登录状态失效，请重新登录'})
                this.$router.push({path: '/login'})
                break
              case 403:
                this.$Notice.error({title: '没有权限'})
                break
              case 422:
                this.$Notice.error({title: '标题不能为空且不能过长'})
                break
              case 444:
                this.$Notice.error({title: '不允许重复标题'})
                break
              default :
                this.$Notice.error({title: '未知错误'})
                break
            }
          })
      },
      GetUser: function () {
        let params = {
          offset: this.Pagination.Size * (this.Pagination.Current - 1),
          limit: this.Pagination.Size,
          token: this.$store.state.auth.token,
          want_deleted: true
        }
        this.$http.get('show/user/list', {params})
          .then(res => {
            let Users = res.data
            for (let user of Users) {
              this.users.push({
                nickname: user.nickname,
                category_sum: user.category,
                user_id: user.id,
                disable: false
              })
            }
          })
          .catch(e => {
            console.log(e)
          })
      },
      ChangeUser: function () {
        store.set('userCategory', this.user_id)
        this.GetCategory()
      },
      ShowEditCategory: function (id) {
        this.editId = id
        this.modal2 = !this.modal2
        for (let ca of this.Category) {
          if (ca.id === id) this.editTitle = ca.title
        }
      },
      EditCategory: function () {
        let data = {
          'token': this.$store.state.auth.token,
          'title': this.editTitle
        }
        this.$http.put('category/' + this.editId, data)
          .then(r => {
            this.$Notice.success({title: '修改成功'})
            this.GetCategory()
            this.modal2 = false
          })
          .catch(e => {
            switch (e.response.status) {
              case 401:
                this.$Notice.error({title: '登录状态失效，请重新登录'})
                this.$router.push({path: '/login'})
                break
              case 403:
                this.$Notice.error({title: '无操作权限'})
                break
              case 404:
                this.$Notice.error({title: '不存在的分组'})
                break
              case 422:
                this.$Notice.error({title: '标题不能为空且不能过长'})
                break
              case 444:
                this.$Notice.error({title: '重复标题'})
                break
              default :
                this.$Notice.error({title: '未知错误'})
            }
          })
      },
      DeleteCategory: function (id) {
        console.log(id)
        this.$http.delete('category/' + id, {params: {token: this.$store.state.auth.token}})
          .then(r => {
            this.$Notice.success({title: '删除成功'})
            this.GetCategory()
          })
          .catch(e => {
            switch (e.response.status) {
              case 401:
                this.$Notice.error({title: '登录状态失效，请重新登录'})
                this.$router.push({path: '/login'})
                break
              case 403:
                this.$Notice.error({title: '无操作权限'})
                break
              case 404:
                this.$Notice.error({title: '不存在的分组'})
                break
              default :
                this.$Notice.error({title: '未知错误'})
            }
          })
      },
      ShowCategoryDetail: function (id) {
        store.set('blogCategory', id)
        setTimeout(this.$router.push({name: 'AdminShowBlog'}), 233)
      },
      ToBlogDetail: function (id) {
        this.$router.push({name: 'AdminShowBlogDetail', params: {id: id}})
      },
      ShowModal3: function (blogId) {
        let params = {
          user_id: this.user_id,
          token: this.$store.state.auth.token
        }
        this.$http.get('show/category/list', {params})
          .then(res => {
            this.CategoryChange = []
            this.CategoryChange.push({
              value: 0,
              label: '默认分组',
              total: res.data.nocate
            })
            let Cates = res.data.category
            for (let cate of Cates) {
              this.CategoryChange.push({
                value: cate.id,
                label: cate.title,
                total: cate.total
              })
            }
            if (this.CategoryChange.length === 1) this.disable = true
            else this.disable = false
          })
          .catch(e => {
            console.log(e)
          })
        this.modal3 = true
        this.movedBlog = blogId
      },
      EditBlogCategory: function () {
        let data = {}
        data.category_id = this.moveToCategory
        data.token = this.$store.state.auth.token
        this.$http.put('blog/' + this.movedBlog + '/change', data)
          .then(r => {
            this.$Notice.success({ title: '修改成功' })
            this.GetCategory()
            this.modal3 = false
            this.moveToCategory = 0
          })
          .catch(e => {
            this.$Notice.error({ title: '修改失败' })
          })
      }
    }
  }
</script>

<style scoped>
  .CategoryList {
  }
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
  .add-button {
    margin-bottom: 20px;
  }
  .select-user {
    width: 300px;
    margin-left: 30px;
  }
  .category-sum {
    float: right;
    color: #cccccc;
  }
  .blog-sum {
    color: #aaaaaa;
  }
  .cate-button {
    margin-left: 10px;
  }
  .cate-card {
    text-align: left;
    margin-bottom: 10px;
    margin-top: 10px;
  }
  .cate-ul {
  }
  .cate-li {
    margin-top: 15px;
  }
  .more-blog {
    margin-top: 10px;
  }
  .edit-blog {
    float: right;
  }
  hr {
    margin-top: 7px !important;
    opacity: 0.2 !important;
  }
  ul {
    list-style: none !important;
  }
  ol {
    list-style: none !important;
  }
  .select-category {
    width: 200px;
    margin-left: auto;
    margin-top: 20px;
    margin-bottom: 20px;
  }
  .sum-blog-per-cate {
    float: right;
    color: #cccccc;
  }
  .create-date {
    margin-top: 20px;
  }
</style>
