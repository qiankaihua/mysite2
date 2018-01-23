<template>
  <div class="AlbumDetail">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item href="/admin/albums/list">Image</i-breadcrumb-item>
        <i-breadcrumb-item>AlbumDetail</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        <div class="title">相册详情</div>
        <div>
          <p class="title-album">
            {{"相册名：" + Title}}
          </p>
          <p class="intro">
            {{"相册简介：" + Intro}}
          </p>
        </div>
        <i-row>
          <i-col>
            <i-modal v-model="modal3" width="80%">
              <p slot="header" style="height: 36px;">
                <i-input v-model="editPhotoName" style="width: 200px;"></i-input>
                <i-button type="warning" class="change-photo" @click="EditPhotoAlbum(bigPhoto.id)">修改图片</i-button>
                <i-select
                  v-model="moveToAlbum"
                  class="select-edit-album">
                  <i-option v-for="item in Album" :value="item.id" :key="item.id" :label="item.title">
                    <span>{{ item.title }}</span>
                    <span class="album-sum">{{ item.total }}</span>
                  </i-option>
                </i-select>

              </p>
              <div style="text-align: center; width: 80%; margin-left: 10%">
                <img :src="bigPhoto.path" class="big-photo">
                <div class="change-image">
                  <a v-show="bigPhoto.pre" class="pre-image" @click="BigPhotoBefore(bigPhoto.photo_id)"></a>
                  <a v-show="bigPhoto.nxt" class="nxt-image" @click="BigPhotoAfter(bigPhoto.photo_id)"></a>
                </div>
              </div>
              <div slot="footer">
                <span class="index">{{bigPhoto.photo_id + 1}} of {{Pagination.Total}}</span>
                <span class="album-name">所属分组：{{Title}}</span>
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
        <i-row>
          <i-col span="11" v-for="(photo, idx) in Photos" :key="idx" style="margin-left: 20px">
            <i-card class="album-card" :key="idx">
              <p slot="title">
                <i-icon type="quote"></i-icon>
                {{photo.name}}
              </p>
              <p slot="extra" class="photo-sum">
              </p>
              <div>
                <img :src="photo.path" class="photo" @click="BigPhoto(photo, idx)">
              </div>
              <p class="create-date" v-if="photo.updated_at">修改时间：{{photo.updated_at}}</p>
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
  import moment from 'moment'
  import axios from 'axios'
  export default {
    name: 'AdmimAlbumDetail',
    data () {
      return {
        modal3: false,
        isAdmin: false,
        moveToAlbum: 0,
        editPhotoName: '',
        movedPhoto: 0,
        editId: 0,
        bigPhoto: {
          id: '',
          album_id: 0,
          photo_id: '',
          path: '',
          name: ''
        },
        user_id: 1,
        Photos: [],
        AlbumId: 0,
        Title: '',
        Intro: '',
        Album: [
          {
            id: 0,
            total: 0,
            title: ''
          }
        ],
        Pagination: {
          Total: 0,
          Current: 1,
          Size: 10
        }
      }
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
    created () {
      let auth = this.$store.state.auth.authUser
      if (auth.id === 1) {
        this.isAdmin = true
        this.user_id = localStorage.userAlbum === undefined ? auth.id : JSON.parse(localStorage.userAlbum)
      } else {
        this.isAdmin = false
        this.user_id = auth.id
      }
      let page = (this.$route.query.page || '').split(',')
      this.Pagination.Current = +page[0] || 1
      this.Pagination.Size = +page[1] || 10
      this.GetPhoto()
      this.GetAlbum()
    },
    methods: {
      GetPhoto: function () {
        let params = {
          user_id: this.user_id,
          album_id: this.$route.params.id,
          offset: this.Pagination.Size * (this.Pagination.Current - 1),
          limit: this.Pagination.Size,
          token: this.$store.state.auth.token
        }
        this.$http.get('show/album/' + this.$route.params.id, {params})
          .then(res => {
            this.Photos = []
            this.AlbumId = res.data.id
            this.Title = res.data.title
            this.Intro = res.data.intro
            for (let photo of res.data.photo) {
              this.Photos.push({
                id: photo.id,
                path: axios.defaults.baseURL + 'show/img/' + photo.path,
                name: photo.name,
                updated_at: moment.unix(photo.updated_at).format('YYYY-MM-DD HH:mm:ss')
              })
            }
            this.Pagination.Total = +res.headers['x-total']
          })
          .catch(e => {
            console.log(e)
          })
      },
      GetAlbum: function () {
        let params = {
          user_id: this.user_id,
          offset: this.Pagination.Size * (this.Pagination.Current - 1),
          limit: this.Pagination.Size,
          token: this.$store.state.auth.token
        }
        this.$http.get('show/album/list', {params})
          .then(res => {
            this.Album = []
            this.Album.push({
              id: 0,
              title: '默认分组',
              total: res.data.nocate
            })
            let Albums = res.data.album
            for (let album of Albums) {
              this.Album.push({
                id: album.id,
                title: album.title,
                total: album.total
              })
            }
//            console.log(res.data.album)
//            console.log(this.Album)
          })
          .catch(e => {
            console.log(e)
          })
      },
      BigPhoto: function (photo, photoId) {
        this.modal3 = !this.modal3
        let hasPre = true
        if (photoId === 0) hasPre = false
        let hasNxt = true
        if (photoId === this.Photos.length - 1) hasNxt = false
        this.bigPhoto = {
          id: photo.id,
          photo_id: photoId,
          path: photo.path,
          name: photo.name,
          pre: hasPre,
          nxt: hasNxt
        }
        this.editPhotoName = this.bigPhoto.name
      },
      BigPhotoBefore: function (photoId) {
        photoId -= 1
        let hasPre = true
        if (photoId === 0) hasPre = false
        let hasNxt = true
        if (photoId === this.Photos.length - 1) hasNxt = false
        let photo = this.Photos[photoId]
        this.bigPhoto = {
          id: photo.id,
          photo_id: photoId,
          path: photo.path,
          name: photo.name,
          pre: hasPre,
          nxt: hasNxt
        }
        this.editPhotoName = this.bigPhoto.name
      },
      BigPhotoAfter: function (photoId) {
        photoId += 1
        let hasPre = true
        if (photoId === 0) hasPre = false
        let hasNxt = true
        if (photoId === this.Photos.length - 1) hasNxt = false
        let photo = this.Photos[photoId]
        this.bigPhoto = {
          id: photo.id,
          photo_id: photoId,
          path: photo.path,
          name: photo.name,
          pre: hasPre,
          nxt: hasNxt
        }
        this.editPhotoName = this.bigPhoto.name
      },
      EditPhotoAlbum: function (id) {
        let data = {}
        data.name = this.editPhotoName
        data.album_id = this.moveToAlbum
        data.token = this.$store.state.auth.token
        this.$http.put('photo/' + id, data)
          .then(r => {
            this.$Notice.success({title: '修改成功'})
            this.GetPhoto()
            this.modal3 = false
            this.moveToAlbum = 0
          })
          .catch(e => {
            this.$Notice.error({title: '修改失败'})
            console.log(e)
          })
      }
    }
  }
</script>

<style scoped lang="less">
  .AlbumDetail {
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
  .photo-sum {
    width: 100px;
    color: #aaaaaa;
  }
  .album-card {
    text-align: left;
    margin-bottom: 10px;
    margin-top: 10px;
  }
  .create-date {
    margin-top: 20px;
  }
  .photo {
    margin-left: 5px;
    max-height: 100px;
    max-width: 100%;
    cursor: pointer;
  }
  .big-photo {
    /*max-width: 100%;*/
    /*max-height: none;*/
    display: block;
    height: auto;
    max-width: 100%;
    max-height: none;
    border-radius: 3px;
    /* Image border */
    border: 4px solid white;

    margin-left: auto;
    margin-right: auto
  }
  .index {
    float: left;
    color: #cccccc;
  }
  .change-image {
    position: absolute;
    top: 50px;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 10;
  a {
    outline: none;
    background-image: url('data:image/gif;base64,R0lGODlhAQABAPAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');
  }
  }
  .pre-image, .nxt-image {
    height: 100%;
    cursor: pointer;
    display: block;
    z-index: 20;
  }
  .pre-image {
    background: url('../../../../static/pre.png') left 5% top 45% no-repeat !important;
    width: 34%;
    left: 0;
    float: left;
    opacity: 0;
    transition: opacity 0.6s;
    -webkit-transition: opacity 0.6s;
    -moz-transition: opacity 0.6s;
    -o-transition: opacity 0.6s;
  &:hover {
     opacity: 1;
   }
  }
  .nxt-image {
    background: url('../../../../static/next.png') right 5% top 45% no-repeat !important;
    width: 64%;
    right: 0;
    float: right;
    opacity: 0;
    transition: opacity 0.6s;
    -webkit-transition: opacity 0.6s;
    -moz-transition: opacity 0.6s;
    -o-transition: opacity 0.6s;
  &:hover {
     opacity: 1;
   }
  }
  .album-name {
  }
  .select-edit-album {
    width: 200px;
    float: right;
    margin-right: 50px;
  }
  .change-photo {
    float: right;
    margin-right: 50px;
  }
  .intro {
    height: 3vh;
    line-height: 3vh;
    font-size: 16px;
  }
  .title-album {
    height: 5vh;
    line-height: 5vh;
    font-size: 20px;
  }
</style>
