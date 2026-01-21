export function initTheme() {
  const saved = localStorage.getItem("theme") || "dark"
  document.documentElement.classList.toggle("dark", saved === "dark")
}

export function toggleTheme() {
  const isDark = document.documentElement.classList.toggle("dark")
  localStorage.setItem("theme", isDark ? "dark" : "light")
  return isDark
}
