<template>
  <div class="AlbumList">
    <div class="layout-breadcrumb">
      <i-breadcrumb>
        <i-breadcrumb-item href="/admin/info">Home</i-breadcrumb-item>
        <i-breadcrumb-item href="/admin/albums/list">Image</i-breadcrumb-item>
        <i-breadcrumb-item>AlbumList</i-breadcrumb-item>
      </i-breadcrumb>
    </div>
    <div class="layout-content">
      <div class="layout-content-main">
        <div class="title">相册管理</div>
        <i-row>
          <i-col :span="12" v-if="isAdmin">
            <i-select v-model="user_id" class="select-user" @on-change="ChangeAlbum">
              <i-option v-for="item in users" :value="item.user_id" :key="item.user_id" :label="item.nickname">
                <span>{{ item.nickname }}</span>
                <span class="album-sum">{{ item.album_sum }}</span>
              </i-option>
            </i-select>
          </i-col>
          <i-col :offset="12" align="right">
            <i-button @click="modal = !modal" type="primary" class="add-button">添加相册</i-button>
            <i-modal v-model="modal">
              <p slot="header">
                <span>添加相册</span>
              </p>
              <div style="text-align:center">
                <div style="text-align: left; margin-left: 15%">标题</div>
                <i-input v-model="newTitle" placeholder="输入相册标题" style="width: 70%"></i-input>
                <div style="text-align: left; margin-left: 15%; margin-top: 20px">简介</div>
                <i-input v-model="newIntro" placeholder="相册简介......" type="textarea" :autosize="{minRows: 2, maxRows: 10}" class="intro"></i-input>
              </div>
              <div slot="footer">
                <i-button @click="AddAlbum" type="primary" class="add-button">确定添加</i-button>
              </div>
            </i-modal>
          </i-col>
          <i-col>
            <i-modal v-model="modal2">
              <p slot="header">
                <span>修改相册</span>
              </p>
              <div style="text-align:center">
                <div style="text-align: left; margin-left: 15%">标题</div>
                <i-input v-model="editTitle" placeholder="输入相册标题" style="width: 70%"></i-input>
                <div style="text-align: left; margin-left: 15%; margin-top: 20px">简介</div>
                <i-input v-model="editIntro" placeholder="相册简介......" type="textarea" :autosize="{minRows: 2, maxRows: 10}" class="intro"></i-input>
              </div>
              <div slot="footer">
                <i-button @click="EditAlbum" type="warning" class="add-button">确定修改</i-button>
              </div>
            </i-modal>
          </i-col>
          <i-col>
            <i-modal v-model="modal3" width="80%">
              <p slot="header" style="height: 36px;">
                <i-input v-model="editPhotoName" style="width: 200px;"></i-input>
                <!--<span>{{bigPhoto.name}}</span>-->
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
              <div style="text-align: center">
                <img :src="bigPhoto.path" class="big-photo">
                <div class="change-image">
                  <a v-show="bigPhoto.pre" class="pre-image" @click="BigPhotoBefore(bigPhoto.photo_id, bigPhoto.album_id)"></a>
                  <a v-show="bigPhoto.nxt" class="nxt-image" @click="BigPhotoAfter(bigPhoto.photo_id, bigPhoto.album_id)"></a>
                </div>
              </div>
              <div slot="footer">
                <span class="index">{{bigPhoto.photo_id + 1}} of {{Album[bigPhoto.album_id].total}}</span>
                <span class="album-name">所属分组：{{Album[bigPhoto.album_id].title}}</span>
                <!--<i-button @click="EditBlogCategory" type="warning" class="add-button">确定修改</i-button>-->
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
        <i-row :key="idx" v-for="(album, idx) in Album">
          <i-col span="16" align="center" offset="4">
            <i-card class="album-card">
              <p slot="title">
                <i-icon type="quote"></i-icon>
                {{album.title}}
              </p>
              <p slot="extra" class="photo-sum">
                {{album.total}}
                <i-button v-if="album.id" @click="ShowEditAlbum(album.id)" type="primary" class="album-button">修改</i-button>
                <i-button v-if="album.id" @click="DeleteAlbum(album.id)" type="error" class="album-button">删除</i-button>
              </p>
              <div v-if="album.photo">
                <img v-for="(photo, idx2) in album.photo.slice(0, 5)" :src="photo.path" class="photo" @click="BigPhoto(photo, idx, idx2)">
              </div>
              <p class="create-date" v-if="album.created_at">创建于{{album.created_at}}</p>
              <i-button v-if="album.total > 5" class="more-photo" @click="ShowAlbumDetail(album.id)">查看更多</i-button>
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
  import axios from 'axios'
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
        newIntro: '',
        editTitle: '',
        editIntro: '',
        AlbumChange: [],
        moveToAlbum: 0,
        editPhotoName: '',
        movedPhoto: 0,
        editId: 0,
        bigPhoto: {
          id: '',
          album_id: 0,
          photo_id: '',
          path: '',
          name: '',
          total: ''
        },
        users: [
          {
            user_id: 0,
            album_sum: '',
            nickname: '选择用户'
          }
        ],
        user_id: 1,
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
    created () {
      let auth = this.$store.state.auth.authUser
      if (auth.id === 1) {
        this.isAdmin = true
        this.user_id = (localStorage.userAlbum === undefined || localStorage.userAlbum === '') ? auth.id : JSON.parse(localStorage.userAlbum)
        if (this.user_id === '') this.user_id = auth.id
        console.log(this.user_id)
      } else {
        this.isAdmin = false
        this.user_id = auth.id
      }
      this.GetUser()
      let page = (this.$route.query.page || '').split(',')
      this.Pagination.Current = +page[0] || 1
      this.Pagination.Size = +page[1] || 10
      this.GetAlbum()
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
              total: res.data.nocate,
              intro: '',
              created_at: '',
              photo: []
            })
            for (let photo of res.data.nocate_photo) {
              this.Album[0].photo.push({
                id: photo.id,
                path: axios.defaults.baseURL + 'show/img/' + photo.path,
                name: photo.name
              })
            }
            this.Pagination.Total = +res.headers['x-total']
            let Albums = res.data.album
            for (let album of Albums) {
              let temp = []
              for (let photo of album.photo) {
                temp.push({
                  id: photo.id,
                  path: axios.defaults.baseURL + 'show/img/' + photo.path,
                  name: photo.name
                })
              }
              this.Album.push({
                id: album.id,
                title: album.title,
                total: album.total,
                photo: temp,
                intro: album.intro,
                created_at: moment.unix(album.created_at).format('YYYY-MM-DD HH:mm:ss')
              })
            }
          })
          .catch(e => {
            console.log(e)
          })
      },
      AddAlbum: function () {
        let config = {
          headers: {
            'Content-Type': 'multipart/form-data',
            'token': this.$store.state.auth.token
          }
        }
        let formData = new FormData()
        formData.append('title', this.newTitle)
        if (this.newIntro === '') this.newIntro = '该用户很懒，没有留下简介'
        formData.append('intro', this.newIntro)
        this.$http.post('/album/add', formData, config)
          .then(r => {
            this.$Notice.success({title: '添加成功'})
            this.newTitle = ''
            this.newIntro = ''
            this.modal = false
            this.GetAlbum()
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
                this.$Notice.error({title: '标题不能为空'})
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
                album_sum: user.album,
                user_id: user.id
              })
            }
          })
          .catch(e => {
            console.log(e)
          })
      },
      ChangeAlbum: function () {
        store.set('userAlbum', this.user_id)
        this.GetAlbum()
      },
      ShowEditAlbum: function (id) {
        this.editId = id
        this.modal2 = !this.modal2
        for (let al of this.Album) {
          if (al.id === id) {
            this.editTitle = al.title
            this.editIntro = al.intro
          }
        }
      },
      EditAlbum: function () {
        if (this.editIntro === '') this.editIntro = '该用户很懒，没有留下简介'
        let data = {
          'token': this.$store.state.auth.token,
          'title': this.editTitle,
          'intro': this.editIntro
        }
        this.$http.put('album/' + this.editId, data)
          .then(r => {
            this.$Notice.success({title: '修改成功'})
            this.GetAlbum()
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
                this.$Notice.error({title: '标题不能为空'})
                break
              case 444:
                this.$Notice.error({title: '重复标题'})
                break
              default :
                this.$Notice.error({title: '未知错误'})
            }
          })
      },
      DeleteAlbum: function (id) {
        this.$http.delete('album/' + id, {params: {token: this.$store.state.auth.token}})
          .then(r => {
            this.$Notice.success({title: '删除成功'})
            this.GetAlbum()
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
      ShowAlbumDetail: function (id) {
        store.set('userAlbum', this.user_id)
        this.$router.push({name: 'AdminAlbumDetail', params: {id: id}})
      },
      EditPhotoAlbum: function (id) {
        let data = {}
        data.name = this.editPhotoName
        data.album_id = this.moveToAlbum
        data.token = this.$store.state.auth.token
        this.$http.put('photo/' + id, data)
          .then(r => {
            this.$Notice.success({title: '修改成功'})
            this.GetAlbum()
            this.modal3 = false
            this.moveToAlbum = 0
          })
          .catch(e => {
            this.$Notice.error({title: '修改失败'})
            console.log(e)
          })
      },
      BigPhoto: function (photo, albumId, photoId) {
        this.modal3 = !this.modal3
        let hasPre = true
        if (photoId === 0) hasPre = false
        let hasNxt = true
        if (photoId === this.Album[albumId].photo.length - 1) hasNxt = false
        this.bigPhoto = {
          id: photo.id,
          photo_id: photoId,
          album_id: albumId,
          path: photo.path,
          name: photo.name,
          pre: hasPre,
          nxt: hasNxt
        }
        this.editPhotoName = this.bigPhoto.name
        this.moveToAlbum = this.bigPhoto.album_id
      },
      BigPhotoBefore: function (photoId, albumId) {
        photoId -= 1
        let hasPre = true
        if (photoId === 0) hasPre = false
        let hasNxt = true
        if (photoId === this.Album[albumId].photo.length - 1) hasNxt = false
        let photo = this.Album[albumId].photo[photoId]
        this.bigPhoto = {
          id: photo.id,
          photo_id: photoId,
          album_id: albumId,
          path: photo.path,
          name: photo.name,
          pre: hasPre,
          nxt: hasNxt
        }
        this.editPhotoName = this.bigPhoto.name
      },
      BigPhotoAfter: function (photoId, albumId) {
        photoId += 1
        let hasPre = true
        if (photoId === 0) hasPre = false
        let hasNxt = true
        if (photoId === this.Album[albumId].photo.length - 1) hasNxt = false
        let photo = this.Album[albumId].photo[photoId]
        this.bigPhoto = {
          id: photo.id,
          photo_id: photoId,
          album_id: albumId,
          path: photo.path,
          name: photo.name,
          pre: hasPre,
          nxt: hasNxt
        }
        this.editPhotoName = this.bigPhoto.name
      }
    }
  }
</script>

<style scoped lang="less">
  .AlbumList {
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
  .album-sum {
    float: right;
    color: #cccccc;
  }
  .photo-sum {
    color: #aaaaaa;
  }
  .album-button {
    margin-left: 10px;
  }
  .album-card {
    text-align: left;
    margin-bottom: 10px;
    margin-top: 10px;
  }
  .album-ul {
  }
  .album-li {
    margin-top: 15px;
  }
  .more-photo {
    margin-top: 10px;
  }
  .edit-photo {
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
  .select-album {
    width: 200px;
    margin-left: auto;
    margin-top: 20px;
    margin-bottom: 20px;
  }
  .sum-photo-per-album {
    float: right;
    color: #cccccc;
  }
  .create-date {
    margin-top: 20px;
  }
  .intro {
    width: 70%;
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
</style>
