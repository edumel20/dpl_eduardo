# Aplicación de Procesamiento de Imágenes con ngx_small_light

## Descripción del Proyecto

Esta práctica consiste en desplegar una aplicación web que permite generar "miniaturas" y aplicar efectos a imágenes en tiempo real utilizando el módulo **ngx_small_light** de Nginx. El módulo procesa las imágenes bajo demanda a través de peticiones URL, sin necesidad de preprocesarlas.

**Autor:** Eduardo Rabadán Melián  
**Curso:** 2º DAW (Desarrollo de Aplicaciones Web)  
**Módulo:** Administración de Servidores Web

---

## Objetivos

1. Instalar el módulo ngx_small_light y cargarlo dinámicamente en Nginx
2. Crear un virtual host específico para el dominio `images.eduardo.me`
3. Habilitar el módulo ngx_small_light en el virtual host solo para el location `/img`
4. Subir las 20 imágenes del archivo images.zip a la carpeta `img/`
5. Crear una aplicación web con formulario para tratamiento de imágenes
6. Incorporar certificado de seguridad SSL
7. Redirigir el subdominio www al dominio base (incluyendo SSL)

---

## 📁 Estructura del Repositorio

```
ut3/a1/
├── img/                                    # 📂 Carpeta de imágenes
│   ├── image01.jpg                         # Imágenes originales (01-20)
│   ├── image02.jpg
│   ├── ...
│   └── image20.jpg
│
├── src/                                    # 📂 Código fuente del módulo ngx_small_light
│   ├── ngx_http_small_light_module.c       # Módulo principal
│   ├── ngx_http_small_light_module.h       # Cabeceras del módulo
│   ├── ngx_http_small_light_imagemagick.c  # Soporte ImageMagick
│   ├── ngx_http_small_light_imagemagick.h
│   ├── ngx_http_small_light_jpeg.c         # Optimización JPEG
│   ├── ngx_http_small_light_jpeg.h
│   ├── ngx_http_small_light_param.c        # Procesamiento de parámetros
│   ├── ngx_http_small_light_param.h
│   ├── ngx_http_small_light_parser.c       # Parser de URLs
│   ├── ngx_http_small_light_parser.h
│   ├── ngx_http_small_light_size.c         # Cambio de tamaño
│   ├── ngx_http_small_light_size.h
│   ├── ngx_http_small_light_type.c         # Detección de tipos
│   ├── ngx_http_small_light_type.h
│   ├── ngx_http_small_light_gd.c           # Soporte GD library
│   ├── ngx_http_small_light_gd.h
│   └── ngx_http_small_light_imlib2.c       # Soporte Imlib2
│       └── ngx_http_small_light_imlib2.h
│
├── nginx-1.24.0/                           # 📂 Servidor Nginx (versión 1.24.0)
│   ├── objs/
│   │   ├── ngx_http_small_light_module.so  # Módulo compilado (.so)
│   │   └── nginx                           # Binario de Nginx
│   ├── conf/
│   │   ├── nginx.conf                      # Configuración principal
│   │   └── mime.types                      # Tipos MIME
│   ├── html/
│   │   ├── index.html                      # Página por defecto
│   │   └── 50x.html                        # Página de errores
│   ├── src/                                # Código fuente de Nginx
│   └── man/
│       └── nginx.8                         # Manual de Nginx
│
├── t/                                      # 📂 Tests del módulo ngx_small_light
│   ├── 01_simple.t                         # Test básico
│   ├── Util.pm                             # Utilidades de testing
│   ├── start_server.pl                     # Script de inicio
│   └── ngx_base/                           # Estructura base para tests
│
├── Build.PL                                # Script de construcción Perl
├── config.in                               # Configuración del módulo
├── config                                  # Configuración de construcción
├── ChangeLog                               # Historial de cambios
├── COPYING                                 # Licencia
├── index.html                              # ✅ Aplicación web principal
├── images.zip                              # Archivo con las 20 imágenes
└── README.md                               # ✅ Este archivo
```

---

## 🚀 Instalación y Configuración

### 1. Instalación de Dependencias

```bash
# Actualizar paquetes
sudo apt update

# Instalar dependencias necesarias
sudo apt install -y \
    build-essential \
    imagemagick \
    libpcre3 \
    libpcre3-dev \
    libmagickwand-dev \
    git \
    wget \
    libssl-dev \
    zlib1g-dev
```

### 2. Descarga del Módulo ngx_small_light

```bash
# Clonar el repositorio del módulo
git clone https://github.com/cubicdaiya/ngx_small_light.git

# Entrar en la carpeta del módulo
cd ngx_small_light

# Configurar el módulo
./setup
```

### 3. Compilación e Instalación de Nginx con el Módulo

