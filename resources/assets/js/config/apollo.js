import { ApolloClient } from 'apollo-client'
import { HttpLink } from 'apollo-link-http'
import { InMemoryCache } from 'apollo-cache-inmemory';
import VueApollo from 'vue-apollo'

let token = document.head.querySelector('meta[name="csrf-token"]')

const httpLink = new HttpLink({
  headers: {
    'X-CSRF-TOKEN': token.content,
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json'
  }
})

const cache = new InMemoryCache();

// Create the apollo client
const apollo = new ApolloClient({
  link: httpLink,
  cache,
  connectToDevTools: true,
})


export default new VueApollo({
  defaultClient: apollo

})