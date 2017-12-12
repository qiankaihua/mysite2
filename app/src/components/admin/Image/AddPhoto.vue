<template><div class="AddPhoto">
  <div class="layout-breadcrumb">
    <i-breadcrumb>
      <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
      <i-breadcrumb-item>Image</i-breadcrumb-item>
      <i-breadcrumb-item>Add</i-breadcrumb-item>
    </i-breadcrumb>
  </div>
  <div class="layout-content">
    <div class="layout-content-main">
      <div class="title">上传图片</div>
      <i-upload
        class="upload"
        :before-upload="beforeUpload"
        multiple
        type="drag"
        action="//">
        <div style="padding: 20px 0">
          <i-icon type="ios-cloud-upload" size="52" style="color: #3399ff"></i-icon>
          <p>Click or drag files here to upload</p>
        </div>
      </i-upload>
      <div class="upload-all">
        <i-button type="primary" @click="UploadAll">上传全部</i-button>
      </div>
      <div class="for-style"></div>
      <div v-for="(item, idx) in image" class="preview">
        <div class="operation">
          <div class="operation-left" @click="UploadPhoto(idx)" v-if="!item.status">
            <i-icon type="share" class="icon"></i-icon>
            <div>上传</div>
          </div>
          <div class="operation-left" @click="DownloadPhoto(idx)" v-else>
            <i-icon type="share" class="icon"></i-icon>
            <div>下载</div>
          </div>
          <div class="operation-right" @click="DeletePhoto(idx)">
            <i-icon type="close" class="icon"></i-icon>
            <div>删除</div>
          </div>
        </div>
        <img :src="item.data" :key="idx"><br>
        <i-input v-model="item.name" placeholder="Enter Photo Name...">
        </i-input>
      </div>
    </div>
  </div>
</div>
</template>

<script>
  import cf from '../../../../.config.js'
  export default {
    name: 'AddPhoto',
    data () {
      return {
        image: []
      }
    },
    computed: {
    },
    methods: {
      imgPreview (file) {
        let self = this
        // 看支持不支持FileReader
        if (!file || !window.FileReader) return
        if (/^image/.test(file.type)) {
          // 创建一个reader
          let reader = new FileReader()
          // 将图片将转成 base64 格式
          reader.readAsDataURL(file)
          // 读取成功后的回调
          reader.onloadend = function () {
            self.image.push({
              status: false, // 未上传
              photo: file,
              data: this.result,
              name: file['name'].replace(/^(.+?)(\.[^.\\]*?)?$/gi, '$1')
            })
          }
        }
      },
      beforeUpload: function (file) {
        this.imgPreview(file)
        return false
      },
      UploadAll: function () {
        for (let i = 0; i < this.image.length; i += 1) {
          if (this.image[i].status === true) continue
          let formData = new FormData()
          formData.append('photo', this.image[i].photo)
          let config = {
            headers: {
              'Content-Type': 'multipart/form-data',
              'token': this.$store.state.auth.token
            }
          }
          this.$http.post('photo/add', formData, config)
            .then(r => {
              this.image[i].status = true
              this.$Message.success('上传成功')
              this.image[i].url = r.data.path
              this.image[i].photoId = r.data.id
            })
            .catch(e => {
              this.$Message.error('上传失败')
              console.log(e)
            })
        }
      },
      UploadPhoto: function (id) {
        console.log(id)
        let formData = new FormData()
        formData.append('photo', this.image[id].photo)
        formData.append('name', this.image[id].name)
        let config = {
          headers: {
            'Content-Type': 'multipart/form-data',
            'token': this.$store.state.auth.token
          }
        }
        this.$http.post('photo/add', formData, config)
          .then(r => {
            this.image[id].status = true
            this.$Message.success('上传成功')
            this.image[id].url = r.data.path
            this.image[id].photoId = r.data.id
          })
          .catch(e => {
            this.$Message.error('上传失败')
            console.log(e)
          })
      },
      DownloadPhoto (id) {
        window.open(cf.api.host + '/show/img/' + this.image[id].url) // 改ip
      },
      DeletePhoto: function (id) {
        if (this.image[id].status === false) {
          this.image.splice(id, 1)
        } else {
          this.$http.delete('photo/' + this.image[id].photoId, {params: {token: this.$store.state.auth.token}})
            .then(r => {
              console.log(r.data)
              this.image.splice(id, 1)
              this.$Message.success('删除成功')
            })
            .catch(e => {
              this.$Message.error('删除失败')
              console.log(e)
            })
        }
      }
    }
  }
</script>

<style scoped lang="less">
  .AddPhoto {
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
  .upload {
    width: 75%;
    margin-left: 15%;
  }
  .preview {
    margin-left: 4%;
    margin-bottom: 20px;
    max-width: 20%;
    display: inline-block;
    position: relative;
    text-align: center;
    .operation {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(45, 183, 245, .2);
      opacity: 0;
      transition: opacity .2s;
      &:hover {
        opacity: 1;
      }
      cursor: pointer;
      color: #fff;
      text-shadow: 5px 5px 5px #1c2438;
      .icon {
        margin-top: calc((100% - 40px - 18px));
        font-size: 40px;
      }
      .operation-left, .operation-right {
        position: absolute;
        line-height: 100%;
        top: 0;
        width: 50%;
        height: 100%;
        background: rgba(45, 183, 245, .3);
        opacity: 0;
        transition: opacity .2s;
        &:hover {
          opacity: 1;
        }
      }
      .operation-left {
        left: 0;
      }
      .operation-right {
        left: 50%;
      }
    }
  }
  .preview img {
    max-width: 100%;
  }
  .upload-all {
    display: inline;
    float: right;
    margin-top: 10px;
    margin-left: 10px;
    margin-bottom: 10px;
    margin-right: 10px;
  }
  .for-style {
    height: 150px;
  }
</style>
