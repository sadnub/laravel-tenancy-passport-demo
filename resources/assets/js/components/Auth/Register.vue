<template>
  <v-layout align-center justify-center>
    <v-flex xs12 sm8 md4>
      <v-card class="elevation-12">
        <v-toolbar dark color="primary">
            <v-toolbar-title>Register</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
          <v-form aria-label="Register">
              
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-model="input.name"
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
                  v-model="input.fqdn"
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
                  v-model="input.password"
                  v-validate="'required|min:6'"
                  data-vv-name="password"
                  :error-messages="errors.collect('password')"
                  ref="password"
                  label="Password"
                  name="password"
                  type="password"></v-text-field>
              </v-flex>
            </v-layout>
            <v-layout row>
              <v-flex xs12>
                <v-text-field
                  v-model="input.password_confirmation"
                  v-validate="'required|confirmed:password'"
                  data-vv-as="password"
                  :error-messages="errors.collect('password_confirmation')"
                  label="Password Confirm"
                  name="password_confirmation"
                  type="password"></v-text-field>
              </v-flex>
            </v-layout>
                
            <v-btn 
              @click="validate" 
              :loading="loading" 
              color="primary">Submit</v-btn>
          </v-form>
        </v-card-text>
      </v-card>
    </v-flex>

    <v-dialog
      v-model="show"
      persistent
      width="300">
      <v-card>
        <v-card-text>
          {{message}}
          <div v-if="url">
          <p>Click on the URL to be directed to the personalized app login page</p>
          <p>
            <a :href="url">{{ url }}</a>
          </p>
          </div>
          <v-progress-linear
            v-show="loading"
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
    data: () => ({
      input: {
        name: '',
        email: '',
        fqdn: '',
        password: '',
        passsword_confirmation: ''
      },

      //Dialog Data
      url: null,
      message: '',
      show: false,
      loading: false
    }),
    methods: {
      validate() {

        this.$validator.validateAll().then((result) => {
          if (result) {
            
            this.loading = true
            this.show = true
            this.message = 'Registering...'

            this.submit()
          } 
        })
      },
      submit(){
        Auth.register(this.input).then(({data}) => {

          this.loading = false
          this.message = data.message
          this.url = data.redirect

        }).catch(error => {

          this.loading = false
          this.show = false
          this.url = null

        })
      }
    }
  }
</script>