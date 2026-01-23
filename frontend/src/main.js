import { initTheme } from "./lib/theme"
import { createApp } from "vue"
import { createPinia } from "pinia"
import router from "./router"
import "./style.css"
import App from "./App.vue"

initTheme()

createApp(App).use(createPinia()).use(router).mount("#app")
