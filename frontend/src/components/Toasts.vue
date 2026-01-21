<script setup>
import { useToastStore } from "../stores/toast"
const toast = useToastStore()
</script>

<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <div v-for="t in toast.items" :key="t.id"
      class="rounded-xl px-4 py-3 shadow border text-sm"
      :class="t.type==='success' ? 'bg-green-50 border-green-200 text-green-900 dark:bg-green-950 dark:text-green-100 dark:border-green-900'
        : t.type==='error' ? 'bg-red-50 border-red-200 text-red-900 dark:bg-red-950 dark:text-red-100 dark:border-red-900'
        : 'bg-slate-50 border-slate-200 text-slate-900 dark:bg-slate-900 dark:text-slate-100 dark:border-slate-700'">
      {{ t.message }}
    </div>
  </div>
</template>
