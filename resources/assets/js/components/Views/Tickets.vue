<template>
  <div>
    <v-toolbar flat color="white">
      <v-toolbar-title>Tickets</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical>
      </v-divider>
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog" max-width="500px">
        <v-btn slot="activator" color="primary" dark class="mb-2">New Ticket</v-btn>
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>

          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="ticket.title" label="Title"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="ticket.contact" label="Name"></v-text-field>
                </v-flex>
                <v-flex xs12 sm12 md12>
                  <v-textarea v-model="ticket.issue" label="Description"></v-textarea>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
            <v-btn color="blue darken-1" flat @click="submit">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="tickets"
      class="elevation-1"
    >
      <template slot="items" slot-scope="{ item }">
        <td>{{ item.id }}</td>
        <td>{{ item.title }}</td>
        <td>{{ item.status }}</td>
        <td>{{ item.contact }}</td>
        <td class="justify-center layout px-0">
          <v-icon
            small
            class="mr-2"
            @click="editItem(item)"
          >
            edit
          </v-icon>
          <v-icon
            small
            @click="deleteItem(item)"
          >
            delete
          </v-icon>
        </td>
      </template>
      <template slot="no-data">
        <p class="text-xs-center">No Data</p>
      </template>
    </v-data-table>
    <v-snackbar
      v-model="show"
      :color="color"
      :timeout="2000"
      :bottom="true"
      :left="true"
    >
      {{ message }}
      <v-btn
        dark
        flat
        @click="show = false"
      >
        Close
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script>

import Api from '@/api/ticket.js'

export default {
    data: () => {
        return {
            tickets: [],
            ticket: {
                title: '',
                contact: '',
                status: 'New',
                issue: ''
            },
            defaults: {
                title: '',
                contact: '',
                status: 'New',
                issue: ''
            },
            dialog: false,
            headers: [
                { text: '#', value: 'id'},
                { text: 'Title', value: 'title' },
                { text: 'Status', value: 'status' },
                { text: 'Contact', value: 'contact' }
            ],
            editedIndex: -1,

            /* Notification Settings */
            show: false,
            message: '',
            color: ''
        }
    },
    created() {
        Api.getTickets()
        .then(({ data }) => {
            this.tickets = data
        })
        .catch(error => {
            console.error(error)
            this.notify('Could not load Tickets', 'error')
        })
    },

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'New Ticket' : 'Edit Ticket'
        }
    },

    watch: {
        dialog (val) {
            val || this.close()
        }
    },
    methods: {
        editItem (item) {
            this.editedIndex = this.tickets.indexOf(item)
            this.ticket = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            const index = this.tickets.indexOf(item)
            confirm('Are you sure you want to delete this item?') && this.tickets.splice(index, 1)
        },

        close () {
            this.dialog = false
            setTimeout(() => {
                this.ticket = Object.assign({}, this.defaults)
                this.editedIndex = -1
            }, 300)
        },

        submit() {
            if (this.editedIndex > -1)
            {
                Api.editTicket(this.ticket.id, this.ticket)
                .then(({ data }) => {
                    Object.assign(this.tickets[this.editedIndex], this.ticket)
                    this.notify('The ticket was modified successfully', 'success')
                })
                .catch(response => {
                    console.error(response)
                    this.notify('There was an error editting the ticket', 'error')
                })
            } else {

                Api.addTicket(this.ticket)
                .then(({ data }) => {
                    tickets.push(data)
                    this.notify('The ticket was added successfully', 'success')
                    
                })
                .catch(response => {
                    console.error(response)
                    this.notify('There was an error adding the ticket', 'error')
                })
            }

            this.close()
        },

        notify(message, color) {
            this.message = message
            this.color = color
            this.show = true
        }
    }
}
</script>