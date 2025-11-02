# Paso 1: Configuración Inicial del Entorno

## Objetivos
- Configurar el entorno de desarrollo local
- Instalar todas las dependencias necesarias
- Preparar las herramientas de desarrollo

## Requisitos Previos
- Windows 10/11 o Linux/MacOS
- Acceso de administrador al sistema
- Conexión a Internet estable

## Pasos de Instalación

### 1. Instalar PHP 8.1+
```bash
# Windows (usando Chocolatey)
choco install php

# Verificar instalación
php -v
```

### 2. Instalar Composer 2.x
```bash
# Descargar y ejecutar Composer-Setup.exe
# Verificar instalación
composer --version
```

### 3. Instalar Node.js 16+
```bash
# Windows (usando Chocolatey)
choco install nodejs

# Verificar instalación
node --version
npm --version
```

### 4. Instalar PostgreSQL 13+
```bash
# Windows (usando Chocolatey)
choco install postgresql

# Verificar instalación
psql --version
```

### 5. Instalar Redis
```bash
# Windows (usando Chocolatey)
choco install redis-64

# Verificar instalación
redis-cli --version
```

### 6. Configurar PHP Extensions
Asegurarse de que las siguientes extensiones estén habilitadas en php.ini:
- BCMath
- Ctype
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD
- Fileinfo

### 7. Configurar Git
```bash
git config --global user.name "Tu Nombre"
git config --global user.email "tu@email.com"
```

## Verificación

Lista de comprobación:
- [ ] PHP instalado y funcionando (php -v)
- [ ] Composer instalado (composer --version)
- [ ] Node.js y npm instalados (node -v && npm -v)
- [ ] PostgreSQL instalado y funcionando (psql --version)
- [ ] Redis instalado y funcionando (redis-cli ping)
- [ ] Todas las extensiones PHP requeridas habilitadas
- [ ] Git configurado correctamente

## Solución de Problemas

### Problemas Comunes

1. **Error: PHP extension not found**
   ```bash
   # Windows
   php --ini
   # Editar el archivo php.ini y descomentar la extensión
   ```

2. **Error: PostgreSQL service not starting**
   ```bash
   # Windows
   net start postgresql
   ```

3. **Error: Redis connection refused**
   ```bash
   # Windows
   net start redis
   ```

## Siguiente Paso
Una vez completada la configuración del entorno, proceder a [STEP-2_CRATER_INSTALLATION.md](STEP-2_CRATER_INSTALLATION.md)