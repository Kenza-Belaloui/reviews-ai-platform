import { defineStore } from "pinia"
import router from "../router"
import { http, setAuthToken } from "../lib/http"

export const useAuthStore = defineStore("auth", {
  state: () => ({
    token: localStorage.getItem("token") || null,
    user: JSON.parse(localStorage.getItem("user") || "null"),
  }),

  actions: {
    async login(payload) {
      const res = await http.post("/login", payload)

      // attendu: { user: {...}, token: "..." }
      const { user, token } = res.data

      this.token = token
      this.user = user

      localStorage.setItem("token", token)
      localStorage.setItem("user", JSON.stringify(user))

      setAuthToken(token)
      return res.data
    },

    async logout() {
      try { await http.post("/logout") } catch {}

      this.token = null
      this.user = null
      localStorage.removeItem("token")
      localStorage.removeItem("user")
      setAuthToken(null)

      // ✅ redirect vers login
      await router.push("/auth/login")
    },
  },
})
