<script setup>
import { useAuthStore } from "../../stores/auth"
import { useToastStore } from "../../stores/toast"
import { toggleTheme } from "../../lib/theme"
const auth = useAuthStore()
const toast = useToastStore()

async function doLogout() {
  await auth.logout()
  toast.show("Déconnecté ✅", "success")
}
</script>

<template>
  <div class="min-h-screen bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
    <div class="flex">
      <aside class="w-64 min-h-screen bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 p-4">
        <div class="text-lg font-bold">Reviews AI</div>
        <div class="mt-6 space-y-2">
          <router-link class="block px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800" to="/">Dashboard</router-link>
          <router-link class="block px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800" to="/reviews">Reviews</router-link>
        </div>
      </aside>

      <main class="flex-1">
        <header class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 backdrop-blur">
          <div class="text-sm">
            <div class="font-semibold">{{ auth.user?.name }}</div>
            <div class="text-slate-500 dark:text-slate-400">{{ auth.user?.role || 'user' }}</div>
          </div>
          <div class="flex gap-2">
            <button class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700" @click="toggleTheme()">🌓</button>
            <button class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700" @click="doLogout">Logout</button>
          </div>
        </header>

        <div class="p-6">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>
