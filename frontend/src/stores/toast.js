import { defineStore } from "pinia"

export const useToastStore = defineStore("toast", {
  state: () => ({ items: [] }),
  actions: {
    show(message, type = "info") {
      const id = crypto.randomUUID()
      this.items.push({ id, message, type })
      setTimeout(() => this.remove(id), 2500)
    },
    remove(id) {
      this.items = this.items.filter((t) => t.id !== id)
    },
  },
})
