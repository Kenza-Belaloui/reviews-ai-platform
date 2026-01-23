<script setup>
import { computed, onMounted, ref, watch } from "vue"
import { http } from "../../lib/http"
import { downloadCsv } from "../../lib/download"
import { useToastStore } from "../../stores/toast"

const toast = useToastStore()

// Table state
const loading = ref(true)
const rows = ref([])
const meta = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 })

// Filters
const q = ref("")
const sentiment = ref("") // positive|neutral|negative|""
const page = ref(1)

// Create/Edit modal
const modalOpen = ref(false)
const editing = ref(null) // review object or null
const formContent = ref("")
const saving = ref(false)

// Delete
const deletingId = ref(null)

// Debounce search
let t = null
watch([q, sentiment], () => {
  clearTimeout(t)
  t = setTimeout(() => {
    page.value = 1
    load()
  }, 300)
})

watch(page, () => load())

const pages = computed(() => {
  const cur = meta.value.current_page || 1
  const last = meta.value.last_page || 1
  const windowSize = 5
  const start = Math.max(1, cur - Math.floor(windowSize / 2))
  const end = Math.min(last, start + windowSize - 1)
  const out = []
  for (let i = start; i <= end; i++) out.push(i)
  return out
})

function badge(sent) {
  if (sent === "positive") return "bg-green-500/15 text-green-200 border-green-500/30"
  if (sent === "negative") return "bg-red-500/15 text-red-200 border-red-500/30"
  return "bg-slate-500/15 text-slate-200 border-slate-500/30"
}

async function load() {
  loading.value = true
  try {
    const params = { page: page.value }
    if (q.value) params.q = q.value
    if (sentiment.value) params.sentiment = sentiment.value

    const { data } = await http.get("/reviews", { params })
    // compatible avec pagination Laravel: { data:[], meta:{} } ou { data:[], links:{}, meta:{} }
    rows.value = data.data ?? data
    meta.value = data.meta ?? { current_page: 1, last_page: 1, per_page: 10, total: rows.value.length }
  } catch (e) {
    toast.show("Erreur chargement reviews", "error")
  } finally {
    loading.value = false
  }
}

function openCreate() {
  editing.value = null
  formContent.value = ""
  modalOpen.value = true
}

function openEdit(r) {
  editing.value = r
  formContent.value = r.content
  modalOpen.value = true
}

function closeModal() {
  modalOpen.value = false
  editing.value = null
  formContent.value = ""
}

async function save() {
  if (!formContent.value.trim()) {
    toast.show("Le contenu est obligatoire", "error")
    return
  }

  saving.value = true
  try {
    if (editing.value) {
      await http.put(`/reviews/${editing.value.id}`, { content: formContent.value })
      toast.show("Review mise à jour ✅", "success")
    } else {
      await http.post("/reviews", { content: formContent.value })
      toast.show("Review créée ✅", "success")
    }
    closeModal()
    await load()
  } catch (e) {
    toast.show("Erreur sauvegarde review", "error")
  } finally {
    saving.value = false
  }
}

async function remove(id) {
  deletingId.value = id
  try {
    await http.delete(`/reviews/${id}`)
    toast.show("Review supprimée ✅", "success")
    await load()
  } catch (e) {
    toast.show("Erreur suppression review", "error")
  } finally {
    deletingId.value = null
  }
}

async function exportCsv() {
  try {
    await downloadCsv("/export/reviews.csv", "reviews.csv")
    toast.show("Export CSV téléchargé ✅", "success")
  } catch {
    toast.show("Export impossible (token ?)", "error")
  }
}

