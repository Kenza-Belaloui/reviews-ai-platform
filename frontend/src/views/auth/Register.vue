<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useAuthStore } from "../../stores/auth"
import { useToastStore } from "../../stores/toast"

const router = useRouter()
const auth = useAuthStore()
const toast = useToastStore()

const name = ref("")
const email = ref("")
const password = ref("")
const loading = ref(false)

async function submit() {
  loading.value = true
  try {
    await auth.register({ name: name.value, email: email.value, password: password.value })
    toast.show("Compte créé ✅", "success")
    router.push("/")
  } catch (e) {
    toast.show("Erreur register (email déjà utilisé ?)", "error")
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <h1 class="text-2xl font-bold">Créer un compte</h1>

    <div class="space-y-2">
      <label class="text-sm">Nom</label>
      <input v-model="name" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950" />
    </div>

    <div class="space-y-2">
      <label class="text-sm">Email</label>
      <input v-model="email" type="email" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950" />
    </div>

    <div class="space-y-2">
      <label class="text-sm">Mot de passe</label>
      <input v-model="password" type="password" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950" />
    </div>

    <button @click="submit" :disabled="loading"
      class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white disabled:opacity-60">
      {{ loading ? "Création..." : "Créer" }}
    </button>

    <p class="text-sm text-center text-slate-500 dark:text-slate-400">
      Déjà un compte ?
      <router-link class="underline" to="/auth/login">Se connecter</router-link>
    </p>
  </div>
</template>
