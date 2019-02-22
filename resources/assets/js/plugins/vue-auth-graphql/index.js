
import {login, refresh, logout, register, checkDomain, updatePassword, forgotPassword, check} from '@/queries/auth.gql'
import {Apollo} from '@/config/apollo.js'

const Plugin = {
  install (Vue, options = {}) {

    //Add $auth api methods
    Vue.prototype.$auth = {

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

        return Apollo.mutate({
          mutation: logout,
        })

      },
    }
  }
}

export default Plugin