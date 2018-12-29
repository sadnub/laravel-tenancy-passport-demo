<template>
  <v-layout align-center justify-center>
    <v-flex xs12 sm8 md4>
      <v-card class="elevation-12">
        <v-toolbar dark color="primary">
          <v-toolbar-title>Reset Password</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
          <form method="POST" action="/password/reset" aria-label="Reset Password">
            
            <input type="hidden" name="_token" :value="csrf_token">

            <input type="hidden" name="token" :value="token">

            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-model="email"
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
                  v-model="password"
                  v-validate="'required|min:6'"
                  data-vv-name="password"
                  :error-messages="errors.collect('password')"
                  type="password"
                  label="Password"
                  name="password"></v-text-field>
              </v-flex>
            </v-layout>
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-model="password_confirmation"
                  v-validate="'required|confirmed:password'"
                  data-vv-as="password"
                  type="password"
                  label="Password Confirmation"
                  name="password_confirmation"></v-text-field>
              </v-flex>
            </v-layout>

            <v-btn type="submit">Reset Password</v-btn>
          </form>
        </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
  export default {
    props: ['token'],
    data: () => ({
      email: '',
      password: '',
      passsword_confirmation: ''
    }),
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
            document.getElementById("reset_form").submit()
          }
        })
      }
    }
  }
</script>