const state = {
  authUser: {},
  token: '',
  status: false
}

const getters = {
}

const mutations = {
  SET_AUTH_USER (state, payload) {
    state.authUser = payload
  },
  SET_API_TOKEN (state, payload) {
    state.token = payload
  },
  SET_STATUS (state, payload) {
    state.status = payload
  }
}

const actions = {
}

export default {
  state,
  getters,
  mutations,
  actions
}
