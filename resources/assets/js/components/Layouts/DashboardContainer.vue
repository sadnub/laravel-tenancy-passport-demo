<template>
    <div>
        <navbar></navbar>
        <main class="py-4">


            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Dashboard</div>

                            <div class="card-body">
                                <router-view></router-view>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </main>
    </div>
</template>

<script>
import Navbar from '@/components/Layouts/Navbar.vue'

export default {
    components: {
        Navbar
    }
}
</script>