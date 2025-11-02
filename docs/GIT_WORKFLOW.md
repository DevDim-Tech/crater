# Git Workflow - Fork de Crater

## Configuración Inicial (Ya realizada)

```bash
# 1. Remote origin (tu fork)
git remote add origin git@github.com:DevDim-Tech/crater.git

# 2. Remote upstream (repositorio original)
git remote add upstream https://github.com/crater-invoice/crater.git

# Verificar remotes
git remote -v
```

## Estructura de Ramas

### Ramas Principales
- **master**: Sincronizada con upstream, nunca modificar directamente
- **develop**: Rama principal de desarrollo
- **feature/nombre**: Ramas para nuevas funcionalidades
- **hotfix/nombre**: Ramas para correcciones urgentes

## Workflow Diario

### 1. Crear Nueva Funcionalidad
```bash
# Asegurarse de estar en develop actualizado
git checkout develop
git pull origin develop

# Crear rama de feature
git checkout -b feature/ai-budget-generator

# Trabajar en la funcionalidad...
# Hacer commits frecuentes
git add .
git commit -m "feat: implement AI budget generator"

# Subir a tu fork
git push origin feature/ai-budget-generator
```

### 2. Actualizar desde Upstream (Crater Original)
```bash
# Ir a master
git checkout master

# Traer cambios del upstream
git fetch upstream

# Mergear cambios del upstream en tu master
git merge upstream/master

# Subir cambios a tu fork
git push origin master

# Actualizar develop con los nuevos cambios
git checkout develop
git merge master
git push origin develop
```

### 3. Integrar Feature en Develop
```bash
# Desde la rama feature, actualizar con develop
git checkout feature/ai-budget-generator
git merge develop

# Resolver conflictos si los hay
# Hacer pruebas

# Volver a develop e integrar
git checkout develop
git merge feature/ai-budget-generator

# Subir a tu fork
git push origin develop
```

### 4. Preparar para Producción
```bash
# Cuando develop esté listo para producción
git checkout master
git merge develop
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin master --tags
```

## Comandos Útiles

### Actualización Rápida desde Upstream
```bash
# Script para actualizar todo
git fetch upstream
git checkout master
git merge upstream/master
git push origin master
git checkout develop
git merge master
git push origin develop
```

### Limpiar Ramas
```bash
# Listar ramas
git branch -a

# Eliminar rama local
git branch -d feature/nombre

# Eliminar rama remota
git push origin --delete feature/nombre
```

### Revertir Cambios
```bash
# Descartar cambios no commiteados
git checkout -- archivo.php

# Revertir último commit (mantener cambios)
git reset --soft HEAD~1

# Revertir último commit (descartar cambios)
git reset --hard HEAD~1
```

## Buenas Prácticas

### Commits
- **feat**: Nueva funcionalidad
- **fix**: Corrección de bug
- **docs**: Cambios en documentación
- **style**: Formato, espacios, etc.
- **refactor**: Refactorización de código
- **test**: Añadir o modificar tests
- **chore**: Tareas de mantenimiento

Ejemplo:
```bash
git commit -m "feat(budgets): add AI-powered budget generation"
git commit -m "fix(invoices): correct tax calculation"
git commit -m "docs(api): update endpoint documentation"
```

### Pull Requests
1. Crear PR desde feature a develop en tu fork
2. Revisar código
3. Hacer merge cuando esté aprobado
4. Eliminar rama feature

## Sincronización Periódica

### Semanal o según necesidad
```bash
# 1. Actualizar master desde upstream
git checkout master
git fetch upstream
git merge upstream/master
git push origin master

# 2. Actualizar develop
git checkout develop
git merge master
git push origin develop

# 3. Actualizar tus features activas
git checkout feature/nombre
git merge develop
# Resolver conflictos si hay
git push origin feature/nombre
```

## Estrategia de Versionado

### Semantic Versioning (SemVer)
- **MAJOR.MINOR.PATCH** (ej: 1.0.0)
- MAJOR: Cambios incompatibles
- MINOR: Nueva funcionalidad compatible
- PATCH: Correcciones compatibles

```bash
# Crear tag de versión
git tag -a v1.0.0 -m "Initial release with AI features"
git push origin --tags
```

## Resolver Conflictos

```bash
# Cuando hay conflicto en merge
git status  # Ver archivos conflictivos

# Editar archivos y resolver conflictos
# Buscar marcadores: <<<<<<<, =======, >>>>>>>

# Marcar como resuelto
git add archivo_resuelto.php

# Continuar merge
git commit
```

## Backup y Seguridad

```bash
# Crear backup de rama actual
git branch backup-$(date +%Y%m%d)

# Guardar cambios temporalmente
git stash
git stash list
git stash pop
```

## Resumen Rápido

**Desarrollo Normal:**
1. `git checkout develop`
2. `git checkout -b feature/nueva-funcionalidad`
3. Desarrollar y commitear
4. `git push origin feature/nueva-funcionalidad`
5. Merge a develop cuando esté listo

**Actualizar desde Crater Original:**
1. `git fetch upstream`
2. `git checkout master && git merge upstream/master`
3. `git checkout develop && git merge master`
4. Actualizar features activas con develop

**NUNCA hacer commits directos en master** - Siempre trabajar en ramas separadas.
