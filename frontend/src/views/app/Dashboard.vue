<script setup>
import { onMounted, ref } from "vue"
import { http } from "../../lib/http"
import { useToastStore } from "../../stores/toast"
import SentimentChart from "../../components/SentimentChart.vue"
import { downloadCsv } from "../../lib/download"

const toast = useToastStore()
const loading = ref(true)
const data = ref(null)
const newContent = ref("")
const creating = ref(false)

async function load() {
  loading.value = true
  try {
    const res = await http.get("/dashboard")
    data.value = res.data
  } catch {
    toast.show("Dashboard: erreur API (token/backend)", "error")
  } finally {
    loading.value = false
  }
}

async function exportCsv() {
  try {
    await downloadCsv("/export/reviews.csv", "reviews.csv")
    toast.show("Export CSV téléchargé ✅", "success")
  } catch {
    toast.show("Export CSV impossible (token/back)", "error")
  }
}


function pill(sentiment) {
  if (sentiment === "positive") return "bg-green-500/15 text-green-200 border-green-500/30"
  if (sentiment === "negative") return "bg-red-500/15 text-red-200 border-red-500/30"
  return "bg-slate-500/15 text-slate-200 border-slate-500/30"
}

async function createReview() {
  if (!newContent.value.trim()) {
    toast.show("Écris un commentaire avant d’envoyer", "error")
    return
  }
  creating.value = true
  try {
    await http.post("/reviews", { content: newContent.value })
    toast.show("Review envoyée ✅", "success")
    newContent.value = ""
    await load() // refresh dashboard
  } catch {
    toast.show("Impossible d’envoyer la review", "error")
  } finally {
    creating.value = false
  }
}

onMounted(load)
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
        <p class="text-sm text-slate-300">Vue d’ensemble ({{ data?.scope || "..." }})</p>
      </div>

      <div class="flex gap-2">
        <button
          class="px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5 text-sm"
          @click="exportCsv"
        >
          Export CSV
        </button>

        <button
          class="px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5 text-sm"
          @click="load"
        >
          ↻ Refresh
        </button>
      </div>

    </div>

    <!-- KPI -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="glass glow rounded-2xl p-4">
        <div class="text-sm text-slate-300">Total reviews</div>
        <div class="mt-2 text-4xl font-bold">{{ loading ? "—" : data.total_reviews }}</div>
      </div>

      <div class="glass glow rounded-2xl p-4">
        <div class="text-sm text-slate-300">Score moyen</div>
        <div class="mt-2 text-4xl font-bold">{{ loading ? "—" : (data.avg_score ?? "—") }}</div>
        <div class="text-xs text-slate-300 mt-1">sur 100</div>
      </div>

      <div class="glass glow rounded-2xl p-4">
        <div class="text-sm text-slate-300">Répartition</div>
        <div class="mt-3 flex flex-wrap gap-2 text-sm">
          <span class="px-3 py-1 rounded-xl border" :class="pill('positive')">+ {{ loading ? "—" : data.sentiments.positive }}</span>
          <span class="px-3 py-1 rounded-xl border" :class="pill('neutral')">= {{ loading ? "—" : data.sentiments.neutral }}</span>
          <span class="px-3 py-1 rounded-xl border" :class="pill('negative')">- {{ loading ? "—" : data.sentiments.negative }}</span>
        </div>
      </div>
    </div>

    <!-- User quick action -->
  <div v-if="!loading && data?.scope === 'mine'" class="glass glow rounded-2xl p-4">
    <div class="flex items-start justify-between gap-3">
      <div>
        <div class="font-semibold">Nouvelle review</div>
        <div class="text-sm text-slate-300">Donne ton avis et l’IA analysera automatiquement.</div>
      </div>
      <router-link
        class="px-3 py-2 rounded-xl border border-slate-700/60 hover:bg-white/5 text-sm"
        to="/reviews"
      >
        Voir mes reviews
      </router-link>
    </div>

    <div class="mt-3 space-y-2">
      <textarea
        v-model="newContent"
        rows="4"
        class="w-full px-3 py-2 rounded-xl border border-slate-700/60 bg-white/5 text-slate-100"
        placeholder="Ex: Très bonne app, rapide, mais il manque..."
      />
      <div class="flex justify-end">
        <button
          class="px-4 py-2 rounded-xl border border-slate-700/60 bg-white/10 hover:bg-white/15"
          :disabled="creating"
          @click="createReview"
        >
          {{ creating ? "Envoi..." : "Envoyer" }}
        </button>
      </div>
    </div>
  </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="glass glow rounded-2xl p-4">
        <div class="font-semibold mb-2">Sentiment</div>
        <SentimentChart
          v-if="!loading"
          :positive="data.sentiments.positive"
          :neutral="data.sentiments.neutral"
          :negative="data.sentiments.negative"
        />
        <div v-else class="h-64 rounded-xl bg-white/5 animate-pulse"></div>
      </div>

      <div class="glass glow rounded-2xl p-4">
        <div class="font-semibold mb-3">Top topics</div>
        <div v-if="loading" class="space-y-2">
          <div class="h-8 rounded-xl bg-white/5 animate-pulse"></div>
          <div class="h-8 rounded-xl bg-white/5 animate-pulse"></div>
          <div class="h-8 rounded-xl bg-white/5 animate-pulse"></div>
        </div>
        <div v-else-if="!data.top_topics?.length" class="text-sm text-slate-300">Aucun topic détecté.</div>
        <div v-else class="space-y-2">
          <div v-for="t in data.top_topics" :key="t.topic" class="flex items-center justify-between">
            <span class="text-sm px-3 py-1 rounded-xl border border-slate-700/60 bg-white/5">{{ t.topic }}</span>
            <span class="text-sm text-slate-300">{{ t.count }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="glass glow rounded-2xl p-4">
      <div class="font-semibold mb-3">Derniers avis</div>

      <div v-if="loading" class="space-y-3">
        <div class="h-16 rounded-xl bg-white/5 animate-pulse"></div>
        <div class="h-16 rounded-xl bg-white/5 animate-pulse"></div>
      </div>

      <div v-else-if="!data.recent_reviews?.length" class="text-sm text-slate-300">Aucun avis pour le moment.</div>

      <div v-else class="space-y-3">
        <div v-for="r in data.recent_reviews" :key="r.id" class="rounded-xl border border-slate-700/60 bg-white/5 p-3">
          <div class="flex items-center justify-between">
            <div class="text-sm font-semibold">{{ r.user?.name || "User" }}</div>
            <span class="text-xs px-2 py-1 rounded-lg border" :class="pill(r.sentiment)">
              {{ r.sentiment || "pending" }} • {{ r.score ?? "—" }}
            </span>
          </div>
          <div class="text-sm text-slate-200 mt-2">{{ r.content }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
