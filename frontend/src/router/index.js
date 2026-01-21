import { createRouter, createWebHistory } from "vue-router"
import { useAuthStore } from "../stores/auth"

import AuthLayout from "../views/layouts/AuthLayout.vue"
import AppLayout from "../views/layouts/AppLayout.vue"
import Login from "../views/auth/Login.vue"
import Register from "../views/auth/Register.vue"
import Dashboard from "../views/app/Dashboard.vue"
import Reviews from "../views/app/Reviews.vue"

const routes = [
  {
    path: "/",
    component: AppLayout,
    meta: { requiresAuth: true },
    children: [
      { path: "", name: "dashboard", component: Dashboard },
      { path: "reviews", name: "reviews", component: Reviews },
    ],
  },
  {
    path: "/auth",
    component: AuthLayout,
    children: [
      { path: "login", name: "login", component: Login },
      { path: "register", name: "register", component: Register },
    ],
  },
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.token) return { name: "login" }
  if (to.name === "login" && auth.token) return { name: "dashboard" }
})

export default router