```bash
# Descargar fuentes de Nginx (si no están disponibles)
wget http://nginx.org/download/nginx-1.24.0.tar.gz
tar -xzvf nginx-1.24.0.tar.gz
cd nginx-1.24.0

# Configurar Nginx con el módulo ngx_small_light
./configure \
    --with-compat \
    --add-dynamic-module=/home/daw2/ngx_small_light \
    --prefix=/usr/local/nginx \
    --with-http_ssl_module

# Compilar
make

# Instalar
sudo make install
```

### 4. Cargar el Módulo Dinámico en Nginx

**IMPORTANTE:** El módulo ngx_small_light debe cargarse en el archivo principal de Nginx (`nginx.conf`) ANTES de poder usarlo en los virtual hosts.

**Archivo:** `nginx.conf` (dentro de la carpeta del proyecto a1)

Copiar y pegar el siguiente contenido en el archivo `/home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf`:

```nginx
# === INICIO DEL ARCHIVO nginx.conf ===
# Este archivo debe estar en: /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf

# Cargar el módulo ngx_small_light dinámicamente
# Esta línea DEBE estar al inicio del archivo, fuera de cualquier bloque
load_module /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/ngx_http_small_light_module.so;

worker_processes auto;
error_log /home/daw2/Escritorio/dpl_eduardo/ut3/a1/logs/nginx-error.log;
pid /home/daw2/Escritorio/dpl_eduardo/ut3/a1/logs/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /home/daw2/Escritorio/dpl_eduardo/ut3/a1/mime.types;
    default_type application/octet-stream;
    
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';
    
    access_log /home/daw2/Escritorio/dpl_eduardo/ut3/a1/logs/nginx-access.log main;
    
    sendfile on;
    keepalive_timeout 65;
    
    # Incluir virtual hosts desde la carpeta del proyecto
    include /home/daw2/Escritorio/dpl_eduardo/ut3/a1/conf.d/*.conf;
}
```

**Crear estructura de directorios necesaria:**
```bash
# Crear directorios de configuración y logs
mkdir -p /home/daw2/Escritorio/dpl_eduardo/ut3/a1/logs
mkdir -p /home/daw2/Escritorio/dpl_eduardo/ut3/a1/conf.d
mkdir -p /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl

# Copiar tipos MIME desde Nginx
cp /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/conf/mime.types /home/daw2/Escritorio/dpl_eduardo/ut3/a1/mime.types
```

**Crear el archivo nginx.conf:**
```bash
# Crear el archivo con el contenido de arriba
nano /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf

# Pegar el contenido y guardar (Ctrl+O, Enter, Ctrl+X)
```

### 5. Copiar el Módulo Compilado

```bash
# Crear directorio de módulos si no existe
sudo mkdir -p /usr/local/nginx/modules

# Copiar el módulo compilado
sudo cp /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/ngx_http_small_light_module.so /usr/local/nginx/modules/

# Verificar que el módulo existe
ls -la /usr/local/nginx/modules/ngx_http_small_light_module.so
```

### 6. Generación del Certificado SSL

```bash
# Crear directorio para certificados dentro del proyecto
mkdir -p /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl

# Generar clave privada
openssl genrsa -out /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.key 2048

# Generar certificado autofirmado
openssl req -new -x509 \
    -key /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.key \
    -out /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.crt \
    -days 365 \
    -subj "/C=ES/ST=Spain/L=Localhost/O=DAW/CN=images.eduardo.me"

# Establecer permisos correctos
chmod 600 /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.key
chmod 644 /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.crt

# Ver el certificado
echo "=== Certificado SSL ===" && \
openssl x509 -in /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.crt -text -noout | head -20
```

### 7. Configuración del Virtual Host

**Archivo:** `conf.d/images.eduardo.me.conf` (dentro de la carpeta del proyecto a1)

```nginx
# === Servidor HTTP (Redirección a HTTPS) ===
server {
    listen 80;
    server_name images.eduardo.me www.images.eduardo.me;
    
    # Redirigir todo el tráfico a HTTPS
    return 301 https://$host$request_uri;
}

# === Servidor HTTPS Principal ===
server {
    listen 443 ssl;
    server_name images.eduardo.me;
    
    # Configuración SSL (certificados dentro del proyecto)
    ssl_certificate /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.crt;
    ssl_certificate_key /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # Ubicación para procesamiento de imágenes
    location /img/ {
        small_light on;
        small_light_mode 'cover';
        small_light_preserve_ratio 'on';
        
        # Ruta a las imágenes (dentro del proyecto)
        alias /home/daw2/Escritorio/dpl_eduardo/ut3/a1/img/;
        
        # Permitir listado de archivos
        autoindex on;
    }
    
    # Servir la aplicación web
    location / {
        root /home/daw2/Escritorio/dpl_eduardo/ut3/a1;
        index index.html;
    }
}

# === Redirección de www al dominio base ===
server {
    listen 443 ssl;
    server_name www.images.eduardo.me;
    
    # Configuración SSL
    ssl_certificate /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.crt;
    ssl_certificate_key /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.key;
    
    # Redirigir www al dominio base
    return 301 https://images.eduardo.me$request_uri;
}
```

