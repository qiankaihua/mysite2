<template>
  <div class="SideBar">
    <Row type="flex">
      <i-col :span=spanLeft class="layout-menu-left">
        <Menu active-name="1-2" theme="dark" width="auto" :open-names="['1']">
          <div class="layout-header" >
            <Button type="text" @click="toggleClick(event)">
              <Icon type="navicon" color="white" size="32"></Icon>
            </Button>
          </div>
          <Submenu name="1">
            <template slot="title">
              <Icon type="ios-navigate"></Icon>
              Item 1
            </template>
            <MenuItem name="1-1">Option 1</MenuItem>
            <MenuItem name="1-2">Option 2</MenuItem>
            <MenuItem name="1-3">Option 3</MenuItem>
          </Submenu>
          <Submenu name="2">
            <template slot="title">
              <Icon type="ios-keypad"></Icon>
              Item 2
            </template>
            <MenuItem name="2-1">Option 1</MenuItem>
            <MenuItem name="2-2">Option 2</MenuItem>
          </Submenu>
          <Submenu name="3">
            <template slot="title">
              <Icon type="ios-analytics"></Icon>
              Item 3
            </template>
            <MenuItem name="3-1">Option 1</MenuItem>
            <MenuItem name="3-2">{{clientHeight}}</MenuItem>
          </Submenu>
        </Menu>
      </i-col>
      <i-col :span=24-spanLeft class="under"></i-col>
    </Row>
  </div>
</template>

<script>
  export default {
    components: {
    },
    name: 'SideBar',
    data () {
      return {
        spanLeft: 5,
        clientHeight: '1000',
        status: this.status,
        username: this.username,
        avatar: this.avatar
      }
    },
    mounted: function () {
      const that = this
      window.onresize = function temp () {
        that.clientHeight = `${document.documentElement.clientHeight}`
      }
    },
    methods: {
      toggleClick () {
        event.preventDefault()
        this.spanLeft = this.spanLeft === 5 ? 2 : 5
      }
    }
  }
</script>

<style scoped>
  .SideBar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    min-height: 100vh;
  }
  .layout-menu-left{
    z-index: 100;
    background: #464c5b;
    min-height:100vh;
  }
  .layout-header {
    text-align: right;
    color: gray;
  }
  .under {
    z-index: -1;
    background-color: #000;
    display: none;
  }
</style>
