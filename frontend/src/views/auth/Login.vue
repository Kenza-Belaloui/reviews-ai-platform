<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useAuthStore } from "../../stores/auth"
import { useToastStore } from "../../stores/toast"

const router = useRouter()
const auth = useAuthStore()
const toast = useToastStore()

const email = ref("")
const password = ref("")
const show = ref(false)
const loading = ref(false)

async function submit() {
  loading.value = true
  try {
    await auth.login({ email: email.value, password: password.value })
    toast.show("Connexion réussie ✅", "success")
    router.push("/")
  } catch (e) {
    toast.show("Connexion impossible : vérifie tes identifiants", "error")
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-2">
      <div class="text-2xl font-bold tracking-tight">Connexion</div>
      <p class="text-sm text-slate-300">
        Accède au dashboard et gère tes reviews.
      </p>
    </div>

    <div class="space-y-3">
      <div class="space-y-2">
        <label class="text-xs uppercase tracking-wide text-slate-300">Email</label>
        <input
          v-model="email"
          type="email"
          placeholder="ex: kenna@test.com"
          class="w-full px-4 py-3 rounded-2xl border border-slate-700/60 bg-white/5 focus:outline-none focus:ring-2 focus:ring-indigo-500/60"
        />
      </div>

      <div class="space-y-2">
        <label class="text-xs uppercase tracking-wide text-slate-300">Mot de passe</label>
        <div class="relative">
          <input
            v-model="password"
            :type="show ? 'text' : 'password'"
            placeholder="••••••••"
            class="w-full px-4 py-3 pr-12 rounded-2xl border border-slate-700/60 bg-white/5 focus:outline-none focus:ring-2 focus:ring-indigo-500/60"
          />
          <button
            type="button"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-sm px-2 py-1 rounded-lg border border-slate-700/60 hover:bg-white/5"
            @click="show = !show"
          >
            {{ show ? "Hide" : "Show" }}
          </button>
        </div>
      </div>
    </div>

    <button
      @click="submit"
      :disabled="loading"
      class="w-full px-4 py-3 rounded-2xl bg-indigo-500 hover:bg-indigo-400 text-slate-950 font-semibold disabled:opacity-60"
    >
      {{ loading ? "Connexion..." : "Se connecter" }}
    </button>

    <div class="flex items-center justify-between text-sm text-slate-300">
      <span>Pas de compte ?</span>
      <router-link class="underline hover:text-white" to="/auth/register">Créer un compte</router-link>
    </div>

    <!-- quick admin hint (optionnel) -->
    <div class="text-xs text-slate-400 border-t border-slate-700/50 pt-4">
    </div>
  </div>
</template>
