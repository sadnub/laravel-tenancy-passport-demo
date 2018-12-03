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

    export default {
        data: () => ({
            drawer: null,
            links: links
        }),
    }
</script>