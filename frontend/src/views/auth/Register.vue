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
  } catch {
    toast.show("Impossible de créer le compte (email déjà utilisé ?)", "error")
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <div>
      <h1 class="text-xl font-bold">Créer un compte</h1>
      <p class="muted">Rejoins la plateforme et analyse tes avis.</p>
    </div>

    <div class="space-y-2">
      <label class="label">Nom</label>
      <input v-model="name" class="input" placeholder="Kenza" />
    </div>

    <div class="space-y-2">
      <label class="label">Email</label>
      <input v-model="email" type="email" class="input" placeholder="kenza@test.com" />
    </div>

    <div class="space-y-2">
      <label class="label">Mot de passe</label>
      <input v-model="password" type="password" class="input" placeholder="min 8 caractères" />
    </div>

    <button class="btn-primary w-full" :disabled="loading" @click="submit">
      {{ loading ? "Création..." : "Créer" }}
    </button>

    <div class="flex items-center justify-between text-sm">
      <span class="muted">Déjà un compte ?</span>
      <router-link class="font-semibold underline" to="/auth/login">Se connecter</router-link>
    </div>
  </div>
</template>
