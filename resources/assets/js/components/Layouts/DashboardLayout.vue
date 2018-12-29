<template>
    <v-app>
        <v-navigation-drawer 
            v-model="drawer"
            clipped
            fixed
            app>

            <v-list dense>
                <v-list-tile v-for="link in links" :to="{path: link.path}" :key="link.name">
                    <v-list-tile-action>
                        <v-icon>{{ link.icon }}</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ link.name }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>

        </v-navigation-drawer>

        <v-toolbar app fixed clipped-left>
            <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
            <v-toolbar-title>Ticket System</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-menu bottom left>
                <v-btn
                slot="activator"
                icon
                >
                    <v-icon large>account_circle</v-icon>
                </v-btn>

                <v-list>
                    <v-list-tile @click="logout">
                        <v-list-tile-title>Logout</v-list-tile-title>
                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            <input type="hidden" name="_token" :value="token">
                        </form>
                    </v-list-tile>
                </v-list>
            </v-menu>
        </v-toolbar>

        <v-content>
            <v-container>
                <router-view></router-view>
            </v-container>
        </v-content>
        <v-footer></v-footer>
    </v-app>
</template>

<script>
    import links from '@/sidebarLinks.js'
    import axios from '@/config/axios.js'

    export default {
        data: () => ({
            drawer: null,
            links: links
        }),
        computed: {
            token() {
                let token = document.head.querySelector('meta[name="csrf-token"]');
                return token.content
            }
        },
        methods: {
            logout() {
                document.getElementById('logout-form').submit()
            }
        }
    }
</script>