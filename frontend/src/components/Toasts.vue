<script setup>
import { useToastStore } from "../stores/toast"
const toast = useToastStore()
</script>

<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <div v-for="t in toast.items" :key="t.id"
      class="rounded-xl px-4 py-3 shadow border text-sm bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700">
      {{ t.message }}
    </div>
  </div>
</template>
