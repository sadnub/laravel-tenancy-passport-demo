//Imports
import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuetify from 'vuetify'
import VeeValidate from 'vee-validate'
import VueApollo from 'vue-apollo'
import App from '@/App'
import routes from '@/routes.js'
import apolloProvider from '@/config/apollo.js'
import VueAuth from '@/plugins/vue-auth-graphql'

//Load Plugins
Vue.use(VueRouter)
Vue.use(Vuetify)
Vue.use(VeeValidate, { inject: false })
Vue.use(VueApollo)
Vue.use(VueAuth)

//Router configuration
const router = new VueRouter({
  mode: 'history',
  routes 
})

export const vm = new Vue({
    el: '#app',
    render: h => h(App),
    apolloProvider,
    router
});
