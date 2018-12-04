@extends('layouts.app')

@section('content')
    <v-layout align-center justify-center>
        <v-flex xs12 sm8 md4>
            <v-card class="elevation-12">
                <v-toolbar dark color="primary">
                    <v-toolbar-title>Register</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <form method="POST" action="{{ route('register') }}" aria-label="{{ __('Register') }}">
                        @csrf

                        <v-layout row>
                            <v-flex xs12>
                                <v-text-field
                                label="Name"
                                name="name"
                                @if ($errors->has('name'))
                                error-messages="{{ $errors->first('name') }}"
                                @endif
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
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
                                label="FQDN"
                                name="fqdn"
                                suffix=".app.itplog.com"
                                @if ($errors->has('fqdn'))
                                error-messages="{{ $errors->first('fqdn') }}"
                                @endif
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                        <v-layout row>
                            <v-flex xs12>
                                <v-text-field
                                label="Password"
                                name="password"
                                type="password"
                                @if ($errors->has('password'))
                                error-messages="{{ $errors->first('password') }}"
                                @endif
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                        <v-layout row>
                            <v-flex xs12>
                                <v-text-field
                                label="Password Confirm"
                                name="password_confirmation"
                                type="password"
                                @if ($errors->has('password_confirmation'))
                                error-messages="{{ $errors->first('password_confirmation') }}"
                                @endif
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                            
                        <v-btn type="submit">Submit</v-btn>
                    </form>
                </v-card-text>
            </v-card>
        </v-flex>
    </v-layout>
@endsection
