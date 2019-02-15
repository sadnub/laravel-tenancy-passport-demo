<template>
    <router-view></router-view>
</template>

<script>

  export default {
    mounted() {
      if(!this.$auth.initiateAuth()) {
        this.$router.push({name:login})
      }
    }
  }
</script>