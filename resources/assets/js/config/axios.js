import axios from 'axios'
import {vm} from '@/app.js'

let token = document.head.querySelector('meta[name="csrf-token"]')

const instance = axios.create({
    baseURL: '/api/v1/',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': token.content
    }
})

// Add a response interceptor
instance.interceptors.response.use(
	response => {
    
    return response;
  }, 
  error => {

    if (error.response.status === 401) {
    	vm.$router.push({name: 'auth.login'});
    }
    return error;
})

export default instance
