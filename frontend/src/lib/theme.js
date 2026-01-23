const KEY = "theme"

export function initTheme() {
  const saved = localStorage.getItem(KEY)
  const prefersDark = window.matchMedia?.("(prefers-color-scheme: dark)")?.matches
  const isDark = saved ? saved === "dark" : prefersDark

  document.documentElement.classList.toggle("dark", isDark)
}

export function toggleTheme() {
  const root = document.documentElement
  const isDark = root.classList.toggle("dark")
  localStorage.setItem(KEY, isDark ? "dark" : "light")
  return isDark
}
