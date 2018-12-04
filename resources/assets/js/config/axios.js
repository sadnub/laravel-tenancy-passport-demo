import axios from 'axios'

let token = document.head.querySelector('meta[name="csrf-token"]');

export default axios.create({
    baseURL: '/api/v1/',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': token.content
    }
})
