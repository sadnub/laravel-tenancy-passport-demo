import axios from '@/config/axios.js'

export default {
	
	register(data) {
		return axios.post('register', data)
	},

	emailLink(data) {
		return axios.post('password/email', data)
	},

	resetPassword(data) {
		return axios.post('password/reset', data)
	},

	checkDomain(data) {
		return axios.post('checkDomain', data)
	}
}