### 8. Habilitar el Virtual Host y Reiniciar Nginx

```bash
# Verificar que la configuración del módulo está presente
head -5 /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf

# Verificar la configuración de Nginx usando el archivo del proyecto
sudo /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -t -c /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf

# Reiniciar Nginx
sudo pkill nginx
sudo /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -c /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf

# Verificar que el módulo está cargado
/home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -V 2>&1 | grep small_light
```

### 9. Iniciar Nginx (Método Alternativo)

Si prefieres usar el binario directamente:

```bash
# Detener Nginx si está ejecutándose
sudo pkill nginx

# Iniciar Nginx con la configuración del proyecto
sudo /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -c /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf

# Verificar que está escuchando
netstat -tlnp | grep nginx
```

---

## 📝 Uso de la Aplicación Web

### Acceder a la Aplicación

Abrir el navegador y navegar a: `https://images.eduardo.me`

### Formulario de Configuración

La aplicación proporciona los siguientes campos:

| Campo | Descripción | Ejemplo |
|-------|-------------|---------|
| **Tamaño (px)** | Lado de la imagen cuadrada | `300` |
| **Ancho del borde (px)** | Grosor del borde | `10` |
| **Color del borde** | Color en hexadecimal | `#FF6B6B` |
| **Enfoque** | Formato `radius x sigma` | `0x0.5` |
| **Desenfoque** | Formato `radius x sigma` | `10x2` |

### Generar Imágenes

1. Ajustar los parámetros en el formulario
2. Hacer clic en **"Generar Imágenes"**
3. Ver las 20 imágenes procesadas con los efectos aplicados

---

## 🔧 Parámetros de ngx_small_light

### Parámetros de Transformación

| Parámetro | Descripción | Ejemplo |
|-----------|-------------|---------|
| `dw` | Ancho de destino (px) | `300` |
| `dh` | Alto de destino (px) | `300` |
| `fmt` | Formato de salida | `jpeg`, `png`, `webp` |
| `q` | Calidad JPEG (1-100) | `85` |
| `ratio` | Ratio de aspecto | `1:1` |

### Parámetros de Borde

| Parámetro | Descripción | Ejemplo |
|-----------|-------------|---------|
| `bo` | Borde con formato | `10px_solid_#FF0000` |

### Parámetros de Efectos

| Parámetro | Descripción | Ejemplo |
|-----------|-------------|---------|
| `sharpen` | Enfoque (radius x sigma) | `0x0.5` |
| `blur` | Desenfoque (radius x sigma) | `10x2` |

### Ejemplo de URL

```
https://images.eduardo.me/img/image01.jpg?dw=300&dh=300&bo=10px_solid_#FF6B6B&sharpen=0x0.5&blur=10x2
```

---

## 📂 Gestión de Imágenes

### Descomprimir Imágenes

```bash
# Descomprimir el archivo images.zip
unzip images.zip

# Mover a la carpeta img (si es necesario)
mv images/* img/
```

### Verificar Imágenes

```bash
# Listar imágenes
ls -la img/

# Verificar acceso (HTTP)
curl -I http://localhost/img/image01.jpg

# Verificar acceso (HTTPS)
curl -kI https://images.eduardo.me/img/image01.jpg
```

### Permisos de Acceso

```bash
# Establecer permisos de lectura
chmod -R 644 img/

# Verificar propietario
chown -R $USER:$USER img/
```

---

## 🛠️ Solución de Problemas

### Error 403 Forbidden

**Causa:** Permisos incorrectos en la carpeta img

```bash
# Solución
chmod -R 755 img/
chmod 644 img/*.jpg
```

### Error 404 Not Found

**Causa:** Ruta incorrecta en la configuración de Nginx

```bash
# Verificar rutas
ls -la /home/daw2/Escritorio/dpl_eduardo/ut3/a1/img/

# Verificar configuración
sudo /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -t -c /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf
```

### Error: unknown directive "small_light"

**Causa:** El módulo ngx_small_light no está cargado en el archivo principal de Nginx.

**Síntoma:**
```
nginx: [emerg] unknown directive "small_light" in /home/daw2/Escritorio/dpl_eduardo/ut3/a1/conf.d/...
```

**Solución:**

1. **Verificar que el módulo existe:**
```bash
ls -la /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/ngx_http_small_light_module.so
```

2. **Verificar que el archivo nginx.conf tiene la línea load_module:**
```bash
head -5 /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf
```

