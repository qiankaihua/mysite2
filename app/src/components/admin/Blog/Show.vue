<template>
  <div class="ShowBlogList">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item href="/admin/blog/list">Blog</i-breadcrumb-item>
        <i-breadcrumb-item>Show</i-breadcrumb-item>
      </i-breadcrumb>
      <div class="layout-content">
        <div class="layout-content-main">
          <i-row>
            <i-col span="18">
              <h1 class="title">{{blog.title}}</h1>
              <div class="small-title">
                <i-tag class="star" color="blue" type="border" v-if="stared">
                  <a @click="ChangeStar()">
                    <i-icon type="ios-heart" color="red"></i-icon>
                    <span class="star-line">取消赞</span>
                  </a>
                </i-tag>
                <i-tag class="star" color="blue" type="border" v-else>
                  <a @click="ChangeStar()">
                    <i-icon type="ios-heart-outline" color="red"></i-icon>
                    <span class="star-line">赞</span>
                  </a>
                </i-tag>
                <span class="small-title-content">分类：{{blog.category.title}}</span>
                <span class="small-title-content">更新时间：{{blog.created_at}}</span>
              </div>
              <div class="tags">
                <span class="small-title-content">标签：</span>
                <i-tag class="tag" v-for="(item, idx) in blog.tags" :key="idx" :name="idx" color="blue">{{ item }}</i-tag>
              </div>
              <div class="line"></div>
              <div class="editorContainer">
                <markdown
                  :mdValuesP="blog.contents"
                  :fullPagStatusP="false"
                  :editStatusP="false"
                  :previewStatusP="true"
                  :navStatusP="false"
                  :icoStatusP="false"
                ></markdown>
              </div>
              <i-button class="to-edit" type="primary" @click="ToEdit">编辑</i-button>
            </i-col>
            <i-col span="6">
              <i-affix :offset-top="20">
                <i-card class="user-card">
                  <p slot="title" class="card-title">
                    <i-avatar v-if="avatar" :src="avatar" />
                    <i-avatar v-else icon="person" />
                    <span class="nickname">{{ blog.user.nickname }}</span>
                  </p>
                  <p>
                    <span style="color: #afafaf;">发表文章数</span>
                    <span style="float: right">{{blog.user.count}}</span>
                  </p>
                  <p class="blogs-title">
                    <span>最新文章</span>
                    <span class="star-sum">点赞数</span>
                  </p>
                  <ol class="none-style">
                    <li v-for="b in blog.user.blogs" class="latest-blogs">
                      <a @click="ToBlogDetail(b.id)" class="latest-blog">{{ b['title'] }}</a>
                      <span class="star-sum">{{b['star']}}</span>
                    </li>
                  </ol>
                </i-card>
              </i-affix>
            </i-col>
          </i-row>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import markdown from '../MarkDown.vue'
  import moment from 'moment'
  export default {
    name: '',
    components: {
      markdown
    },
    data: function () {
      return {
        checked: false,
        avatar: null,
        stared: false,
        blog: {
          title: '',
          category: {
            title: ''
          },
          id: 0,
          contents: '',
          star: 0,
          tags: [],
          user: {
            id: 0,
            nickname: '',
            avatar: null
          },
          created_at: null,
          updated_at: null
        }
      }
    },
    computed: {},
    beforeMount: function () {
      this.$http.get('show/blog/' + this.$route.params.id, {params: {token: this.$store.state.auth.token}})
        .then(r => {
          this.blog = r.data
          this.blog.created_at = moment.unix(this.blog.created_at).format('YYYY-MM-DD HH:mm:ss')
          this.blog.updated_at = moment.unix(this.blog.updated_at).format('YYYY-MM-DD HH:mm:ss')
          let avatarName = r.data.user.avatar
          if (avatarName !== null && avatarName !== undefined) {
            this.avatar = axios.defaults.baseURL + 'show/img/' + avatarName
          } else {
            this.avatar = null
          }
          if (this.blog.category === null) {
            this.blog.category = {
              title: '默认分组'
            }
          }
          this.stared = this.blog.stared
          document.title = this.blog.title + window.title_suf
        })
        .catch(e => {
          console.log(e)
        })
    },
    methods: {
      ToBlogDetail: function (id) {
        this.$router.push({name: 'AdminShowBlogDetail', params: {id: id}})
      },
      ChangeStar: function () {
        let data = {}
        data.token = this.$store.state.auth.token
        this.$http.put('blog/' + this.$route.params.id + '/star', data)
          .then(r => {
            this.stared = !this.stared
            if (this.stared === true) this.$Message.success('点赞成功')
            else this.$Message.success('取消点赞成功')
          })
          .catch(e => {
            this.$Message.error('操作失败')
            console.log(e)
          })
      },
      ToEdit: function () {
        event.preventDefault()
        this.$router.push({name: 'AdminEditBlog', params: {id: this.blog.id}})
      }
    }
  }
</script>

<style scoped>
  .layout-breadcrumb{
    padding: 10px 15px 0;
  }
  .editorContainer {
    width: 90%;
    min-height: 80vh;
    margin-left: 5%;
    margin-top: 20px;
    margin-bottom: 20px;
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
  .user-card {
    margin-left: 5px;
  }
  .card-title {
    height: 36px;
  }
  .nickname {
    margin-left: 25%;
    height: 36px;
    margin-bottom: 5px;
  }
  .tag {
    min-width: 20px;
  }
  .tags {
    margin-left: -20px;
    text-align: center;
    /*margin-bottom: 10px;*/
  }
  .none-style
    ul {
      list-style: none !important;
    }
    ol {
      list-style: none !important;
    }
  .star-sum {
    float: right;
    color: #cccccc;
  }
  .latest-blog {
    white-space:nowrap;
    width: 80%;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
  }
  .blogs-title {
    margin-top: 6px;
  }
  .small-title {
    text-align: center;
    margin-bottom: 10px;
  }
  .small-title-content {
    margin-left: 20px;
  }
  .line {
    height: 0;
    margin-top: 20px;
    border-top: 1px solid #7f7f7f;
  }
  .star {
    float: right;
    margin-left: 60%;
    position: absolute;
  }
  .star-line {
    margin-left: 5px;
    color: #2d8cf0;
  }
  .star-line:after {
    position: absolute;
    top:0;
    bottom: 0;
    height: 100%;
    left:2em;
    right: 1em;
    content: '';
    width:0;
    border-right: 1px solid #2d8cf0;
  }
  .to-edit {
    float: right;
  }
</style>
