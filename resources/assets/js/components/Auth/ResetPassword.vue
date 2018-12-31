<template>
  <v-layout align-center justify-center>
    <v-flex xs12 sm8 md4>
      <v-card class="elevation-12">
        <v-toolbar dark color="primary">
          <v-toolbar-title>Reset Password</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
              <v-alert
                v-model="show"
                :type="status"
                dismissible>
                {{ message }}
              </v-alert>
          <v-form aria-label="Reset Password">

            <input type="hidden" name="token" :value="token">

            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-model="input.email"
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
                  v-model="input.password"
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
                  v-model="input.password_confirmation"
                  v-validate="'required|confirmed:password'"
                  data-vv-name="password_confirmation"
                  type="password"
                  label="Password Confirmation"
                  name="password_confirmation"></v-text-field>
              </v-flex>
            </v-layout>

            <v-btn 
              color="primary"
              loading="loading">
              Reset Password
            </v-btn>
          </v-form>
        </v-card-text>
      </v-card>
    </v-flex>
    <v-dialog
      v-model="loading"
      width="300">
      <v-card>
        <v-card-text>
          {{ text }}
          <v-progress-linear
            indeterminate
            class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  import Auth from '@/api/auth.js'

  export default {
    inject: ['$validator'],
    props: ['token'],
    data: () => ({
      input: {
        email: '',
        password: '',
        passsword_confirmation: ''
      },

      //Dialog Data
      loading: false,
      text: '',

      //Alert Data
      status: 'success',
      message: '',
      show: false

    }),
    methods: {
      validate() {
        this.$validator.validateAll().then((result) => {

          if (result) {
            
            this.text = 'Resetting Password...'
            this.loading = true

          }
        })
      },
      submit() {
        Auth.resetPassword(this.input).then(({data}) => {

          this.message = data.message
          this.status = data.status
          this.loading = false
          this.show = true

          if (data.status === 'success'){

            this.text = 'Redirecting to Login Page...'
            this.loading = true

            setTimeout(() => {
              this.$router.push({name: 'auth.login'})
            }, 2000)
          }
        }).catch(error => {

          this.loading = false
          this.show = false
        })
      }
    }
  }
</script>