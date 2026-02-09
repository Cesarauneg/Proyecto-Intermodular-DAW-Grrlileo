# Canela's Desk

Proyecto web inspirado en Animal Crossing que combina una enciclopedia interactiva, museo personal, estadísticas y un minijuego de memoria. La app está construida con Laravel + Inertia + Vue 3 y usa MySQL para persistencia.

## Características
1. **Welcome** con secciones dinámicas: Inicio, Critterpedia, Temporada, Vecinos, Museo y Estadísticas.
2. **Critterpedia** con filtros y búsqueda por categoría (bichos, peces, marinos, fósiles, arte).
3. **Museo personal** con progreso del usuario y contadores por colección.
4. **Vecinos** con filtros y paginación.
5. **Reproductor de música** horario con mascota (Totakeke).
6. **Memory Game** por niveles con mejores tiempos y movimientos (solo usuarios autenticados).

## Stack
- **Backend**: Laravel (PHP 8.2+), MySQL
- **Frontend**: Vue 3 (Composition API), Inertia.js, Tailwind CSS, Vite
- **Auth**: Laravel Breeze

## Estructura del proyecto
- `Backend/` contiene la app Laravel completa (backend + frontend Inertia/Vue).
- `Backend/resources/js/` páginas y componentes Vue.
- `Backend/resources/css/` estilos globales y por sección.
- `Backend/public/` assets (imágenes, música, iconos).
- `Backend/database/` migraciones y seeders.
- `Backend/routes/` rutas web y auth.

## Requisitos
- PHP 8.2+
- Composer
- Node.js 18+ (recomendado)
- MySQL/MariaDB

## Configuración local (rápido)
```bash
cd Backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run dev
```

En otra terminal:
```bash
php artisan serve
```

## Variables de entorno mínimas (.env)
- `APP_NAME=ACpedia`
- `APP_URL=http://localhost:8000`
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=...`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`

Si usas Laragon/XAMPP, ajusta host/puerto según tu instalación.

## Usuarios y datos de prueba
El seeder crea un usuario:
- **email**: `admin@correo.com`
- **password**: `password`

También carga vecinos, peces, bichos, fósiles, arte, criaturas marinas y música horaria.

## Scripts útiles (Backend)
```bash
composer run dev     # servidor + colas + logs + vite
composer run setup   # instala todo, migra y compila
php artisan migrate:fresh --seed
php artisan route:list
```

## Memory Game
- Ruta: `/games/memory/{nivel}` (niveles 1–5).
- Integrado en Welcome dentro del dropdown “Juegos”.
- Guarda mejor tiempo y movimientos por usuario y nivel.
- Tabla: `game_scores` (user_id, level, time_seconds, moves).
- Lógica: solo guarda si mejora el tiempo (o empate con menos movimientos).

## Endpoints y rutas relevantes
- `/` Welcome (secciones dinámicas).
- `/profile` Perfil (auth).
- `/games/memory/{level}` Memory (auth).
- `/games/memory/{level}/data` JSON del juego (auth).
- `/games/memory/score` guarda score (auth).
- `/user/*` colecciones del usuario (auth:sanctum).

## Diagrama rápido de módulos
```
Welcome (Inertia)
├─ Inicio (regalo + cumpleaños)
├─ Critterpedia (bichos/peces/marinos/fósiles/arte)
├─ Temporada (disponibilidad por mes)
├─ Vecinos (filtros + paginación)
├─ Museo (colecciones + progreso)
├─ Estadísticas (resumen de museo)
└─ Juegos
   └─ Memory (niveles 1–5, score por usuario)
```

## Colecciones y relaciones
| Colección | Tabla principal | Tabla pivote con users | Campo pivote | Nota |
|---|---|---|---|---|
| Peces | `fish` | `fish_user` | `donated_to_museum` | Donación al museo |
| Bichos | `bugs` | `bug_user` | `donated_to_museum` | Donación al museo |
| Fósiles | `fossils` | `fossil_user` | `donated_to_museum` | Donación al museo |
| Arte | `art` | `art_user` | `donated_to_museum` | Donación al museo |
| Criaturas marinas | `sea_creatures` | `sea_creature_user` | `donated_to_museum` | Donación al museo |
| Vecinos | `villagers` | `user_villager` | `is_favorite` | Favoritos |

## Base de datos
Migraciones y seeders ya incluyen:
- Villagers, peces, bichos, fósiles, arte, criaturas marinas.
- Música horaria y canciones de Totakeke.

## Frontend (Inertia/Vue)
- Páginas en `Backend/resources/js/Pages/`.
- Componentes en `Backend/resources/js/Components/`.
- Estilos globales en `Backend/resources/css/app.css`.
- Estilos por sección en `Backend/resources/css/pages/`.

## Assets
- Vecinos: `Backend/public/images/villagers/{file}.png`
- Reverso de carta: `Backend/public/images/logos/favicon.png`
- Música y logos: `Backend/public/music/` y `Backend/public/images/logos/`

## Notas
- Para cambios de estilos o UI, revisar `Backend/resources/css/` y `Backend/resources/js/`.
- Si cambias el esquema, recuerda ejecutar migraciones.
- Si modificas rutas o componentes Inertia, reinicia `npm run dev`.
fin
