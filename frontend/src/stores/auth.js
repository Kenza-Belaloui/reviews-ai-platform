import { defineStore } from "pinia"
import { http } from "../lib/http"

export const useAuthStore = defineStore("auth", {
  state: () => ({
    token: localStorage.getItem("token") || "",
    user: JSON.parse(localStorage.getItem("user") || "null"),
  }),
  actions: {
    async register(payload) {
      const { data } = await http.post("/register", payload)
      this.setSession(data)
    },
    async login(payload) {
      const { data } = await http.post("/login", payload)
      this.setSession(data)
    },
    async logout() {
      try { await http.post("/logout") } catch {}
      this.token = ""
      this.user = null
      localStorage.removeItem("token")
      localStorage.removeItem("user")
    },
    setSession(data) {
      this.token = data.token
      this.user = data.user
      localStorage.setItem("token", data.token)
      localStorage.setItem("user", JSON.stringify(data.user))
    },
  },
})
