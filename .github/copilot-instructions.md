# Configuración del Proyecto - Crater Fork

## Información del Repositorio

### Repositorios
- **Origin (Tu Fork Privado)**: `git@github.com:DevDim-Tech/crater.git`
- **Upstream (Original)**: `https://github.com/crater-invoice/crater.git` (Solo lectura)

### Reglas Importantes
- NUNCA hacer push a upstream
- NUNCA modificar master directamente
- Todos los cambios van a origin (DevDim-Tech/crater)

## Estructura de Ramas

### Ramas Principales
- `master`: Sincronizada con upstream, NO modificar directamente
- `develop`: Rama principal de desarrollo del proyecto
- `feature/*`: Ramas para nuevas funcionalidades
- `hotfix/*`: Ramas para correcciones urgentes

### Workflow de Desarrollo
1. Trabajar SIEMPRE en ramas `feature/*` o `hotfix/*`
2. Crear nuevas features desde `develop`
3. Integrar features completadas en `develop`
4. Solo mergear a `master` cuando esté listo para producción

## Stack Tecnológico

### Backend
- Laravel 8.x
- PHP 8.2
- MySQL/MariaDB
- Redis (cache y colas)

### Frontend
- Vue.js 2.x (Crater usa Vue 2)
- TailwindCSS
- Inertia.js (implementación actual de Crater)

### Nuevas Integraciones a Desarrollar
- OpenAI SDK para generación de presupuestos con IA
- Módulo de presupuestos con IA

## Convenciones de Código

### Commits
Usar conventional commits:
- `feat(scope): descripción` - Nueva funcionalidad
- `fix(scope): descripción` - Corrección de bugs
- `docs(scope): descripción` - Documentación
- `refactor(scope): descripción` - Refactorización
- `test(scope): descripción` - Tests
- `chore(scope): descripción` - Tareas de mantenimiento

Ejemplos:
```
feat(budgets): add AI-powered budget generation
fix(invoices): correct tax calculation in multi-currency
docs(api): update OpenAI integration documentation
```

### Scopes Principales
- `budgets`: Módulo de presupuestos
- `invoices`: Facturas
- `ai`: Integración con OpenAI
- `api`: Endpoints API
- `ui`: Interfaz de usuario
- `database`: Migraciones y modelos
- `config`: Configuración

## Estructura del Proyecto

### Directorios Importantes
```
app/
├── AI/                     # Nuevo módulo de IA (a crear)
│   ├── Services/          # Servicios de OpenAI
│   ├── Interfaces/        # Contratos de IA
│   └── DTOs/              # Data Transfer Objects
├── Models/                # Modelos Eloquent
├── Http/
│   ├── Controllers/       # Controladores
│   └── Requests/          # Form Requests
└── Services/              # Servicios de negocio

resources/
├── js/                    # Componentes Vue.js
└── views/                 # Vistas Blade

database/
├── migrations/            # Migraciones
└── seeders/               # Seeders

docs/                      # Documentación del proyecto
└── steps/                 # Guías paso a paso
```

## Comandos Frecuentes

### Desarrollo
```bash
# Iniciar servidor de desarrollo
php artisan serve

# Compilar assets en modo desarrollo
npm run dev

# Compilar assets con watch
npm run watch

# Ejecutar migraciones
php artisan migrate

# Limpiar caché
php artisan optimize:clear
```

### Testing
```bash
# Ejecutar tests
php artisan test

# Ejecutar tests con coverage
php artisan test --coverage
```

### Git Workflow
```bash
# Crear nueva feature
git checkout develop
git checkout -b feature/nombre-feature

# Actualizar desde upstream
git fetch upstream
git checkout master
git merge upstream/master
git checkout develop
git merge master

# Push a tu fork
git push origin branch-name
```

## Configuración de Entorno

### Variables Importantes en .env
```
APP_ENV=local
APP_URL=http://crater.test
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=crater
OPENAI_API_KEY=tu_api_key  # A configurar
```

## Próximos Pasos de Desarrollo

### Fase 1: Setup ✅ COMPLETADO
1. ✅ Clonar repositorio
2. ✅ Configurar remotes (origin y upstream)
3. ✅ Configurar rama develop
4. ✅ Ejecutar migraciones
5. ✅ Instalar dependencias npm
6. ✅ Verificar instalación
   - Servidor Laravel: http://127.0.0.1:8000
   - Vite Dev Server: http://localhost:3000

### Fase 2: Integración OpenAI ✅ COMPLETADO
1. ✅ Instalar OpenAI SDK (openai-php/client v0.18.0)
2. ✅ Crear módulo AI con namespace Crater\AI
3. ✅ Implementar servicios base (OpenAIService)
4. ✅ Crear tests unitarios (3/3 passing)
5. ✅ Configurar GPUStack endpoint personalizado
6. ✅ Implementar prompts en español

### Fase 3: Módulo de Presupuestos 🚧 EN PROGRESO
1. ✅ Crear migraciones (budgets, budget_items)
2. ✅ Ejecutar migraciones
3. ✅ Crear modelos (Budget, BudgetItem)
4. ✅ Implementar relaciones entre modelos
5. ✅ Crear BudgetsController con métodos CRUD
6. ✅ Crear BudgetRequest y DeleteBudgetRequest para validación
7. ✅ Crear BudgetResource y BudgetItemResource
8. ✅ Crear BudgetPolicy con reglas de autorización
9. ✅ Registrar rutas API
10. ⏳ Crear componentes Vue.js
11. ⏳ Crear tests unitarios y de integración
12. ⏳ Probar generación de presupuestos con IA
13. ⏳ Documentar API endpoints

## Notas Importantes

- **Base de Datos**: Usar MySQL en WAMP (puerto 3306)
- **Servidor Web**: PHP built-in server o Apache de WAMP
- **Node.js**: Versión 22.x instalada
- **PHP**: Versión 8.2 instalada
- **Composer**: Versión 2.5.8

## Recursos

- [Documentación Crater Original](https://docs.craterapp.com/)
- [Laravel 8 Docs](https://laravel.com/docs/8.x)
- [OpenAI API Docs](https://platform.openai.com/docs)
- [Vue.js 2 Docs](https://v2.vuejs.org/)

## Contacto y Soporte

Este es un fork privado de Crater para DevDim-Tech.
El repositorio original está en: https://github.com/crater-invoice/crater
