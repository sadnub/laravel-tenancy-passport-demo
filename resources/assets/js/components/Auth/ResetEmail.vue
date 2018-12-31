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
              dismissible
              :type="status">
              {{ message }}
            </v-alert>

            <v-form aria-label="Reset Password">

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

              <v-btn 
                color="primary" 
                @click="validate" 
                :loading="loading">
                Send Reset Link
              </v-btn>
              <router-link :to="{name: 'auth.login'}">Login</router-link>
            </v-form>
          </v-card-text>
        </v-card>
    </v-flex>
	</v-layout>
</template>

<script>
  import Auth from '@/api/auth.js'

	export default {
    inject: ['$validator'],
    data: () => ({
      input: {
        email: '',
      },
      message: '',
      status: 'success',
      loading: false,
      show: false
    }),
    methods: {
      validate() {

        this.$validator.validateAll().then((result) => {

          this.show = false

          if (result) {
            
            this.loading = true
            this.submit()
          } 
        })
      },
      submit(){
        Auth.emailLink(this.input).then(({data}) => {

          console.log(data)

          this.status = data.status
          this.message = data.message
          this.loading = false
          this.show = true

        }).catch((error) => {

          this.loading = false

        })
      }
    }
  }
</script>