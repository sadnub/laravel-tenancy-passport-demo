<template>
	<v-layout align-center justify-center>
    <v-flex xs12 sm8 md4>
	    <v-card class="elevation-12">
        <v-toolbar dark color="primary">
            <v-toolbar-title>Reset Password</v-toolbar-title>
        </v-toolbar>
          <v-card-text>
            <v-alert
            v-if="$session.has('status')"
            :value="true"
            dismissible
            type="success"
            >
            {{ $session.get('status') }}
            </v-alert>

            <form id="reset_form" method="POST" action="/password/email" aria-label="Reset Password">
              
              <input type="hidden" name="_token" :value="csrf_token">

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

              <v-btn @click="validate">Send Reset Link</v-btn>
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
	          document.getElementById("reset_form").submit()
	        }
	      })
      }
    }
  }
</script>