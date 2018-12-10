@extends('layouts.app')

@section('content')
<v-layout align-center justify-center>
    <v-flex xs12 sm8 md4>
        <v-card class="elevation-12">
            <v-toolbar dark color="primary">
                <v-toolbar-title>Login - {{ env('APP_NAME') }}</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
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
                    <v-layout row>
                        <v-flex xs12>
                            <v-text-field
                            label="Password"
                            name="password"
                            @if ($errors->has('password'))
                            error-messages="{{ $errors->first('password') }}"
                            @endif
                            ></v-text-field>
                        </v-flex>
                    </v-layout>

                    <v-btn type="submit">Login</v-btn>
                </form>
            </v-card-text>
        </v-card>
    </v-flex>
</v-layout>

       
@endsection
