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

import {getTickets, getTicket, createTicket, updateTicket, deleteTicket} from '@/queries/ticket.gql'

export default {
    inject: ['$validator'],
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
    apollo: {
      tickets: {
        query: getTickets
      },
      ticket: {
        query: getTicket,
        variables() {
          return {
            id: this.ticket.id
          }
        },
        deep: true,
        skip:true
      }
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
            this.$apollo.queries.ticket.skip = false
            this.ticket = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
          if (confirm('Are you sure you want to delete this item?'))
          {

            this.$apollo.mutate({
              mutation: deleteTicket,
              variables: {
                id: item.id
              },
              update: (store, { data: { deleteTicket } }) => {

                const data = store.readQuery({ query: getTickets })
                const index = data.tickets.indexOf(item)
                data.tickets.splice(index, 1)
                store.writeQuery({ query: getTickets, data })
              }
            })
            .then(() => {
              this.notify('The ticket was deleted successfully', 'success')
            })
            .catch((error) => {
              console.error({error})
              this.notify('There was an error deleting the ticket.', 'error')
            })
            
          }
        },

        close () {
            this.dialog = false
            this.$apollo.queries.ticket.skip = true
            setTimeout(() => {
                this.ticket = Object.assign({}, this.defaults)
                this.editedIndex = -1
            }, 300)
        },

        submit() {
            if (this.editedIndex > -1) {

                this.$apollo.mutate({
                  mutation: updateTicket,
                  variables: this.ticket,
                  optimisticResponse: {
                    __typename: 'Mutation',
                    updateTicket: {
                      __typename: 'Ticket',
                      ...this.ticket
                    }
                  }
                })
                .then(() => {
                  this.notify('The ticket was modified successfully', 'success')
                })
                .catch(error => {
                  this.notify('There was an error editting the ticket', 'error')
                  console.log({error})
                })

            } else {

                this.$apollo.mutate({
                  mutation: createTicket,
                  variables: this.ticket,
                  update: (store, { data: { createTicket } }) => {

                    const data = store.readQuery({ query: getTickets })
                    data.tickets.push(createTicket)
                    store.writeQuery({ query: getTickets, data })
                  },
                  optimisticResponse: {
                    __typename: 'Mutation',
                    createTicket: {
                      __typename: 'Ticket',
                      id: -1,
                      ...this.ticket
                    }
                  },
                })
                .then(() => {
                  this.notify('The ticket was added successfully', 'success')
                })
                .catch(error => {
                  this.notify('There was an error adding the ticket', 'error')
                  console.log({error})
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