# Configuración de Docker para Crater

## Puerto de Nginx

El puerto de Nginx es configurable a través de la variable de entorno `DOCKER_NGINX_PORT` en el archivo `.env`.

### Configuración

Por defecto, Nginx se expondrá en el puerto **8888** del host.

Para cambiar el puerto, edita tu archivo `.env`:

```bash
# Docker Configuration
DOCKER_NGINX_PORT=8888  # Cambia este valor al puerto deseado
```

### Valores por defecto

Si no se especifica `DOCKER_NGINX_PORT` en el `.env`, Docker usará el puerto **8888** por defecto.

### Ejemplo de uso

```bash
# Usar puerto 8888 (por defecto)
DOCKER_NGINX_PORT=8888

# Usar puerto 8080
DOCKER_NGINX_PORT=8080

# Usar puerto 3000
DOCKER_NGINX_PORT=3000
```

## Acceso a la aplicación

Una vez configurado el puerto y levantados los contenedores:

```bash
docker-compose up -d
```

Puedes acceder a la aplicación en:
- http://localhost:8888 (o el puerto que hayas configurado)

## Otros servicios

### Base de datos (MariaDB)
- Puerto expuesto: **33006**
- Usuario: `crater`
- Contraseña: `crater`
- Base de datos: `crater`

Para conectarte desde el host:
```bash
mysql -h 127.0.0.1 -P 33006 -u crater -pcrater crater
```

## Comandos útiles

```bash
# Levantar contenedores
docker-compose up -d

# Ver logs
docker-compose logs -f

# Detener contenedores
docker-compose down

# Reconstruir contenedores
docker-compose up -d --build

# Entrar al contenedor de la aplicación
docker-compose exec app bash

# Ejecutar comandos de Laravel
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

## Troubleshooting

### Puerto 80 ocupado

Si el puerto 80 está ocupado (por ejemplo, por WAMP, XAMPP, Apache, etc.), simplemente cambia `DOCKER_NGINX_PORT` en `.env` a un puerto disponible:

```bash
DOCKER_NGINX_PORT=8888
```

### Ver qué puertos están en uso

**Windows (PowerShell):**
```powershell
netstat -ano | findstr :80
netstat -ano | findstr :8888
```

**Linux/Mac:**
```bash
lsof -i :80
lsof -i :8888
```
