<template>
  <v-layout align-center justify-center>
    <v-flex xs12 sm8 md4>
      <v-card class="elevation-12">
        <v-toolbar dark color="primary">
            <v-toolbar-title>Register</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
          <form id="register_form" method="POST" action="/register" aria-label="Register">
              
            <input type="hidden" name="_token" :value="csrf_token">
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-validate="'required|max:255'"
                  data-vv-name="name"
                  :error-messages="errors.collect('name')"
                  label="Name"
                  name="name"></v-text-field>
              </v-flex>
            </v-layout>
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-validate="'required|email|max:255'"
                  data-vv-name="email"
                  :error-messages="errors.collect('email')"
                  label="Email"
                  name="email"></v-text-field>
              </v-flex>
            </v-layout>
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-validate="'required|max:255'"
                  data-vv-name="fqdn"
                  :error-messages="errors.collect('fqdn')"
                  label="FQDN"
                  name="fqdn"
                  suffix=".app.itplog.com"></v-text-field>
              </v-flex>
            </v-layout>
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-validate="'required|min:6'"
                  data-vv-name="password"
                  :error-messages="errors.collect('password')"
                  label="Password"
                  name="password"
                  type="password"></v-text-field>
              </v-flex>
            </v-layout>
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-validate="'required|confirmed:password'"
                  data-vv-as="password"
                  label="Password Confirm"
                  name="password_confirmation"
                  type="password"></v-text-field>
              </v-flex>
            </v-layout>
                
            <v-btn @click="validate">Submit</v-btn>
          </form>
        </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>

  export default {
    computed: {
      csrf_token() {
        let token = document.head.querySelector('meta[name="csrf-token"]')
        return token.content
      }
    },
    methods: {
      validate() {
        this.$validator.validateAll().then((result) => {
          if (result) {
            
            //Manually submit form if not errors
            document.getElementById("register_form").submit()
          }
        })
      }
    }
  }
</script>