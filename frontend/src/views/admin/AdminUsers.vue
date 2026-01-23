<script setup>
import { onMounted, ref, watch } from "vue"
import { http } from "../../lib/http"
import { useToastStore } from "../../stores/toast"

const toast = useToastStore()
const loading = ref(true)
const rows = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const page = ref(1)
const q = ref("")
let t = null

function badge(role) {
  return role === "admin"
    ? "bg-indigo-500/15 text-indigo-200 border-indigo-500/30"
    : "bg-slate-500/15 text-slate-200 border-slate-500/30"
}

async function load() {
  loading.value = true
  try {
    const { data } = await http.get("/admin/users", { params: { page: page.value, q: q.value || undefined } })
    rows.value = data.data
    meta.value = data.meta
  } catch {
    toast.show("Accès admin/users impossible", "error")
  } finally {
    loading.value = false
  }
}

watch(q, () => {
  clearTimeout(t)
  t = setTimeout(() => { page.value = 1; load() }, 300)
})

watch(page, load)
onMounted(load)
</script>

<template>
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Admin • Utilisateurs</h1>
        <p class="text-sm text-slate-300">Gestion des comptes + contrôle d’accès.</p>
      </div>

      <div class="text-sm text-slate-300">
        Total: <b class="text-slate-100">{{ meta.total }}</b>
      </div>
    </div>

    <div class="flex gap-2">
      <input
        v-model="q"
        class="w-full px-3 py-2 rounded-xl border border-slate-700/60 bg-white/5"
        placeholder="Rechercher par nom ou email..."
      />
    </div>

    <div class="rounded-2xl border border-slate-700/60 bg-white/5 overflow-hidden">
      <div class="grid grid-cols-12 px-4 py-3 text-xs uppercase tracking-wide text-slate-300 border-b border-slate-700/60">
        <div class="col-span-4">Nom</div>
        <div class="col-span-5">Email</div>
        <div class="col-span-2">Role</div>
        <div class="col-span-1 text-right">ID</div>
      </div>

      <div v-if="loading" class="p-4 space-y-3">
        <div class="h-12 rounded-xl bg-white/5 animate-pulse"></div>
        <div class="h-12 rounded-xl bg-white/5 animate-pulse"></div>
        <div class="h-12 rounded-xl bg-white/5 animate-pulse"></div>
      </div>

      <div v-else-if="!rows.length" class="p-6 text-sm text-slate-300">
        Aucun résultat.
      </div>

      <div v-else>
        <div
          v-for="u in rows"
          :key="u.id"
          class="grid grid-cols-12 gap-2 px-4 py-3 border-b border-slate-700/40 hover:bg-white/5"
        >
          <div class="col-span-12 md:col-span-4 font-semibold">{{ u.name }}</div>
          <div class="col-span-12 md:col-span-5 text-slate-300">{{ u.email }}</div>
          <div class="col-span-6 md:col-span-2">
            <span class="text-xs px-2 py-1 rounded-lg border" :class="badge(u.role)">{{ u.role }}</span>
          </div>
          <div class="col-span-6 md:col-span-1 text-right text-slate-300">#{{ u.id }}</div>
        </div>
      </div>
    </div>

    <!-- pagination -->
    <div class="flex items-center justify-end gap-2 text-sm text-slate-300">
      <button
        class="px-3 py-1.5 rounded-xl border border-slate-700/60 hover:bg-white/5"
        :disabled="page <= 1"
        @click="page--"
      >
        Prev
      </button>

      <span class="px-3 py-1.5 rounded-xl border border-slate-700/60 bg-white/5">
        Page {{ meta.current_page }} / {{ meta.last_page }}
      </span>

      <button
        class="px-3 py-1.5 rounded-xl border border-slate-700/60 hover:bg-white/5"
        :disabled="page >= meta.last_page"
        @click="page++"
      >
        Next
      </button>
    </div>
  </div>
</template>
