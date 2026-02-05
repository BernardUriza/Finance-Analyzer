# Analizador Financiero Personal

Una herramienta web para analizar tu situacion financiera personal. Rastrea ingresos, gastos, activos, deudas y obtiene insights inteligentes para tomar mejores decisiones con tu dinero.

**[Abrir la App](https://bernarduriza.github.io/Finance-Analyzer/app/)**

## Funcionalidades

- **Control de ingresos y gastos** — Registra y categoriza cada movimiento financiero mensual
- **Calculadora de patrimonio neto** — Suma activos, resta deudas, visualiza tu valor financiero real
- **Insights inteligentes** — Alertas automaticas sobre vivienda, deuda, ahorro e inversiones
- **5 prioridades financieras** — Sistema paso a paso: flujo de efectivo, deuda cara, fondo de emergencia, invertir, deuda manejable
- **100% privado** — Todos los datos se guardan en tu navegador (localStorage), nada se envia a ningun servidor

## Demo en Vivo

La app esta disponible como GitHub Page:

- **Landing page**: https://bernarduriza.github.io/Finance-Analyzer/
- **App**: https://bernarduriza.github.io/Finance-Analyzer/app/

## Stack Tecnico

### App Web (GitHub Pages - `docs/`)
- HTML5 + CSS3 + JavaScript vanilla
- Bootstrap 5 (CDN) con tema oscuro
- localStorage para persistencia de datos
- Sin dependencias, sin build step

### Backend (Symfony - `src/`)
- PHP 8.4 + Symfony 7.4
- SQLite con Doctrine ORM
- Entidad unica con columnas JSON
- Bootstrap 5 con tema oscuro

## Desarrollo Local

### Version estatica (recomendada)
```bash
# Cualquier servidor estatico sirve
cd docs
python -m http.server 8001
# Abrir http://localhost:8001
```

### Version Symfony
```bash
# Requiere PHP 8.2+
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php -S localhost:8000 -t public
```

## Estructura del Proyecto

```
finance-analyzer/
  docs/                    # GitHub Pages (version estatica)
    index.html             # Landing page
    app/index.html         # App completa en JS
  src/                     # Symfony (version backend)
    Controller/            # 2 controllers
    Dto/                   # CalculationResult, Insight, InsightCollection
    Entity/                # FinancialProfile (unica entidad)
    Form/                  # 6 form types
    Service/               # CalculationEngine, InsightsEngine
  templates/               # Twig templates
```

## Licencia

MIT

---

Hecho por [Bernard Uriza](https://github.com/BernardUriza)
