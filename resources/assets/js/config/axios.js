import axios from 'axios'
import {vm} from '@/app.js'

let token = document.head.querySelector('meta[name="csrf-token"]')

const instance = axios.create({
    baseURL: '/api/v1/',
    headers: {
        'Content-Type': 'application/json',
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

    } else if (error.response.status === 422) {

        if (error.response.data.errors){
        
            for(let key in error.response.data.errors){
            
              vm.$validator.errors.add({field: key, msg: error.response.data.errors[key]})
            }
        }
    }

    return Promise.reject(error);
})

export default instance
