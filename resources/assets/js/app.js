//Imports
import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuetify from 'vuetify'
import App from '@/App'
import routes from '@/routes.js'

//Load Plugins
Vue.use(VueRouter)
Vue.use(Vuetify)

//Router configuration
const router = new VueRouter({
    routes 
  })

const app = new Vue({
    el: '#app',
    render: h => h(App),
    router
});
