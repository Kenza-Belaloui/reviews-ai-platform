<script setup>
import { onMounted, onBeforeUnmount, ref, watch } from "vue"
import { Chart } from "chart.js/auto"

const props = defineProps({
  positive: { type: Number, default: 0 },
  neutral: { type: Number, default: 0 },
  negative: { type: Number, default: 0 },
})

const canvasRef = ref(null)
let chart = null

function render() {
  if (!canvasRef.value) return
  const ctx = canvasRef.value.getContext("2d")

  if (chart) chart.destroy()

  chart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["Positive", "Neutral", "Negative"],
      datasets: [
        {
          data: [props.positive, props.neutral, props.negative],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: "bottom" },
      },
    },
  })
}

onMounted(render)
watch(() => [props.positive, props.neutral, props.negative], render)

onBeforeUnmount(() => {
  if (chart) chart.destroy()
})
</script>

<template>
  <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
    <div class="font-semibold mb-3">Sentiment</div>
    <canvas ref="canvasRef" />
  </div>
</template>
