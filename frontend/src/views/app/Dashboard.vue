<script setup>
import { onMounted, ref } from "vue"
import { http } from "../../lib/http"
import { useToastStore } from "../../stores/toast"
import SentimentChart from "../../components/SentimentChart.vue"

const toast = useToastStore()

const loading = ref(true)
const data = ref(null)

async function load() {
  loading.value = true
  try {
    const res = await http.get("/dashboard")
    data.value = res.data
  } catch (e) {
    toast.show("Impossible de charger le dashboard (token ? backend ?)", "error")
  } finally {
    loading.value = false
  }
}

function badgeClass(sentiment) {
  if (sentiment === "positive") return "bg-green-100 text-green-900 dark:bg-green-950 dark:text-green-100 border-green-200 dark:border-green-900"
  if (sentiment === "negative") return "bg-red-100 text-red-900 dark:bg-red-950 dark:text-red-100 border-red-200 dark:border-red-900"
  return "bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-slate-100 border-slate-200 dark:border-slate-700"
}

onMounted(load)
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm">
          Vue d’ensemble ({{ data?.scope || "..." }})
        </p>
      </div>

      <a
        v-if="!loading"
        class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800"
        :href="'http://127.0.0.1:8000/api/export/reviews.csv'"
        target="_blank"
      >
        Export CSV
      </a>
    </div>

    <!-- Cards -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="h-24 rounded-2xl bg-white/60 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 animate-pulse"></div>
      <div class="h-24 rounded-2xl bg-white/60 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 animate-pulse"></div>
      <div class="h-24 rounded-2xl bg-white/60 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 animate-pulse"></div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
        <div class="text-sm text-slate-500 dark:text-slate-400">Total reviews</div>
        <div class="text-3xl font-bold mt-1">{{ data.total_reviews }}</div>
      </div>
      <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
        <div class="text-sm text-slate-500 dark:text-slate-400">Score moyen</div>
        <div class="text-3xl font-bold mt-1">{{ data.avg_score ?? "—" }}</div>
      </div>
      <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
        <div class="text-sm text-slate-500 dark:text-slate-400">Répartition</div>
        <div class="mt-2 flex gap-2 text-sm">
          <span class="px-2 py-1 rounded-xl border"
            :class="badgeClass('positive')">+ {{ data.sentiments.positive }}</span>
          <span class="px-2 py-1 rounded-xl border"
            :class="badgeClass('neutral')">= {{ data.sentiments.neutral }}</span>
          <span class="px-2 py-1 rounded-xl border"
            :class="badgeClass('negative')">- {{ data.sentiments.negative }}</span>
        </div>
      </div>
    </div>

    <!-- Chart + Topics -->
    <div v-if="!loading" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <SentimentChart
        :positive="data.sentiments.positive"
        :neutral="data.sentiments.neutral"
        :negative="data.sentiments.negative"
      />

      <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
        <div class="font-semibold mb-3">Top topics</div>
        <div v-if="!data.top_topics?.length" class="text-sm text-slate-500 dark:text-slate-400">Aucun topic détecté.</div>
        <div v-else class="space-y-2">
          <div v-for="t in data.top_topics" :key="t.topic" class="flex items-center justify-between text-sm">
            <span class="px-2 py-1 rounded-xl bg-slate-100 dark:bg-slate-800">{{ t.topic }}</span>
            <span class="text-slate-500 dark:text-slate-400">{{ t.count }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent reviews -->
    <div v-if="!loading" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
      <div class="font-semibold mb-3">Derniers avis</div>

      <div v-if="!data.recent_reviews?.length" class="text-sm text-slate-500 dark:text-slate-400">
        Aucun avis pour le moment.
      </div>

      <div v-else class="space-y-3">
        <div v-for="r in data.recent_reviews" :key="r.id" class="p-3 rounded-xl border border-slate-200 dark:border-slate-800">
          <div class="flex items-center justify-between gap-2">
            <div class="text-sm font-semibold">{{ r.user?.name || "User" }}</div>
            <span class="text-xs px-2 py-1 rounded-xl border" :class="badgeClass(r.sentiment)">
              {{ r.sentiment || "pending" }} • {{ r.score ?? "—" }}
            </span>
          </div>
          <div class="text-sm mt-2 text-slate-700 dark:text-slate-200">
            {{ r.content }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
