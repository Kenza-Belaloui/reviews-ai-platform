<template>
  <div class="min-h-screen grid place-items-center">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-200 to-white dark:from-slate-950 dark:to-slate-900"></div>

    <div class="w-full max-w-md">
      <div class="card p-6">
        <div class="mb-4">
          <div class="text-2xl font-black tracking-tight">ReviewsAI</div>
          <div class="muted">Plateforme d’analyse d’avis & dashboard IA.</div>
        </div>

        <router-view />
      </div>

      <div class="mt-4 text-center text-xs text-slate-500 dark:text-slate-400">
        © 2026 • Kenza • Fullstack project
      </div>
    </div>
  </div>
</template>
