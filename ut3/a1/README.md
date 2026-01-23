<center>

# UT3-TE1: Administración de servidores web

</center>

**Nombre:** Eduardo  
**Curso:** 2º de Ciclo Superior de Desarrollo de Aplicaciones Web.

### ÍNDICE

+ [Introducción](#id1)
+ [Objetivos](#id2)
+ [Material empleado](#id3)
+ [Desarrollo](#id4)
+ [Conclusiones](#id5)

---

#### ***Introducción***. <a name="id1"></a>

Esta práctica consiste en el despliegue de una aplicación web que permite procesar imágenes en tiempo real utilizando el módulo **ngx_small_light** de Nginx. Este módulo es un procesador de imágenes dinámico que permite generar miniaturas y aplicar diversos efectos a través de peticiones URL, sin necesidad de preprocesar las imágenes almacenadas en el servidor.

El módulo ngx_small_light actúa como un proxy de imágenes, interceptando las peticiones a recursos estáticos y aplicando transformaciones como redimensionado, añade efectos como bordes, desenfoque y nitidez. Todo este procesamiento se realiza de forma dinámica bajo demanda, lo que mejora la flexibilidad del sistema y elimina la necesidad de preprocesar y almacenar numerosas variantes de imágenes.

La aplicación web desarrollada permite a los usuarios configurar diferentes parámetros de procesamiento de imágenes a través de un formulario intuitivo, visualizando inmediatamente los resultados sobre un conjunto de 20 imágenes de muestra.

---

#### ***Objetivos***. <a name="id2"></a>

Los objetivos principales de esta práctica evaluable son:

1. **Instalación del módulo ngx_small_light**: Compilar e integrar el módulo como módulo dinámico en Nginx, incluyendo todas las dependencias necesarias (build-essential, imagemagick, libpcre3, libpcre3-dev, libmagickwand-dev).

2. **Configuración de virtual host**: Crear un servidor virtual específico que atienda peticiones en el dominio `images.nombrealumno.me`, configurando correctamente el server_name.

3. **Habilitación del módulo por location**: Activar ngx_small_light exclusivamente para el location `/img`, permitiendo el procesamiento de imágenes en esa ruta específica.

4. **Gestión de contenido estático**: Subir y organizar 20 imágenes de prueba (image01.jpg a image20.jpg) en la carpeta img/ del proyecto.

5. **Desarrollo de aplicación web**: Crear una interfaz web completa con formulario para especificar parámetros de procesamiento de imágenes.

6. **Implementación de seguridad SSL**: Configurar certificado de seguridad para el dominio y mostrar indicador de conexión segura.

7. **Redirección HTTPS**: Configurar redirección desde el subdominio www al dominio base incluyendo SSL.

---

#### ***Material empleado***. <a name="id3"></a>

**Hardware:**
- Ordenador con arquitectura compatible para compilación de software
- Conexión a internet para descarga de dependencias y módulos

**Software y herramientas:**
- **Sistema operativo**: Linux (distribución basada en Debian/Ubuntu)
- **Nginx**: Servidor web con soporte para módulos dinámicos
- **ngx_small_light**: Módulo de Nginx para procesamiento de imágenes
- **ImageMagick**: Biblioteca y herramientas de línea de comandos para procesamiento de imágenes
- **libmagickwand-dev**: Desarrollo de ImageMagick para integración con módulos Nginx
- **build-essential**: Paquete de herramientas de compilación (gcc, make, etc.)
- **Git**: Control de versiones para descarga del código fuente del módulo
- **Certificado SSL**: Certificado para HTTPS (Let's Encrypt o similar)

**Estructura de archivos y rutas:**

| Archivo/Carpeta | Ruta |
|-----------------|------|
| Carpeta del proyecto | `/home/dpl_eduardo/dpl_eduardo/ut3/a1` |
| Aplicación web (index.html) | `/home/dpl_eduardo/dpl_eduardo/ut3/a1/index.html` |
| Documentación | `/home/dpl_eduardo/dpl_eduardo/ut3/a1/README.md` |
| Carpeta de imágenes | `/home/dpl_eduardo/dpl_eduardo/ut3/a1/img/` |
| Imágenes (01-20) | `/home/dpl_eduardo/dpl_eduardo/ut3/a1/img/image01.jpg` a `/home/dpl_eduardo/dpl_eduardo/ut3/a1/img/image20.jpg` |
| Código fuente ngx_small_light | `/home/dpl_eduardo/ngx_small_light/` |
| Código fuente Nginx | `/home/dpl_eduardo/nginx-1.28.0` |
| Módulo dinámico compilado | `/usr/lib/nginx/modules/ngx_http_small_light_module.so` |
| Configuración módulos Nginx | `/etc/nginx/modules-enabled/60-small-light.conf` |
| Configuración virtual host | `/etc/nginx/sites-available/images.eduardo.me` |
| Enlace simbólico sitio habilitado | `/etc/nginx/sites-enabled/images.eduardo.me` |
| Configuración principal Nginx | `/etc/nginx/nginx.conf` |
| Certificados SSL | `/etc/letsencrypt/live/images.eduardo.me/` |
| Archivo clave SSL | `/etc/letsencrypt/live/images.eduardo.me/privkey.pem` |
| Certificado completo SSL | `/etc/letsencrypt/live/images.eduardo.me/fullchain.pem` |
| Configuración SSL Nginx | `/etc/letsencrypt/options-ssl-nginx.conf` |
| Params DH SSL | `/etc/letsencrypt/ssl-dhparams.pem` |

**Configuraciones de red:**
- Dominio: images.eduardo.me (apuntando a la IP del servidor)
- Puerto 80: HTTP (redirección a HTTPS)
- Puerto 443: HTTPS con certificado SSL válido

---

#### ***Desarrollo***. <a name="id4"></a>

A continuación se detallan los pasos seguidos para completar la práctica:

---

**PASO 1: Instalación de dependencias**

Se instalaron las dependencias necesarias para compilar el módulo ngx_small_light:

```bash
sudo apt update
sudo apt install -y build-essential imagemagick libpcre3 libpcre3-dev libmagickwand-dev pkg-config git wget
```

- **build-essential**: Proporciona el compilador GCC y herramientas make necesarias para compilar desde código fuente.
- **imagemagick**: Biblioteca de procesamiento de imágenes que ngx_small_light utiliza internamente.
- **libpcre3-dev**: Biblioteca de expresiones regulares (PCRE) requerida por Nginx.
- **libmagickwand-dev**: Interfaz de desarrollo para ImageMagick, necesaria para la integración del módulo.
- **pkg-config**: Herramienta para facilitar la compilación de aplicaciones que usan librerías.
- **git**: Para clonar el repositorio del módulo.
- **wget**: Para descargar archivos.

> **⚠️ POSIBLE ERROR**: Si al ejecutar `./configure` aparece el error "checking for ngx_small_light dependencies ... not found" junto con un error de compilación de `MagickWand.h`, significa que el sistema no encuentra las librerías de desarrollo de ImageMagick.

> **SOLUCIÓN**: Verificar que las cabeceras están disponibles:
> ```bash
> dpkg -L libmagickwand-dev | grep -i "wand\.h"
> ```
> La salida debe mostrar: `/usr/include/ImageMagick-7/wand/MagickWand.h`

> Si el problema persiste, instalar ImageMagick desde código fuente:
> ```bash
> cd /tmp
> wget https://imagemagick.org/archive/releases/ImageMagick.tar.gz
> tar -xzf ImageMagick.tar.gz
> cd ImageMagick-*
> ./configure --prefix=/usr/local
> make -j$(nproc)
> sudo make install
> sudo ldconfig
> /usr/local/bin/convert --version
> ```

---

**PASO 2: Descarga del código fuente del módulo**

Se clonó el repositorio oficial del módulo ngx_small_light:

```bash
cd /home/dpl_eduardo
git clone https://github.com/cubicdaiya/ngx_small_light.git
```

- **Ruta del módulo**: `/home/dpl_eduardo/ngx_small_light/`

---

**PASO 3: Configuración previa del módulo**

Antes de configurar Nginx, se ejecutó el script de configuración del módulo:

```bash
cd /home/dpl_eduardo/ngx_small_light
./setup
```

Este script prepara el módulo para su integración con Nginx.

---

**PASO 4: Descarga y compilación de Nginx con módulo dinámico**

Se descargó el código fuente de Nginx con la misma versión instalada en el sistema:

```bash
cd /tmp
wget http://nginx.org/download/nginx-1.28.0.tar.gz
tar -xzf nginx-1.28.0.tar.gz
cd nginx-1.28.0

# Verificar versión de Nginx instalada
nginx -v
```

Se compiló Nginx con el módulo ngx_small_light como módulo dinámico:

```bash
# Rutas de configuración:
# - Código fuente Nginx: /tmp/nginx-1.28.0/
# - Módulo ngx_small_light: /home/dpl_eduardo/ngx_small_light/
# - Módulo compilado: /usr/lib/nginx/modules/ngx_http_small_light_module.so

./configure --prefix=/etc/nginx --sbin-path=/usr/sbin/nginx --modules-path=/usr/lib/nginx/modules \
    --with-debug --add-dynamic-module=/home/dpl_eduardo/ngx_small_light \
    --conf-path=/etc/nginx/nginx.conf --error-log-path=/var/log/nginx/error.log \
    --http-log-path=/var/log/nginx/access.log --pid-path=/var/run/nginx.pid \
    --lock-path=/var/run/nginx.lock --user=nginx --group=nginx

make -j$(nproc)
sudo make install
```

---

**PASO 5: Carga del módulo dinámico**

Se creó el archivo de carga del módulo en la carpeta de módulos habilitados de Nginx:

```bash
echo 'load_module /usr/lib/nginx/modules/ngx_http_small_light_module.so;' | sudo tee /etc/nginx/modules-enabled/60-small-light.conf
```

- **Archivo de configuración**: `/etc/nginx/modules-enabled/60-small-light.conf`
- **Módulo cargado**: `/usr/lib/nginx/modules/ngx_http_small_light_module.so`

---

**PASO 6: Configuración del virtual host**

Se creó el archivo de configuración del virtual host:

```bash
sudo nano /etc/nginx/sites-available/images.eduardo.me
```

**Contenido del archivo** `/etc/nginx/sites-available/images.eduardo.me`:

```nginx
# Virtual host para images.eduardo.me

# Servidor HTTP (redirección a HTTPS)
server {
    listen 80;
    server_name images.eduardo.me www.images.eduardo.me;
    
    # Redirigir HTTP a HTTPS
    return 301 https://$host$request_uri;
}

# Servidor HTTPS
server {
    listen 443 ssl http2;
    server_name images.eduardo.me;
    
    # Configuración SSL - Rutas de certificados
    ssl_certificate /etc/letsencrypt/live/images.eduardo.me/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/images.eduardo.me/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
    
    # Redirigir www al dominio base
    if ($host = www.images.eduardo.me) {
        return 301 https://images.eduardo.me$request_uri;
    }
    
    # Ruta del proyecto (aplicación web)
    root /home/daw/Escritorio/dpl_eduardo/ut3/a1;
    index index.html;
    
    location / {
        try_files $uri $uri/ =404;
    }
    
    location /img/ {
        # Habilitar módulo ngx_small_light
        small_light on;
        
        # Configuración de procesamiento de imágenes
        small_light_set $dw 300;      # Ancho por defecto (px)
        small_light_set $dh 300;      # Alto por defecto (px)
        small_light_imagick on;
        
        # Ruta de las imágenes originales
        alias /home/daw/Escritorio/dpl_eduardo/ut3/a1/img/;
    }
}
```

- **Archivo de configuración**: `/etc/nginx/sites-available/images.eduardo.me`
- **Raíz del proyecto**: `/home/daw/Escritorio/dpl_eduardo/ut3/a1`
- **Carpeta de imágenes**: `/home/daw/Escritorio/dpl_eduardo/ut3/a1/img/`

---

**PASO 7: Habilitación del sitio y reinicio de Nginx**

Se creó el enlace simbólico para habilitar el sitio:

```bash
sudo ln -s /etc/nginx/sites-available/images.eduardo.me /etc/nginx/sites-enabled/images.eduardo.me
```

- **Enlace simbólico**: `/etc/nginx/sites-enabled/images.eduardo.me`

Se verificó la configuración y se reinició Nginx:

```bash
# Verificar configuración
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx

# Ver estado del servicio
sudo systemctl status nginx
```

---

**PASO 8: Obtención del certificado SSL**

Se utilizó Let's Encrypt con Certbot para obtener el certificado SSL gratuito:

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obtener y configurar certificado
sudo certbot --nginx -d images.eduardo.me -d www.images.eduardo.me
```

Certbot guarda los certificados en:
- **Certificado completo**: `/etc/letsencrypt/live/images.eduardo.me/fullchain.pem`
- **Clave privada**: `/etc/letsencrypt/live/images.eduardo.me/privkey.pem`
- **Configuración SSL**: `/etc/letsencrypt/options-ssl-nginx.conf`
- **Parámetros DH**: `/etc/letsencrypt/ssl-dhparams.pem`

---

**PASO 9: Desarrollo de la aplicación web**

Se creó la aplicación web con el archivo principal:

```bash
nano /home/daw/Escritorio/dpl_eduardo/ut3/a1/index.html
```

- **Archivo**: `/home/daw/Escritorio/dpl_eduardo/ut3/a1/index.html`

**Características de la aplicación:**
- **Formulario de configuración** con campos para:
  - Tamaño de imagen (en píxeles)
  - Ancho del borde (en píxeles)
  - Color del borde (formato hexadecimal)
  - Enfoque (formato radius x sigma)
  - Desenfoque (formato radius x sigma)

- **Galería de imágenes** que muestra las 20 imágenes procesadas dinámicamente

- **Generación de URLs** con parámetros ngx_small_light:
  - `dw` y `dh`: Dimensiones de la imagen
  - `bo`: Borde con formato `widthpx_solid_color`
  - `sharpen`: Enfoque con formato `radius x sigma`
  - `blur`: Desenfoque con formato `radius x sigma`

- **Indicador de conexión segura** (🔒) que muestra el certificado SSL válido

---

**PASO 10: Subida de imágenes**

Las 20 imágenes de prueba se encuentran en:

```bash
# Carpeta de imágenes
/home/daw/Escritorio/dpl_eduardo/ut3/a1/img/

# Verificar imágenes
ls -la /home/daw/Escritorio/dpl_eduardo/ut3/a1/img/
```

Lista de imágenes:
- `/home/daw/Escritorio/dpl_eduardo/ut3/a1/img/image01.jpg`
- `/home/daw/Escritorio/dpl_eduardo/ut3/a1/img/image02.jpg`
- `/home/daw/Escritorio/dpl_eduardo/ut3/a1/img/image03.jpg`
- ...
- `/home/daw/Escritorio/dpl_eduardo/ut3/a1/img/image20.jpg`

---

**Verificación del funcionamiento**

1. Acceder a `https://images.eduardo.me` en un navegador web
2. Observar el indicador de conexión segura (🔒) y el certificado SSL
3. Verificar que se muestran las 20 imágenes con los parámetros por defecto
4. Modificar los parámetros del formulario y pulsar "Generar"
5. Confirmar que las imágenes se actualizan con los nuevos efectos aplicados
6. Verificar redirección desde `www.images.eduardo.me` a `images.eduardo.me`

---

**Resumen de rutas de archivos importantes:**

```
/home/daw/Escritorio/dpl_eduardo/ut3/a1/
├── index.html          (Aplicación web)
├── README.md           (Documentación)
└── img/                (Imágenes)
    ├── image01.jpg
    ├── image02.jpg
    ├── ...
    └── image20.jpg

/etc/nginx/
├── nginx.conf          (Configuración principal)
├── modules-enabled/
│   └── 60-small-light.conf  (Carga del módulo)
└── sites-available/
    └── images.eduardo.me    (Virtual host)
    
/etc/nginx/sites-enabled/
    └── images.eduardo.me    (Enlace simbólico)

/etc/letsencrypt/live/images.eduardo.me/
├── privkey.pem         (Clave privada SSL)
├── fullchain.pem       (Certificado completo)
├── cert.pem            (Certificado)
└── chain.pem           (Cadena de certificados)

/home/dpl_eduardo/
└── ngx_small_light/    (Código fuente del módulo)

/tmp/
└── nginx-1.28.0/       (Código fuente de Nginx)
```

---

#### ***Conclusiones***. <a name="id5"></a>

Esta práctica ha permitido profundizar en varios aspectos fundamentales de la administración de servidores web:

1. **Compilación de módulos Nginx**: Se ha aprendido el proceso completo de compilación de módulos dinámicos para Nginx, incluyendo la gestión de dependencias y la configuración con el código fuente de Nginx.

2. **Procesamiento de imágenes en tiempo real**: El módulo ngx_small_light demuestra cómo delegar el procesamiento de imágenes al servidor web, evitando la necesidad de preprocesar imágenes en diferentes tamaños y efectos. Esto reduce el almacenamiento necesario y permite mayor flexibilidad.

3. **Configuración de virtual hosts**: Se ha consolidado el conocimiento sobre la creación de servidores virtuales específicos con dominios personalizados y sus configuraciones de redirection.

4. **Seguridad web con SSL/TLS**: La implementación de HTTPS con Let's Encrypt ha demostrado lo accesible que puede ser securing un sitio web moderno, así como la importancia de las redirecciones para garantizar que todos los accesos utilicen la conexión segura.

5. **Desarrollo web con APIs de procesamiento**: La creación de la aplicación web ha mostrado cómo integrar las capacidades de procesamiento del servidor con una interfaz de usuario intuitiva, utilizando parámetros URL para comunicar las transformaciones deseadas.

6. **Gestión de proyectos**: El trabajo con estructuras de carpetas organizadas y la documentación apropiada son aspectos clave para mantener la mantenibilidad de los proyectos a largo plazo.

El resultado final es un sistema completo que permite procesar imágenes dinámicamente con una interfaz web moderna y segura, demostrando la integración efectiva de múltiples tecnologías y conceptos aprendidos durante el ciclo formativo.

