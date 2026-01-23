<script setup>
import { computed } from "vue"
import { useAuthStore } from "../../stores/auth"
import { useToastStore } from "../../stores/toast"
import { toggleTheme } from "../../lib/theme"

const auth = useAuthStore()
const toast = useToastStore()

const initials = computed(() => {
  const n = auth.user?.name || "U"
  return n
    .split(" ")
    .slice(0, 2)
    .map((s) => s[0]?.toUpperCase())
    .join("")
})

async function doLogout() {
  await auth.logout()
  toast.show("Déconnecté ✅", "success")
}
</script>

<template>
<div class="min-h-screen text-slate-900 dark:text-slate-100">
  <!-- background -->
  <div class="fixed inset-0 -z-10 bg-gradient-to-br
              from-slate-100 via-white to-indigo-100
              dark:from-slate-950 dark:via-slate-900 dark:to-indigo-950"></div>

  <div
    class="fixed inset-0 -z-10 opacity-40"
    style="background-image:
      radial-gradient(circle at 20% 10%, rgba(99,102,241,.35), transparent 40%),
      radial-gradient(circle at 80% 30%, rgba(56,189,248,.22), transparent 35%),
      radial-gradient(circle at 60% 85%, rgba(34,197,94,.16), transparent 45%);"
  ></div> 

    <div class="mx-auto max-w-6xl px-4 py-6">
      <div class="grid grid-cols-12 gap-4">
        <!-- sidebar -->
        <aside class="col-span-12 lg:col-span-3 glass glow rounded-2xl p-4">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-lg font-bold tracking-tight">ReviewsAI</div>
              <div class="text-xs text-slate-300">Student SaaS • v1.0</div>
            </div>

            <button
              class="px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5"
              @click="toggleTheme()"
              title="Toggle theme"
            >
              🌓
            </button>
          </div>

          <!-- user card -->
          <div class="mt-6 flex items-center gap-3 rounded-2xl border border-slate-700/50 bg-white/5 p-3">
            <div class="h-10 w-10 rounded-xl bg-white/10 grid place-items-center font-bold">
              {{ initials }}
            </div>

            <div class="min-w-0">
              <div class="font-semibold truncate">{{ auth.user?.name }}</div>
              <div class="text-xs text-slate-300 truncate">{{ auth.user?.email }}</div>
              <div class="text-xs mt-1 inline-flex px-2 py-0.5 rounded-lg bg-white/10 border border-slate-700/50">
                {{ auth.user?.role || "user" }}
              </div>
            </div>
          </div>

          <!-- nav -->
          <nav class="mt-6 space-y-2">
            <router-link
              class="flex items-center gap-2 px-3 py-2 rounded-xl border border-transparent hover:border-slate-700/50 hover:bg-white/5"
              to="/"
            >
              <span>📊</span><span>Dashboard</span>
            </router-link>

            <router-link
              class="flex items-center gap-2 px-3 py-2 rounded-xl border border-transparent hover:border-slate-700/50 hover:bg-white/5"
              to="/reviews"
            >
              <span>📝</span><span>Reviews</span>
            </router-link>

            <!-- ADMIN ONLY -->
            <router-link
              v-if="auth.user?.role === 'admin'"
              class="flex items-center gap-2 px-3 py-2 rounded-xl border border-transparent hover:border-slate-700/50 hover:bg-white/5"
              to="/admin/users"
            >
              <span>🛡️</span><span>Admin</span>
            </router-link>
          </nav>

          <!-- logout -->
          <div class="mt-6">
          <button
            class="w-full px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5"
            @click="auth.logout()"
          >
            Logout
          </button>
          </div>
        </aside>

        <!-- content -->
        <main class="col-span-12 lg:col-span-9">
          <div class="glass glow rounded-2xl p-5">
            <router-view />
          </div>
        </main>
      </div>
    </div>
  </div>
</template>
