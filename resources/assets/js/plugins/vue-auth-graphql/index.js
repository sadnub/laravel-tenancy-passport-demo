
import {login, refresh, logout, register, checkDomain, updatePassword, forgotPassword} from '@/queries/auth.gql'
import {Apollo} from '@/config/apollo.js'
import Cookie from 'js-cookie'

const Plugin = {
  install (Vue, options = {}) {

    //Add $auth api methods
    Vue.prototype.$auth = {

      //Checks if access token is present
      check() {
        return Cookie.get('access_token') !== null
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

      register(data) {
        return Apollo.mutate({
          mutation: register,
          variables: {
            data: data
          }
        })
      },

      checkDomain(data) {
        return Apollo.mutate({
          mutation: checkDomain,
          variables: {
            fqdn: data
          }
        })
      },

      resetForgottenPassword(data) {
        return Apollo.mutate({
          mutation: updatePassword,
          variables: {
            data: data
          }
        })
      },

      resetForgottenPassword(data) {
        return Apollo.mutate({
          mutation: updateForgottenPassword,
          variables: {
            data: data
          }
        })
      },

      //Attempts to log the user in with supplied credentials
      login(data) {
        return Apollo.mutate({
          mutation: login,
          variables: {
            data: data
          }
        })
      },

      //Logs the user out and clears local tokens
      logout() {

        Cookie.remove('refresh_token')
        Cookie.remove('access_token')

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

          return Cookie.get('refresh_token')
      },

      //Sets both access and refresh tokens in the cache and cookie
      setTokens(refresh, access, expires) {
        this.setAccessToken(access, expires)
        this.setRefreshToken(refresh)
      },

      //Sets access token in the cache
      setAccessToken(token, expires) {
        Cookie.set('access_token', token, {expires: expires})
      },

      //Sets refresh token in cache and cookie
      setRefreshToken(token) {

        Cookie.set('refresh_token', token, {expires: 1})
      }
    }
  }
}

export default Plugin