# Paso 2: Instalación de Crater

## Objetivos
- Clonar el repositorio de Crater
- Configurar el entorno de desarrollo
- Realizar la instalación inicial
- Verificar la instalación

## Requisitos Previos
- Completar [STEP-1_INITIAL_SETUP.md](STEP-1_INITIAL_SETUP.md)
- PostgreSQL ejecutándose
- Redis ejecutándose

## Pasos de Instalación

### 1. Clonar el Repositorio
```bash
# Crear directorio del proyecto
mkdir presu_factura
cd presu_factura

# Clonar Crater
git clone https://github.com/crater-invoice/crater.git .

# Cambiar a la última versión estable
git checkout $(git describe --tags $(git rev-list --tags --max-count=1))
```

### 2. Instalar Dependencias
```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias Node.js
npm install
```

### 3. Configurar el Entorno
```bash
# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar Base de Datos
```bash
# Crear base de datos
createdb crater

# Configurar .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=crater
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
```

### 5. Configurar Redis
```bash
# Configurar en .env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
```

### 6. Inicializar la Aplicación
```bash
# Ejecutar migraciones
php artisan migrate:fresh --seed

# Compilar assets
npm run dev

# Iniciar servidor de desarrollo
php artisan serve
```

## Verificación

### Lista de Comprobación
- [ ] Aplicación accesible en http://localhost:8000
- [ ] Poder iniciar sesión con credenciales por defecto
- [ ] Base de datos migrada correctamente
- [ ] Redis conectado y funcionando
- [ ] Assets compilados correctamente

### Credenciales por Defecto
- **Email:** admin@demo.com
- **Password:** 12345678

## Personalización Inicial

### 1. Configuración de la Empresa
```bash
# Ejecutar comando de configuración
php artisan crater:setup
```

### 2. Configuración de Correo
```bash
# Configurar en .env
MAIL_MAILER=smtp
MAIL_HOST=tu_servidor_smtp
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_contraseña
MAIL_ENCRYPTION=tls
```

## Solución de Problemas

### Problemas Comunes

1. **Error: No se puede conectar a la base de datos**
   - Verificar credenciales en .env
   - Verificar que PostgreSQL esté ejecutándose
   - Verificar permisos de usuario

2. **Error: No se pueden compilar los assets**
   - Limpiar caché de npm: `npm cache clean --force`
   - Reinstalar node_modules: `rm -rf node_modules && npm install`

3. **Error: Redis no conecta**
   - Verificar que Redis esté ejecutándose
   - Comprobar configuración en .env

## Siguiente Paso
Una vez completada la instalación de Crater, proceder a [STEP-3_OPENAI_INTEGRATION.md](STEP-3_OPENAI_INTEGRATION.md)