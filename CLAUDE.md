# Finance Analyzer — Analizador Financiero Personal

## Cómo correr el proyecto

```bash
# Servidor de desarrollo (desde la raíz del proyecto)
C:\Users\buo45\php\php.exe -S localhost:8000 -t public

# Limpiar caché
C:\Users\buo45\php\php.exe bin/console cache:clear

# Correr migraciones
C:\Users\buo45\php\php.exe bin/console doctrine:migrations:migrate --no-interaction
```

## Stack

- **PHP 8.4** (instalado en `C:\Users\buo45\php`)
- **Symfony 7.4** (skeleton + bundles manuales)
- **SQLite** (`var/data.db`) — no necesita servidor de BD
- **Bootstrap 5 CDN** con tema oscuro (`data-bs-theme="dark"`)
- **Sin build step** — no hay Webpack, Vite, ni Node.js

## Arquitectura

### Entidad única: `FinancialProfile`
- **Single-user app** — solo existe un perfil, creado automáticamente por `FinancialProfileRepository::getOrCreate()`
- Columnas JSON para income, expenses, financialAssets, physicalAssets, liabilities
- No crear entidades separadas para assets/liabilities — el diseño JSON es intencional

### Servicios
- `CalculationEngine` — toda la lógica matemática financiera (totales, ratios, prioridades)
- `InsightsEngine` — genera alertas/consejos basados en los datos del usuario

### Controllers
- `DashboardController` (`/`) — dashboard de solo lectura con cálculos e insights
- `ProfileController` (`/profile/edit`, `/profile/reset`) — CRUD del perfil

## Reglas y convenciones

1. **Idioma**: Toda la UI, mensajes, labels y comentarios están en **español**
2. **Moneda**: Pesos mexicanos (**MXN**) — usar `MoneyType` con `'currency' => 'MXN'`
3. **Referencias financieras mexicanas**: Afore, CETES, fondos indexados, SAT — no usar términos de USA/Canadá
4. **Forms dinámicos**: Los `CollectionType` usan `data-prototype` + vanilla JS en `edit.html.twig` — no agregar dependencias JS
5. **No over-engineer**: Es una app personal, no agregar auth, API endpoints, ni features no solicitados
6. **Tema oscuro**: Bootstrap 5 con `data-bs-theme="dark"` en `<html>` — mantener consistencia visual
7. **CSRF**: Las acciones destructivas (como reset) deben tener protección CSRF
8. **Sin Webpack/Node**: Todo el CSS/JS viene de CDN o es inline — no introducir build tools

## Estructura clave

```
src/
  Controller/        → DashboardController, ProfileController
  Dto/               → CalculationResult, Insight, InsightCollection
  Entity/            → FinancialProfile (única entidad)
  Form/              → 6 form types (Profile, Income, Expenses, FinancialAsset, PhysicalAsset, Liability)
  Repository/        → FinancialProfileRepository (con getOrCreate())
  Service/           → CalculationEngine, InsightsEngine
templates/
  base.html.twig     → Layout principal con Bootstrap 5 CDN
  dashboard/         → 5 templates (index + 4 partials)
  profile/           → 6 templates (edit + 5 partials)
```

## Composer

```bash
# Instalar dependencias
C:\Users\buo45\composer\composer.bat install

# Agregar paquete
C:\Users\buo45\composer\composer.bat require nombre/paquete
```