onMounted(load)
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Reviews</h1>
        <p class="text-sm text-slate-300">Créer, filtrer, modifier, supprimer, exporter.</p>
      </div>

      <div class="flex gap-2">
        <button class="px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5 text-sm" @click="exportCsv">
          Export CSV
        </button>
        <button class="px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5 text-sm" @click="openCreate">
          + Nouvelle review
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-3">
      <input
        v-model="q"
        class="w-full md:w-1/2 px-3 py-2 rounded-xl border border-slate-700/60 bg-white/5"
        placeholder="Rechercher (ex: rapide, bug, design...)"
      />

      <select
        v-model="sentiment"
        class="px-3 py-2 rounded-xl border border-slate-700/60 bg-white/5 text-slate-100
              focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
      >
        <option class="bg-slate-900 text-slate-100" value="">Tous</option>
        <option class="bg-slate-900 text-slate-100" value="positive">Positive</option>
        <option class="bg-slate-900 text-slate-100" value="neutral">Neutral</option>
        <option class="bg-slate-900 text-slate-100" value="negative">Negative</option>
      </select>


      <button class="px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5" @click="load">
        ↻
      </button>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-slate-700/60 bg-white/5 overflow-hidden">
      <div class="grid grid-cols-12 px-4 py-3 text-xs uppercase tracking-wide text-slate-300 border-b border-slate-700/60">
        <div class="col-span-6">Contenu</div>
        <div class="col-span-2">Sentiment</div>
        <div class="col-span-1">Score</div>
        <div class="col-span-2">Date</div>
        <div class="col-span-1 text-right">Actions</div>
      </div>

      <div v-if="loading" class="p-4 space-y-3">
        <div class="h-14 rounded-xl bg-white/5 animate-pulse"></div>
        <div class="h-14 rounded-xl bg-white/5 animate-pulse"></div>
        <div class="h-14 rounded-xl bg-white/5 animate-pulse"></div>
      </div>

      <div v-else-if="!rows.length" class="p-6 text-sm text-slate-300">
        Aucune review. Clique sur <b>Nouvelle review</b>.
      </div>

      <div v-else>
        <div
          v-for="r in rows"
          :key="r.id"
          class="grid grid-cols-12 gap-2 px-4 py-3 border-b border-slate-700/40 hover:bg-white/5"
        >
          <div class="col-span-12 md:col-span-6 text-sm text-slate-100">
            {{ r.content }}
            <div v-if="r.topics?.length" class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="t in r.topics"
                :key="t"
                class="text-xs px-2 py-0.5 rounded-lg border border-slate-700/60 bg-white/5 text-slate-200"
              >
                {{ t }}
              </span>
            </div>
          </div>

          <div class="col-span-4 md:col-span-2 flex items-start">
            <span class="text-xs px-2 py-1 rounded-lg border" :class="badge(r.sentiment)">
              {{ r.sentiment || "pending" }}
            </span>
          </div>

          <div class="col-span-2 md:col-span-1 text-sm text-slate-200">
            {{ r.score ?? "—" }}
          </div>

          <div class="col-span-6 md:col-span-2 text-sm text-slate-300">
            {{ (r.created_at || "").toString().slice(0, 10) }}
          </div>

          <div class="col-span-12 md:col-span-1 flex justify-end gap-2">
            <button class="text-xs px-2 py-1 rounded-lg border border-slate-700/60 hover:bg-white/5" @click="openEdit(r)">
              Edit
            </button>
            <button
              class="text-xs px-2 py-1 rounded-lg border border-slate-700/60 hover:bg-white/5"
              :disabled="deletingId === r.id"
              @click="remove(r.id)"
            >
              {{ deletingId === r.id ? "..." : "Del" }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between text-sm text-slate-300">
      <div>
        Total: <b class="text-slate-100">{{ meta.total }}</b>
      </div>

      <div class="flex items-center gap-2">
        <button
          class="px-3 py-1.5 rounded-xl border border-slate-700/60 hover:bg-white/5"
          :disabled="page <= 1"
          @click="page = page - 1"
        >
          Prev
        </button>

        <button
          v-for="p in pages"
          :key="p"
          class="px-3 py-1.5 rounded-xl border border-slate-700/60 hover:bg-white/5"
          :class="p === page ? 'bg-white/10' : ''"
          @click="page = p"
        >
          {{ p }}
        </button>

        <button
          class="px-3 py-1.5 rounded-xl border border-slate-700/60 hover:bg-white/5"
          :disabled="page >= meta.last_page"
          @click="page = page + 1"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="modalOpen" class="fixed inset-0 z-50 grid place-items-center bg-black/60 p-4">
      <div class="w-full max-w-2xl rounded-2xl border border-slate-700/60 bg-slate-950 p-5">
        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="text-lg font-bold">
              {{ editing ? "Modifier la review" : "Nouvelle review" }}
            </div>
            <div class="text-sm text-slate-300">L’analyse IA se met à jour automatiquement.</div>
          </div>

          <button class="px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5" @click="closeModal">
            ✕
          </button>
        </div>

        <div class="mt-4 space-y-2">
          <label class="text-sm text-slate-300">Contenu</label>
          <textarea
            v-model="formContent"
            rows="6"
            class="w-full px-3 py-2 rounded-xl border border-slate-700/60 bg-white/5 text-slate-100"
            placeholder="Ex: Très bonne app, rapide, mais il manque un mode sombre..."
          />
        </div>

        <div class="mt-4 flex justify-end gap-2">
          <button class="px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5" @click="closeModal">
            Annuler
          </button>
          <button
            class="px-3 py-2 rounded-xl border border-slate-700/60 bg-white/10 hover:bg-white/15"
            :disabled="saving"
            @click="save"
          >
            {{ saving ? "..." : "Enregistrer" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
