<template>
  <div class="min-h-screen text-slate-100">
    <!-- background -->
    <div class="fixed inset-0 -z-10 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950"></div>
    <div
      class="fixed inset-0 -z-10 opacity-40"
      style="background-image:
        radial-gradient(circle at 20% 10%, rgba(99,102,241,.35), transparent 40%),
        radial-gradient(circle at 80% 30%, rgba(56,189,248,.22), transparent 35%),
        radial-gradient(circle at 60% 85%, rgba(34,197,94,.16), transparent 45%);"
    ></div>

    <div class="min-h-screen grid place-items-center px-4">
      <div class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- left: marketing -->
        <div class="hidden lg:flex glass glow rounded-3xl p-8 flex-col justify-between">
          <div>
            <div class="text-xl font-bold tracking-tight">ReviewsAI</div>
            <p class="mt-2 text-slate-300">
              Analyse intelligente des avis clients : sentiment, score, topics, dashboard, export.
            </p>

            <div class="mt-8 space-y-3 text-sm">
              <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-lg bg-white/10 border border-slate-700/50">⚡</span>
                <span class="text-slate-200">Dashboard temps réel</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-lg bg-white/10 border border-slate-700/50">🧠</span>
                <span class="text-slate-200">Analyse automatique IA</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-lg bg-white/10 border border-slate-700/50">🔒</span>
                <span class="text-slate-200">Accès sécurisé (tokens)</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-lg bg-white/10 border border-slate-700/50">📤</span>
                <span class="text-slate-200">Export CSV</span>
              </div>
            </div>
          </div>

          <div class="text-xs text-slate-400">
            © {{ new Date().getFullYear() }} ReviewsAI • Projet Full-Stack
          </div>
        </div>

        <!-- right: form -->
        <div class="glass glow rounded-3xl p-6 sm:p-8">
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>