Debería mostrar:
```nginx
load_module /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/ngx_http_small_light_module.so;
```

3. **Si falta la línea, añadirla al inicio de nginx.conf:**
```bash
nano /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf
```

Añadir esta línea como PRIMERA línea del archivo:
```
load_module /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/ngx_http_small_light_module.so;
```

4. **Verificar y reiniciar:**
```bash
sudo /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -t -c /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf
sudo pkill nginx
sudo /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -c /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf
```

5. **Verificar que el módulo está cargado:**
```bash
/home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -V 2>&1 | grep small_light
```

**Nota:** La directiva `load_module` DEBE estar fuera de cualquier bloque (http, events, etc.) y debe ser la primera línea del archivo.

### Error 500 Internal Server Error

**Causa:** El módulo ngx_small_light no está cargado

```bash
# Verificar módulos cargados
/home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -V

# Verificar que el módulo existe
ls -la /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/ngx_http_small_light_module.so

# Reiniciar Nginx
sudo pkill nginx
sudo /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx-1.24.0/objs/nginx -c /home/daw2/Escritorio/dpl_eduardo/ut3/a1/nginx.conf
```

### Error SSL

**Causa:** Certificado no encontrado o ruta incorrecta

```bash
# Verificar certificado
openssl x509 -in /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.crt -text -noout

# Regenerar certificado si es necesario
mkdir -p /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.key \
    -out /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/images.ssl.crt \
    -subj "/C=ES/ST=Spain/L=Localhost/O=DAW/CN=images.eduardo.me"
```

### Imágenes no se Procesan

**Causa:** Parámetros incorrectos en la URL

```bash
# Verificar respuesta de Nginx
curl -k "https://images.eduardo.me/img/image01.jpg?dw=300&dh=300"

# Verificar logs de Nginx
cat /home/daw2/Escritorio/dpl_eduardo/ut3/a1/logs/nginx-error.log
```

---

## 📜 Documentación Técnica

### Módulo ngx_small_light

**Repositorio:** https://github.com/cubicdaiya/ngx_small_light

El módulo ngx_small_light es un filtro de imágenes para Nginx que permite:

- 📐 **Redimensionamiento:** Cambiar el tamaño de imágenes
- 🎨 **Efectos:** Aplicar desenfoque, enfoque, sepia, etc.
- 🖼️ **Formatos:** Soporta JPEG, PNG, GIF, WebP
- ⚡ **Rendimiento:** Procesamiento on-the-fly sin pre-generación
- 🔌 **Carga dinámica:** Se puede cargar como módulo .so

### Nginx

**Versión:** 1.24.0  
**Documentación oficial:** https://nginx.org/en/docs/

### ImageMagick

**Propósito:** Motor de procesamiento de imágenes  
**Documentación:** https://imagemagick.org/

---

## 📊 URLs de Acceso

| Servicio | URL |
|----------|-----|
| **Aplicación Web** | https://images.eduardo.me |
| **Imágenes** | https://images.eduardo.me/img/image01.jpg |
| **API de pruebas** | https://images.eduardo.me/img/image01.jpg?dw=300&dh=300 |

---

## ✅ Verificación Final

```bash
# 1. Verificar Nginx está ejecutándose
ps aux | grep nginx

# 2. Verificar certificado SSL
echo "=== Certificado ===" && \
openssl x_client -connect images.eduardo.me:443 -servername images.eduardo.me 2>/dev/null | openssl x509 -noout -dates

# 3. Verificar acceso a imágenes
curl -k -I https://images.eduardo.me/img/image01.jpg

# 4. Verificar redirección www
curl -I http://www.images.eduardo.me

# 5. Verificar procesamiento de imágenes
curl -k "https://images.eduardo.me/img/image01.jpg?dw=200&dh=200" -o /tmp/test.jpg

# 6. Verificar estructura de archivos del proyecto
ls -la /home/daw2/Escritorio/dpl_eduardo/ut3/a1/
ls -la /home/daw2/Escritorio/dpl_eduardo/ut3/a1/conf.d/
ls -la /home/daw2/Escritorio/dpl_eduardo/ut3/a1/ssl/
```

---

## 📚 Conclusiones

Esta práctica ha permitido adquirir competencias en:

1. **Configuración de módulos dinámicos en Nginx**  
   Instalación y carga dinámica del módulo ngx_small_light

2. **Virtual Hosting con SSL/TLS**  
   Configuración de servidores virtuales con certificados de seguridad

3. **Procesamiento de imágenes en tiempo real**  
   Uso de ImageMagick y ngx_small_light para manipulación on-the-fly

4. **Desarrollo de aplicaciones web con JavaScript**  
   Creación de interfaces dinámicas para control de parámetros

5. **Administración de servidores web**  
   Configuración, seguridad y optimización de Nginx

---


