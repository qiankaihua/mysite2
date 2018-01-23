<template>
  <div class="md">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item>Blog</i-breadcrumb-item>
        <i-breadcrumb-item>Post</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        <div class="title">发表博客</div>
        <i-form ref="addBlogValidate" :model="formValidate" :rules="ruleValidate" :label-width="80" onsubmit="return false">
          <i-row>
            <i-col span="16" offset="3">
              <i-form-item label="" prop="title">
                <i-input v-model="formValidate.title" placeholder="Edit your blog's title">
                  <span slot="prepend">
                    <i-icon :size="16" type="quote"></i-icon>
                  </span>
                </i-input>
              </i-form-item>
            </i-col>
          </i-row>
          <i-row>
            <i-select v-model="category_id" class="select-category" :disabled="disable">
              <i-option v-for="item in category" :value="item.value" :key="item.value" :label="item.label">
                <span>{{ item.label }}</span>
                <span class="sum-blog-per-cate">{{ item.total }}</span>
              </i-option>
            </i-select>
          </i-row>
          <i-row>
            <i-tag class="tag" v-for="(item, idx) in tags" :key="idx" :name="idx" color="blue" closable @on-close="CloseTag">{{ item }}</i-tag>
            <i-poptip v-model="show">
              <i-button icon="ios-plus-empty" type="dashed" size="small" @on-ok="AddTag">添加标签</i-button>
              <div slot="content">
                <p>请填写标签</p>
                <i-input size="small" v-model="newTag"></i-input>
                <div style="margin-top: 10px" align="right">
                  <i-button type="primary" size="small" @click="AddTag">确认添加</i-button>
                </div>
              </div>
            </i-poptip>

          </i-row>
        </i-form>
        <div class="editorContainer">
          <markdown
            :mdValuesP="msg.mdValue"
            :fullPageStatusP="false"
            :editStatusP="true"
            :previewStatusP="true"
            :navStatusP="true"
            :icoStatusP="false"
            @childevent="childEventHandler"
          ></markdown>
        </div>
        <i-row>
          <i-col offset="15">
            <i-button type="primary" shape="circle" size="large" style="width: 200px" @click="Submit">上传</i-button>
          </i-col>
        </i-row>
      </div>
    </div>
  </div>
</template>

<script>
  import markdown from '../MarkDown.vue'
  import store from 'store'
  export default {
    name: 'AddBlog',
    data () {
      return {
        newTag: '',
        show: false,
        tags: [],
        category: [
          {
            value: 0,
            total: 0,
            label: '默认分组'
          }
        ],
        category_id: 0,
        disable: false,
        token: '',
        formValidate: {
          title: ''
        },
        ruleValidate: {
          title: [
            { required: true, message: '标题不能为空', trigger: 'blur' }
          ]
        },
        msg: {
          mdValue: '# Edit your blog here!'
        },
        mdValue: ''
      }
    },
    components: {
      markdown
    },
    beforeMount: function () {
      this.token = this.$store.state.auth.token
      this.msg.mdValue = localStorage.mdValue === undefined ? '# Edit your blog here! \n' +
        '\n' +
        '行内公式使用` \\(在此编写公式\\)`\n' +
        '行间公式使用`\\[在此编写公式\\]`' : JSON.parse(localStorage.mdValue)
      let auth = this.$store.state.auth.authUser
      let params = {
        user_id: auth.id,
        token: this.$store.state.auth.token
      }
      this.$http.get('show/category/list', { params })
        .then(r => {
          let originCategory = r.data.category
          let cc = r.data.count
          for (let cate of originCategory) {
            cc = cc - cate.total
            this.category.push({
              value: cate.id,
              label: cate.title,
              total: cate.total
            })
          }
          this.category[0].total = cc
          if (this.category.length === 1) this.disable = true
          else this.disable = false
        })
        .catch(e => {
          console.log(e)
        })
    },
    methods: {
      childEventHandler: function (res) {
        // res会传回一个data,包含属性mdValue和htmlValue，具体含义请自行翻译
        this.msg = res
        this.mdValue = this.msg.mdValue
      },
      Submit: function () {
        event.preventDefault()
        this.$refs.addBlogValidate.validate((valid) => {
          if (valid) {
            let formdata = new FormData()
            formdata.append('title', this.formValidate.title)
            formdata.append('contents', this.msg.mdValue)
            formdata.append('category_id', this.category_id)
            let config = {
              headers: {
                'Content-Type': 'multipart/form-data',
                'token': this.token
              }
            }
            this.$http.post('blog/add', formdata, config)
              .then(r => {
                this.$Notice.success({ title: '上传成功' })
                store.set('mdValue', '# Edit your blog here! \n' +
                  '\n' +
                  '行内公式使用` \\(在此编写公式\\)`\n' +
                  '行间公式使用`\\[在此编写公式\\]`')
                for (let tag of this.tags) {
                  let formdata = new FormData()
                  formdata.append('blog_id', r.data.id)
                  formdata.append('value', tag)
                  this.$http.post('tag/add', formdata, config)
                    .then(r => {
                    })
                    .catch(e => {
                      console.log(e)
                    })
                }
                this.$router.push({name: 'AdminShowBlogDetail', params: {id: r.data.id}})
              })
              .catch(e => {
                this.$Notice.error({ title: '上传失败' })
              })
          }
        })
      },
      CloseTag: function (event, name) {
        const index = this.tags.indexOf(name)
        this.tags.splice(index, 1)
      },
      AddTag: function () {
        event.preventDefault()
        if (this.newTag === '') {
          this.$Message.error('不能添加空标签')
        } else {
          let flag = true
          for (let t of this.tags) {
            if (this.newTag === t) {
              this.$Message.error('不能添加重复标签')
              flag = false
              break
            }
          }
          if (flag) this.tags.push(this.newTag)
        }
        this.newTag = ''
        this.show = false
      }
    },
    watch: {
      mdValue: function (newV, oldV) {
        store.set('mdValue', newV)
      }
    }
  }
</script>

<style scoped>
  .md {
    /*height: 90vh !important;*/
  }
  .layout-breadcrumb{
    padding: 10px 15px 0;
  }
  .editorContainer {
    width: 90%;
    height: 80vh;
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
    text-align: center;
  }
  .title {
    height: 10vh;
    line-height: 10vh;
    text-align: center;
    font-size: x-large;
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
  .tag {
    min-width: 20px;
  }
</style>
