import { ApolloClient } from 'apollo-client'
import { HttpLink } from 'apollo-link-http'
import { InMemoryCache } from 'apollo-cache-inmemory';
import VueApollo from 'vue-apollo'
import { vm } from '@/app.js' 

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
export const Apollo = new ApolloClient({
  link: httpLink,
  cache,
  connectToDevTools: true,
})


export const apolloProvider = new VueApollo({
  defaultClient: Apollo,

  // Global error handler for all smart queries and subscriptions
  errorHandler: ({networkError, graphQLErrors}) => {

    if (networkError){

      // Redirect to Login on unauthenticated error
      if (networkError.statusCode === 401) {

        vm.$router.push({ name: 'auth.login' })


      }

      console.log(networkError)

    } else if (graphQLErrors){

      console.log(graphQLErrors)
    }
  }

})