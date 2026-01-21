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
const loading = ref(false)

async function submit() {
  loading.value = true
  try {
    await auth.login({ email: email.value, password: password.value })
    toast.show("Connexion réussie ✅", "success")
    router.push("/")
  } catch {
    toast.show("Identifiants incorrects", "error")
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <div>
      <h1 class="text-xl font-bold">Connexion</h1>
      <p class="muted">Accède à ton dashboard et gère tes reviews.</p>
    </div>

    <div class="space-y-2">
      <label class="label">Email</label>
      <input v-model="email" type="email" class="input" placeholder="admin@reviews.local" />
    </div>

    <div class="space-y-2">
      <label class="label">Mot de passe</label>
      <input v-model="password" type="password" class="input" placeholder="••••••••" />
    </div>

    <button class="btn-primary w-full" :disabled="loading" @click="submit">
      {{ loading ? "Connexion..." : "Se connecter" }}
    </button>

    <div class="flex items-center justify-between text-sm">
      <span class="muted">Pas de compte ?</span>
      <router-link class="font-semibold underline" to="/auth/register">Créer un compte</router-link>
    </div>

    <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-3 text-xs text-slate-600 dark:text-slate-300">
      <div class="font-semibold mb-1">Admin (test)</div>
      <div>Email: <b>admin@reviews.local</b></div>
      <div>Pass: <b>Admin12345!</b></div>
    </div>
  </div>
</template>
