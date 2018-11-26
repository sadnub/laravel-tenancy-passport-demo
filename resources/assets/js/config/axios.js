import axios from 'axios'

let token = document.head.querySelector('meta[name="csrf-token"]');
let BASE_PATH = '/api/v1/'

export default axios.create({
    baseUrl: BASE_PATH,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': token.content
    }
})
