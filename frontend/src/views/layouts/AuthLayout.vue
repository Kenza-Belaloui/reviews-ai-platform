<template>
  <div class="min-h-screen text-slate-900 dark:text-slate-100">
    <!-- background -->
    <div
      class="fixed inset-0 -z-10 bg-gradient-to-br
             from-slate-100 via-white to-indigo-100
             dark:from-slate-950 dark:via-slate-900 dark:to-indigo-950"
    ></div>

    <div
      class="fixed inset-0 -z-10 opacity-40"
      style="background-image:
        radial-gradient(circle at 20% 10%, rgba(99,102,241,.35), transparent 40%),
        radial-gradient(circle at 80% 30%, rgba(56,189,248,.22), transparent 35%),
        radial-gradient(circle at 60% 85%, rgba(34,197,94,.16), transparent 45%);"
    ></div>

    <div class="min-h-screen grid place-items-center px-4">
      <div class="w-full max-w-md glass glow rounded-3xl p-6 sm:p-8">
        <!-- left: marketing -->

        <!-- right: form -->
        <div class="glass glow rounded-3xl p-6 sm:p-8">
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>
