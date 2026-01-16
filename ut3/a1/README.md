<center>

# Administración de servidores web


</center>

***Nombre:*** Eduardo Rabadán Melián
***Curso:*** 2º de Ciclo Superior de Desarrollo de Aplicaciones Web.

### ÍNDICE

+ [Introducción](#id1)
+ [Objetivos](#id2)
+ [Material empleado](#id3)
+ [Desarrollo](#id4)
+ [Conclusiones](#id5)


#### ***Introducción***. <a name="id1"></a>

Se va a realizar una práctica relacionada con la administración de servidores web. El objetivo de esta tarea es desplegar una aplicación web escrita en HTML/Javascript que permita hacer uso del módulo de Nginx ngx_small_light. Este módulo sirve para generar "miniaturas" de imágenes on the fly además de otros posibles procesamientos a través de peticiones URL.

#### ***Objetivos***. <a name="id2"></a>

El objetivo de esta práctica es desplegar una aplicación web escrita en **HTML/Javascript** que permita hacer uso del módulo de Nginx **ngx_small_light**.

Los objetivos específicos son:
- Instalar el módulo ngx_small_light y cargarlo dinámicamente en Nginx
- Crear un virtual host específico que atienda peticiones en el dominio `images.nombrealumno.me`
- Habilitar el módulo ngx_small_light en el virtual host sólo para el location `/img`
- Subir las imágenes de images.zip a una carpeta `img` dentro de la carpeta de trabajo
- Crear una aplicación web que permita el tratamiento de dichas imágenes
- Incorporar certificado de seguridad (mostrar el certificado 🔒)
- Redirigir el subdominio www al dominio base (incluyendo ssl)

#### ***Material empleado***. <a name="id3"></a>

- **Visual Studio Code** - Editor de código
- **Nginx** - Servidor web
- **Docker** - Contenedor para el entorno de desarrollo
- **Git** - Control de versiones
- **OpenSSL** - Para la generación de certificados SSL
- **build-essential** - Herramientas de compilación
- **imagemagick** - Biblioteca de procesamiento de imágenes
- **libpcre3, libpcre3-dev** - Bibliotecas PCRE
- **libmagickwand-dev** - Biblioteca MagickWand
- Módulo **ngx_small_light** - Módulo de Nginx para procesamiento de imágenes

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
./configure --with-compat --add-dynamic-module=/home/daw2/Escritorio/dpl_eduardo/ut3/a1/ngx_small_light

# Compilar e instalar
make
sudo make install
```

##### **Crear el Virtual Host**

Crear un virtual host específico que atienda peticiones en el dominio `images.nombrealumno.me`.

**Nombre del archivo:** `images.nombrealumno.me` (en `/etc/nginx/sites-available/`)

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
    }
    
    # Servir aplicación web
    location / {
        root /home/daw2/Escritorio/dpl_eduardo/ut3/a1;
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

Este paso es fundamental para que la aplicación web pueda procesar las imágenes. A continuación se detalla el proceso completo:

**1. Obtener el archivo images.zip**

El archivo `images.zip` contiene las imágenes de prueba que utilizará la aplicación. Este archivo debe estar disponible en el entorno de trabajo (puede descargarse del aula virtual, repositorio de la asignatura, o ser proporcionado por el profesor).

**2. Descomprimir las imágenes**

```bash
# Verificar que el archivo existe
ls -lh images.zip

# Descomprimir el archivo (si está comprimido)
unzip images.zip

# Ver el contenido extraído
ls -la
```

**3. Estructura de carpetas esperada**

Tras la descompresión, debe aparecer una carpeta llamada `img` con las imágenes numeradas:

```
img/
├── image01.jpg
├── image02.jpg
├── image03.jpg
├── ...
├── image20.jpg
```

**4. Crear la carpeta de trabajo y copiar las imágenes**

Se recomienda trabajar en una carpeta dentro del directorio personal del usuario:

```bash
# Crear la carpeta de trabajo (si no existe)
mkdir -p ~/trabajo/img

# Copiar las imágenes a la carpeta de trabajo
cp -r img/* ~/trabajo/img/

# Verificar la copia
ls -la ~/trabajo/img/
```

**5. Configurar permisos de las imágenes**

Es importante que Nginx tenga permisos de lectura sobre las imágenes:

```bash
# Dar permisos de lectura a todos los usuarios
chmod -R 644 ~/trabajo/img/

# Verificar permisos
ls -l ~/trabajo/img/
```

**6. Verificar que Nginx puede acceder a las imágenes**

```bash
# Probar acceder a una imagen directamente
curl -I http://localhost/img/image01.jpg
```

Debería devolver una respuesta HTTP 200 si todo está configurado correctamente.

**7. Ubicación definitiva de las imágenes**

La estructura final debe ser:
```
~/trabajo/
├── img/
│   ├── image01.jpg
│   ├── image02.jpg
│   └── ...
│   └── image20.jpg
├── index.html
└── ...
```

**8. Configuración en Nginx**

Asegurarse de que el location `/img/` en el virtual host apunte a la carpeta correcta:

```nginx
location /img/ {
    small_light on;
    small_light_mode 'cover';
    small_light_preserve_ratio 'on';
    
    # Ruta a las imágenes
    alias /home/usuario/trabajo/img/;
    
    # Opcional: establecer permisos de caché
    expires 7d;
}
```

**9. Verificación final**

Reiniciar Nginx y verificar que las imágenes se sirven correctamente:

```bash
# Verificar configuración de Nginx
sudo nginx -t

# Reiniciar el servicio
sudo systemctl restart nginx

# Probar acceso a una imagen
curl "http://images.eduardo.me/img/image01.jpg?dw=300&dh=300"
```

**Posibles problemas y soluciones:**

| Problema | Posible causa | Solución |
|----------|---------------|----------|
| Error 403 Forbidden | Permisos incorrectos | `chmod -R 644 ~/trabajo/img/` |
| Error 404 Not Found | Ruta incorrecta | Verificar `alias` o `root` en Nginx |
| Error 500 | Módulo no cargado | Verificar que ngx_small_light está cargado |
| Imágenes no se procesan | Parámetros incorrectos | Revisar sintaxis de parámetros GET |

**Nota importante:** El nombre de las imágenes debe seguir el patrón `imageXX.jpg` donde XX es un número del 01 al 20. La aplicación web presupone esta nomenclatura para generar las URLs de procesamiento.

##### **Crear la aplicación web**

La aplicación debe contener un formulario web con los siguientes campos de texto:

- **Tamaño de la imagen** → En píxeles (corresponde al "lado": son imágenes cuadradas)
- **Ancho del borde** → En píxeles
- **Color del borde** → Formato hexadecimal
- **Enfoque** → Formato `<radius>x<sigma>`
- **Desenfoque** → Formato `<radius>x<sigma>`

Al pulsar el botón de "Generar" se tienen que mostrar todas las imágenes cambiando la URL del atributo src de cada imagen `<img>` para contemplar los parámetros establecidos en el formulario.

**Notas importantes:**
- Se puede presuponer que siempre van a haber 20 imágenes con los nombres image01.jpg, image02.jpg, ... y que las imágenes son cuadradas
- Usar peticiones GET del módulo ngx_small_light para el tratamiento de las imágenes, modificando el atributo src de cada `<img>`
- Trabajar en una carpeta dentro del `$HOME`

**Ejemplo de estructura HTML/JavaScript:**

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesador de Imágenes - ngx_small_light</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .main-content {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .form-panel {
            flex: 1;
            min-width: 350px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .preview-panel {
            flex: 2;
            min-width: 400px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .help-text {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }
        
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .image-item {
            background: #f5f5f5;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }
        
        .image-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            transition: transform 0.3s;
        }
        
        .image-item img:hover {
            transform: scale(1.05);
        }
        
        .image-item p {
            margin-top: 10px;
            font-weight: 600;
            color: #555;
        }
        
        .secure-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            margin-top: 10px;
        }
        
        .secure-badge::before {
            content: "🔒";
        }
        
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
            
            .form-panel, .preview-panel {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Procesador de Imágenes</h1>
            <p>Genera miniaturas y aplica efectos con ngx_small_light</p>
            <div class="secure-badge">Conexión Segura SSL</div>
        </header>
        
        <div class="main-content">
            <div class="form-panel">
                <h2>Configuración de Imagen</h2>
                <form id="imageForm">
                    <div class="form-group">
                        <label for="size">Tamaño de la imagen (px)</label>
                        <input type="number" id="size" name="size" value="300" min="50" max="1200">
                        <p class="help-text">Lado de la imagen cuadrada en píxeles</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="borderWidth">Ancho del borde (px)</label>
                        <input type="number" id="borderWidth" name="borderWidth" value="10" min="0" max="50">
                        <p class="help-text">Ancho del borde en píxeles</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="borderColor">Color del borde</label>
                        <input type="text" id="borderColor" name="borderColor" value="#FF6B6B" placeholder="#RRGGBB">
                        <p class="help-text">Color en formato hexadecimal (ej: #FF6B6B)</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="focus">Enfoque</label>
                        <input type="text" id="focus" name="focus" value="0x0.5" placeholder="radius x sigma">
                        <p class="help-text">Formato: <radius>x<sigma> (ej: 0x0.5)</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="blur">Desenfoque</label>
                        <input type="text" id="blur" name="blur" value="10x2" placeholder="radius x sigma">
                        <p class="help-text">Formato: <radius>x<sigma> (ej: 10x2)</p>
                    </div>
                    
                    <button type="submit">Generar Imágenes</button>
                </form>
            </div>
            
            <div class="preview-panel">
                <h2>Galería de Imágenes</h2>
                <div class="image-gallery" id="imageGallery">
                    <!-- Las imágenes se generarán aquí -->
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Generar URLs para las imágenes procesadas con ngx_small_light
        function generateImageUrl(filename, params) {
            const baseUrl = `https://images.nombrealumno.me/img/${filename}`;
            const queryParams = new URLSearchParams();
            
            // Tamaño de la imagen (cuadrada)
            queryParams.set('dw', params.size);
            queryParams.set('dh', params.size);
            
            // Borde
            if (params.borderWidth > 0) {
                queryParams.set('bo', `${params.borderWidth}px_solid_${params.borderColor}`);
            }
            
            // Enfoque (sharpen)
            if (params.focus) {
                const [radius, sigma] = params.focus.split('x');
                queryParams.set('sharpen', `${radius}x${sigma}`);
            }
            
            // Desenfoque (blur)
            if (params.blur) {
                const [radius, sigma] = params.blur.split('x');
                queryParams.set('blur', `${radius}x${sigma}`);
            }
            
            return `${baseUrl}?${queryParams.toString()}`;
        }
        
        // Renderizar galería de imágenes
        function renderGallery(params) {
            const gallery = document.getElementById('imageGallery');
            gallery.innerHTML = '';
            
            // Generar las 20 imágenes (image01.jpg a image20.jpg)
            for (let i = 1; i <= 20; i++) {
                const imageName = `image${i.toString().padStart(2, '0')}.jpg`;
                const imageUrl = generateImageUrl(imageName, params);
                
                const imageItem = document.createElement('div');
                imageItem.className = 'image-item';
                imageItem.innerHTML = `
                    <img src="${imageUrl}" alt="${imageName}" loading="lazy">
                    <p>${imageName}</p>
                `;
                
                gallery.appendChild(imageItem);
            }
        }
        
        // Manejar envío del formulario
        document.getElementById('imageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const params = {
                size: document.getElementById('size').value,
                borderWidth: document.getElementById('borderWidth').value,
                borderColor: document.getElementById('borderColor').value,
                focus: document.getElementById('focus').value,
                blur: document.getElementById('blur').value
            };
            
            renderGallery(params);
        });
        
        // Generar imágenes iniciales al cargar la página
        window.addEventListener('DOMContentLoaded', function() {
            renderGallery({
                size: 300,
                borderWidth: 10,
                borderColor: '#FF6B6B',
                focus: '0x0.5',
                blur: '10x2'
            });
        });
    </script>
</body>
</html>
```

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



Si encontramos dificultades a la hora de realizar algún paso debemos explicar esas dificultades, que pasos hemos seguido para resolverla y los resultados obtenidos.

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

