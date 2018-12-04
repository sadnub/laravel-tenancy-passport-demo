@extends('layouts.app')

@section('content')
<v-layout align-center justify-center>
    <v-flex xs12 sm8 md4>
        <v-card class="elevation-12">
            <v-toolbar dark color="primary">
                <v-toolbar-title>Reset Password</v-toolbar-title>
            </v-toolbar>
                <v-card-text>
                    @if (session('status'))
                    <v-alert
                    :value="true"
                    dismissible
                    type="success"
                    >
                    {{ session('status') }}
                    </v-alert>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" aria-label="{{ __('Reset Password') }}">
                        @csrf

                        <v-layout row>
                            <v-flex xs12>
                                <v-text-field
                                label="Email"
                                name="email"
                                @if ($errors->has('email'))
                                error-messages="{{ $errors->first('email') }}"
                                @endif
                                ></v-text-field>
                            </v-flex>
                        </v-layout>

                        <v-btn type="submit">Send Reset Link</v-btn>
                    </form>
                </v-card-text>
            </v-card>
    </v-flex>
</v-layout>
@endsection
