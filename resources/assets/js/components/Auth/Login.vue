<template>
  <v-layout align-center justify-center>
    <v-flex xs12 sm8 md4>
      <v-card class="elevation-12">
        <v-toolbar dark color="primary">
            <v-toolbar-title>Login</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
          <form @submit="validate" aria-label="Login">
            
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-model="email"
                  v-validate="'required|max:255'"
                  data-vv-name="email"
                  :error-messages="errors.collect('email')"
                  label="Email"
                  name="email"></v-text-field>
              </v-flex>
            </v-layout>
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-model="password"
                  v-validate="'required|min:6'"
                  data-vv-name="password"
                  :error-messages="errors.collect('password')"
                  label="Password"
                  name="password"
                  type="password"></v-text-field>
              </v-flex>
            </v-layout>

            <v-btn color="primary" type="submit" @click="validate">Login</v-btn>
            <router-link :to="{name: 'auth.email'}">Forgot Password?</router-link>
          </form>
        </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>

  export default {
    inject: ['$validator'],
    data: () => ({
      email: '',
      password: '',
      error: null
    }),
    methods: {
      validate() {
        this.$validator.validateAll().then((result) => {
          if (result) {
            
            this.$auth.login({email: this.email, password: this.password})
            .then(response => {
              this.$router.push({name: dashboard})
            })
            .catch(error => {
              this.error = "Username or password is incorrect"
            })
          }
        })
      }
    }
  }
</script>