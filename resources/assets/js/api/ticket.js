import axios from '@/config/axios.js'

export default {

    getTickets() {
        return axios.get('tickets')
    },

    getTicket(id) {
        return axios.get('tickets/'. id)
    },

    editTicket(id, data) {
        return axios.post('tickets/' . id, data)
    },

    deleteTicket(id) {
        return axios.delete('tickets/' . id)
    }
}