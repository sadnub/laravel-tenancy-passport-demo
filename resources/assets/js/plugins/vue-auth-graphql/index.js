
import {login, refresh, logout} from '@/queries/auth.gql'

const Plugin = {
  install (Vue, options = {}) {

    //Requires a mechanism to set a cookie using $cookie instance property
    //Requires Vue Apollo to be installed
    const Cookie = Vue.prototype.$cookie
    const Apollo = Vue.prototype.$apollo

    //Add $auth api methods
    Vue.prototype.$auth = {

      //Holds cache for tokens
      store: {
        access_token: null,
        expiresIn: null,
        refresh_token: null
      },

      //Checks if access token is present
      check() {
        return this.store.access_token !== null
      },

      initiateAuth() {
        const refresh_token = this.getRefreshToken()

        if (refresh_token != null) {
          this.refresh().then(response => {
            return true
          })

        } else {
          return false
        }
      },

      //Attempts to log the user in with supplied credentials
      login(data) {
        return Apollo.mutate({
          mutation: login,
          variables: {
            data: data
          }
        })
        .then(({data: {login}}) => {
          this.setTokens(login.refresh_token, login.access_token, login.expires_in)
        })
      },

      //Logs the user out and clears local tokens
      logout() {
        this.store.access_token = null
        this.store.expiresIn = null
        this.store.refresh_token = null
        Cookie.delete('refresh_token')

        return Apollo.mutate({
          mutation: logout,
        })

      },

      //Uses a valid refresh token to obtain an access token
      refresh() {
        const refresh_token = this.getRefreshToken()

        if (refresh_token === null) {
          return null
        }

        return Apollo.mutate({
          mutation: refresh,
          variables: {
            data: {
              refresh_token: refresh_token
            }
          }
        })
        .then(({data: {refresh_token}}) => {
          this.setTokens(refresh_token.refresh_token, refresh_token.access_token, refresh_token.expires_in)
        })
      },

      //Checks for a cached refresh_token and then a cookie
      getRefreshToken() {
        if (this.store.refresh_token !== null) {
          return this.store.refresh_token
        } else {

          return Cookie.get('refresh_token')
        }
      },

      //Sets both access and refresh tokens in the cache and cookie
      setTokens(refresh, access, expires) {
        this.setAccessToken(access, expires)
        this.setRefreshToken(refresh)
      },

      //Sets access token in the cache
      setAccessToken(token, expires) {
        this.store.access_token = token
        this.store.expiresIn = expires
      },

      //Sets refresh token in cache and cookie
      setRefreshToken(token) {

        Cookie.set('refresh_token', token)
        this.store.refresh_token = token
      }
    }
  }
}

export default Plugin