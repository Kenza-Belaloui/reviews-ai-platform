<script setup>
import Toasts from "./components/Toasts.vue"
import { initTheme } from "./lib/theme"
initTheme()
</script>

<template>
  <router-view />
  <Toasts />
</template>
