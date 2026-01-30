<center>

# Administración de servidores web

</center>

***Nombre:*** Eduardo Rabadán Melián  
***Curso:*** 2º de Ciclo Superior de Desarrollo de Aplicaciones Web.

### ÍNDICE

+ [Introducción](#id1)
+ [Objetivos](#id2)
+ [Estructura del proyecto](#id3)
+ [Desarrollo](#id4)
+ [Conclusiones](#id5)

#### ***Introducción***. <a name="id1"></a>

Se va a realizar una práctica relacionada con la administración de servidores web. El objetivo de esta tarea es desplegar una aplicación web escrita en HTML/JavaScript que permita hacer uso del módulo de Nginx ngx_small_light. Este módulo sirve para generar "miniaturas" de imágenes on the fly además de otros posibles procesamientos a través de peticiones URL.

#### ***Objetivos***. <a name="id2"></a>

El objetivo de esta práctica es desplegar una aplicación web escrita en **HTML/JavaScript** que permita hacer uso del módulo de Nginx **ngx_small_light**.

Los objetivos específicos son:
- Instalar el módulo ngx_small_light y cargarlo dinámicamente en Nginx
- Crear un virtual host específico que atienda peticiones en el dominio `images.eduardo.me`
- Habilitar el módulo ngx_small_light en el virtual host sólo para el location `/img`
- Subir las imágenes de images.zip a una carpeta `img` dentro de la carpeta de trabajo
- Crear una aplicación web que permita el tratamiento de dichas imágenes
- Incorporar certificado de seguridad (mostrar el certificado 🔒)
- Redirigir el subdominio www al dominio base (incluyendo SSL)

#### ***Estructura del proyecto***. <a name="id3"></a>

```
a1/
├── img/                    # Imágenes de la aplicación (image01.jpg - image20.jpg)
├── src/                    # Código fuente del módulo ngx_small_light
│   ├── ngx_http_small_light_module.c
│   ├── ngx_http_small_light_module.h
│   ├── ngx_http_small_light_imagemagick.c
│   ├── ngx_http_small_light_jpeg.c
│   └── ... (otros archivos del módulo)
├── nginx-1.24.0/           # Servidor Nginx compilado con el módulo
│   ├── objs/               # Archivos objeto y módulo compilado
│   │   └── ngx_http_small_light_module.so
│   ├── conf/               # Archivos de configuración
│   └── html/               # Páginas por defecto
├── t/                      # Tests del módulo
├── index.html              # Aplicación web principal
├── images.zip              # Archivo con las imágenes
└── Build.PL                # Script de construcción
```

#### ***Desarrollo***. <a name="id4"></a>

##### **Instalación del módulo ngx_small_light**

Para la instalación del módulo seguir las siguientes instrucciones:

1. **Instalar las dependencias necesarias:**
```bash
sudo apt install -y build-essential imagemagick libpcre3 libpcre3-dev libmagickwand-dev
```

2. **Descargar el código fuente del módulo:**
```bash
git clone https://github.com/cubicdaiya/ngx_small_light.git
```

3. **Configurar el módulo:**
Entrar en la carpeta del módulo y ejecutar:
```bash
cd ngx_small_light
./setup
```

##### **Configuración de Nginx**

Para cargar dinámicamente el módulo, es necesario compilar Nginx añadiendo el módulo:

```bash
# Descargar fuentes de Nginx
wget http://nginx.org/download/nginx-1.24.0.tar.gz
tar -xzvf nginx-1.24.0.tar.gz
cd nginx-1.24.0

# Configurar con el módulo ngx_small_light
./configure --with-compat --add-dynamic-module=/ruta/a/ngx_small_light

# Compilar e instalar
make
sudo make install
```

##### **Crear el Virtual Host**

Crear un virtual host específico que atienda peticiones en el dominio `images.eduardo.me`.

**Nombre del archivo:** `images.eduardo.me` (en `/etc/nginx/sites-available/`)

```nginx
server {
    listen 80;
    server_name images.eduardo.me www.images.eduardo.me;
    
    # Redirigir HTTP a HTTPS
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name images.eduardo.me;
    
    # Configuración SSL
    ssl_certificate /etc/ssl/certs/images.ssl.crt;
    ssl_certificate_key /etc/ssl/private/images.ssl.key;
    
    # Habilitar módulo small_light para /img
    location /img/ {
        small_light on;
        small_light_mode 'cover';
        small_light_preserve_ratio 'on';

        alias /home/dpl_eduardo/dpl_eduardo/ut3/a1/img;
    }
    
    # Servir aplicación web
    location / {
        root /home/dpl_eduardo/dpl_eduardo/ut3/a1;
        index index.html;
    }
}

server {
    listen 443 ssl;
    server_name www.images.eduardo.me;
    
    # Configuración SSL
    ssl_certificate /etc/ssl/certs/images.ssl.crt;
    ssl_certificate_key /etc/ssl/private/images.ssl.key;
    
    # Redirigir www al dominio base
    return 301 https://images.eduardo.me$request_uri;
}
```

##### **Generar certificado SSL autofirmado**

```bash
# Crear directorio para certificados
sudo mkdir -p /etc/ssl/private
sudo mkdir -p /etc/ssl/certs

# Generar clave privada
sudo openssl genrsa -out /etc/ssl/private/images.ssl.key 2048

# Generar certificado
sudo openssl req -new -x509 \
    -key /etc/ssl/private/images.ssl.key \
    -out /etc/ssl/certs/images.ssl.crt \
    -days 365 \
    -subj "/C=ES/ST=Spain/L=Localidad/O=Centro/CN=images.eduardo.me"

# Ver certificado
openssl x509 -in /etc/ssl/certs/images.ssl.crt -text -noout
```

##### **Subir imágenes**

Este paso es fundamental para que la aplicación web pueda procesar las imágenes.

**1. Descomprimir las imágenes:**
```bash
unzip images.zip
```

**2. Verificar la estructura:**
```
img/
├── image01.jpg
├── image02.jpg
├── ...
└── image20.jpg
```

**3. Verificar acceso a las imágenes:**
```bash
curl -I http://localhost/img/image01.jpg
```

##### **Crear la aplicación web**

La aplicación contiene un formulario web con los siguientes campos:

- **Tamaño de la imagen** → En píxeles (cuadrada)
- **Ancho del borde** → En píxeles
- **Color del borde** → Formato hexadecimal
- **Enfoque** → Formato `<radius>x<sigma>`
- **Desenfoque** → Formato `<radius>x<sigma>`

Al pulsar el botón "Generar" se muestran las 20 imágenes con los parámetros configurados.

##### **Habilitar el Virtual Host y reiniciar Nginx**

```bash
# Crear enlace simbólico para habilitar el sitio
sudo ln -s /etc/nginx/sites-available/images.eduardo.me /etc/nginx/sites-enabled/

# Verificar configuración de Nginx
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx
```

##### **Parámetros de ngx_small_light**

| Parámetro | Descripción | Ejemplo |
|-----------|-------------|---------|
| `dw` | Ancho de destino | `300` |
| `dh` | Alto de destino | `300` |
| `bo` | Borde | `10px_solid_#FF6B6B` |
| `sharpen` | Enfoque | `0x0.5` |
| `blur` | Desenfoque | `10x2` |
| `c` | Calidad JPEG | `85` |
| `fmt` | Formato de salida | `jpeg`, `png`, `webp` |

##### **Problemas comunes y soluciones**

| Problema | Posible causa | Solución |
|----------|---------------|----------|
| Error 403 Forbidden | Permisos incorrectos | `chmod -R 644 img/` |
| Error 404 Not Found | Ruta incorrecta | Verificar `alias` o `root` en Nginx |
| Error 500 | Módulo no cargado | Verificar que ngx_small_light está cargado |
| Imágenes no se procesan | Parámetros incorrectos | Revisar sintaxis de parámetros GET |

#### ***Conclusiones***. <a name="id5"></a>

Esta práctica ha permitido adquirir conocimientos sobre:

1. **Configuración de módulos dinámicos en Nginx**: Se ha aprendido a compilar e instalar módulos adicionales para Nginx de forma dinámica.

2. **Virtual Hosting con SSL**: Se ha configurado un servidor virtual con certificados SSL, incluyendo la redirección de HTTP a HTTPS y de www a dominio base.

3. **Procesamiento de imágenes en tiempo real**: El módulo ngx_small_light permite generar miniaturas y aplicar efectos sin necesidad de software adicional, procesando las imágenes bajo demanda.

4. **Desarrollo de aplicaciones web con JavaScript**: Se ha creado una interfaz web dinámica que permite a los usuarios configurar parámetros de procesamiento de imágenes mediante peticiones GET al módulo ngx_small_light.

5. **Seguridad en servidores web**: La implementación de SSL/TLS y las redirecciones seguras garantizan una conexión cifrada.

En conclusión, esta práctica proporciona una base sólida para el despliegue de aplicaciones web con capacidades de procesamiento de imágenes, utilizando tecnologías modernas y seguras.

---

*Práctica desarrollada como parte del Ciclo Superior de Desarrollo de Aplicaciones Web*

