//Imports
import Vue from 'vue'
import App from './App'

//Configurations
require('./config/axios.js')

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */



const app = new Vue({
    el: '#app',
    render: h => h(App)
});
