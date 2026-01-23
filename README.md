# ReviewsAI Platform (Full-Stack SaaS)

Plateforme SaaS d’analyse d’avis (reviews) : authentification, rôles (admin/user), dashboard analytics, gestion des reviews, export CSV.

## Stack
- Backend: Laravel (API) + SQLite + Sanctum
- Frontend: Vue 3 + Vite + Tailwind CSS + Chart.js

## Fonctionnalités
### Auth & Sécurité
- Login/Register
- Auth token (Sanctum)
- Rôles: admin / user
- Accès admin protégé (middleware + guard Vue Router)

### Reviews
- CRUD complet (création, édition, suppression)
- Recherche + filtre sentiment + pagination
- Analyse automatique (sentiment/score/topics selon ton backend)
- Export CSV sécurisé (token requis)

### Dashboards
- **User dashboard**: scope “mine” + formulaire rapide pour envoyer une review
- **Admin dashboard**: scope “all” + vue globale
- Graphique sentiment + top topics + derniers avis

### Admin Panel
- Liste des utilisateurs (pagination + recherche)
- Visible uniquement pour admin

## Installation (local)
### Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed --seeder=DemoDataSeeder
php artisan serve
