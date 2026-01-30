<center>

# UT4-A3 Administración de servidores de aplicaciones: Laravel


</center>

***Nombre:*** Eduardo Rabadán Melián
***Curso:*** 2º de Ciclo Superior de Desarrollo de Aplicaciones Web.

#### ***Desarrollo***.

## Laravel (PHP)


[Laravel](https://laravel.com/) es un **framework de código abierto** para desarrollar aplicaciones y servicios web con **PHP**.

### Instalación

#### Composer

Lo primero que necesitamos es un **gestor de dependencias para PHP**. Vamos a instalar [Composer](https://getcomposer.org/):

```console
curl -fsSL https://raw.githubusercontent.com/composer/getcomposer.org/main/web/installer \
| php -- --quiet | sudo mv composer.phar /usr/local/bin/composer
```

Comprobamos la versión instalada:

```console
composer --version
Composer version 2.4.4 2022-10-27 14:39:29
```

#### Paquetes de soporte

Necesitamos **ciertos módulos PHP** habilitados en el sistema. Para ello instalamos los siguientes paquetes soporte:

```console
sudo apt install -y php8.2-mbstring php8.2-xml \
php8.2-bcmath php8.2-curl php8.2-pgsql
```

| Paquete                                                     | Descripción                                     |
| ----------------------------------------------------------- | ----------------------------------------------- |
| [mbstring](https://www.php.net/manual/es/book.mbstring.php) | Gestión de cadenas de caracteres multibyte      |
| [xml](https://www.php.net/manual/es/book.xml.php)           | Análisis XML                                    |
| [bcmath](https://www.php.net/manual/en/book.bc.php)         | Operaciones matemáticas de precisión arbitraria |
| [curl](https://www.php.net/manual/es/book.curl.php)         | Cliente de cURL                                 |
| [pgsql](https://www.php.net/manual/es/book.pgsql.php)       | Herramientas para PostgreSQL                    |

#### Aplicación

Ahora ya podemos **crear la estructura** de nuestra aplicación Laravel. Para ello utilizamos `composer` indicando el paquete [laravel/laravel](https://packagist.org/packages/laravel/laravel) junto al nombre de la aplicación:

```console
composer create-project laravel/laravel travelroad
```

Vemos que se ha creado una carpeta `travelroad` con el andamio (_scaffolding_) para empezar a trabajar:

```console
total 364
-rw-r--r-- 1 dplprod_alumno dplprod_alumno   3911 Jan 13 16:43 README.md
drwxr-xr-x 5 dplprod_alumno dplprod_alumno   4096 Jan 13 16:43 app
-rwxr-xr-x 1 dplprod_alumno dplprod_alumno    425 Jan 13 16:43 artisan
drwxr-xr-x 3 dplprod_alumno dplprod_alumno   4096 Jan 13 16:43 bootstrap
-rw-r--r-- 1 dplprod_alumno dplprod_alumno   2836 Jan 13 16:43 composer.json
-rw-r--r-- 1 dplprod_alumno dplprod_alumno 308385 Jan 13 16:43 composer.lock
drwxr-xr-x 2 dplprod_alumno dplprod_alumno   4096 Jan 13 16:43 config
drwxr-xr-x 5 dplprod_alumno dplprod_alumno   4096 Jan 13 16:43 database
-rw-r--r-- 1 dplprod_alumno dplprod_alumno    414 Jan 13 16:43 package.json
-rw-r--r-- 1 dplprod_alumno dplprod_alumno   1284 Jan 13 16:43 phpunit.xml
drwxr-xr-x 2 dplprod_alumno dplprod_alumno   4096 Jan 13 16:43 public
drwxr-xr-x 5 dplprod_alumno dplprod_alumno   4096 Jan 13 16:43 resources
drwxr-xr-x 2 dplprod_alumno dplprod_alumno   4096 Jan 13 16:43 routes
drwxr-xr-x 5 dplprod_alumno dplprod_alumno   4096 Jan 13 16:43 storage
drwxr-xr-x 4 dplprod_alumno dplprod_alumno   4096 Jan 13 16:43 tests
-rw-r--r-- 1 dplprod_alumno dplprod_alumno    436 Jan 13 16:43 vite.config.js
```

Entramos en la carpeta de trabajo y probamos que se ha instalado correctamente [artisan](https://laravel.com/docs/9.x/artisan), **la interfaz en línea de comandos para Laravel**:

```console
./artisan --version
Laravel Framework 9.38.0
```

Por defecto se ha creado un **fichero de configuración** `.env` durante el andamiaje. Abrimos este fichero y **modificamos ciertos valores** para especificar credenciales de acceso:

```console
vi .env
```

```ini
...
APP_NAME=TravelRoad
APP_ENV=development
...
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=travelroad
DB_USERNAME=travelroad_user
DB_PASSWORD=dpl0000
...
```

### Configuración Nginx

Lo primero será fijar los **permisos adecuados a los ficheros del proyecto** para que los servicios Nginx+PHP-FPM puedan trabajar sin errores de acceso.

Existen un par de carpetas en las que se puede almacenar información. Ajustamos los permisos:

```console
sudo chgrp -R nginx storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

La **configuración del _virtual host_ Nginx** para nuestra aplicación Laravel la vamos a hacer en un fichero específico:

```console
sudo vi /etc/nginx/conf.d/travelroad.conf
```

> Contenido:

```nginx
server {
    server_name travelroad;
    root /home/sdelquin/travelroad/public; # MIRAR LA RUTA DEL ORDENA DE CLASE

    index index.html index.htm index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

> 💡 Recordar añadir `travelroad` al fichero `/etc/hosts` en caso de estar trabajando en local.

**Comprobamos la sintaxis** del fichero y, si todo ha ido bien, **recargamos la configuración** Nginx:

```console
sudo nginx -t
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful

sudo systemctl reload nginx
```

Si ahora abrimos el navegador en http://travelroad veremos una página de inicio (_launching_) con información general sobre el framework:

```console
firefox http://travelroad
```

![Laravel Init](./images/laravel-init.png)

### Lógica de negocio

Nos queda modificar el comportamiento de la aplicación para cargar los datos y mostrarlos en una plantilla.

Lo primero es **cambiar el código de la ruta**:

```console
vi routes/web.php
```

> Contenido:

```php
<?php

// https://laravel.com/api/6.x/Illuminate/Support/Facades/DB.html
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
  $wished = DB::select('select * from places where visited = false');
  $visited = DB::select('select * from places where visited = true');

  return view('travelroad', ['wished' => $wished, 'visited' => $visited]);
});
```

Lo segundo es **escribir la plantilla** que renderiza los datos. **Renderizar una plantilla** significa sustituir las variables por sus valores y así obtener un HTML final. Utilizaremos [Blade](https://laravel.com/docs/9.x/blade) como **motor de plantillas** incluido en Laravel.

```console
vi resources/views/travelroad.blade.php
```

> Contenido:

```html
<html>
  <head>
    <title>Travel List</title>
  </head>

  <body>
    <h1>My Travel Bucket List</h1>
    <h2>Places I'd Like to Visit</h2>
    <ul>
      @foreach ($wished as $place)
      <li>{{ $place->name }}</li>
      @endforeach
    </ul>

    <h2>Places I've Already Been To</h2>
    <ul>
      @foreach ($visited as $place)
      <li>{{ $place->name }}</li>
      @endforeach
    </ul>
  </body>
</html>
```

Ya podemos abrir el navegador en http://travelroad y comprobar que todo está funcionando correctamente:

```console
firefox http://travelroad
```

![Laravel Works](./images/laravel-works.png)

### Producción

Hay que tener en cuenta un detalle. La carpeta `vendor` está fuera de control de versiones por una entrada que se crea automáticamente un el fichero `.gitignore` del "scaffolding" que realiza Laravel:

```console
grep vendor .gitignore
/vendor
```

Esta carpeta contiene todas las dependencias del proyecto. Por lo tanto, **cuando hagamos el despliegue en producción**, debemos ejecutar el siguiente comando para crear esta carpeta e instalar todas las dependencias necesarias:

```console
composer install
Installing dependencies from lock file (including require-dev)
Verifying lock file contents can be installed on current platform.
Nothing to install, update or remove
Generating optimized autoload files
> Illuminate\Foundation\ComposerScripts::postAutoloadDump
> @php artisan package:discover --ansi

   INFO  Discovering packages.

  laravel/sail .............................................................................. DONE
  laravel/sanctum ........................................................................... DONE
  laravel/tinker ............................................................................ DONE
  nesbot/carbon ............................................................................. DONE
  nunomaduro/collision ...................................................................... DONE
  nunomaduro/termwind ....................................................................... DONE
  spatie/laravel-ignition ................................................................... DONE

81 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
```

### Script de despliegue

Veamos un ejemplo de **script de despliegue** para esta aplicación:

```console
vi deploy.sh
```

> Contenido:

```bash
#!/bin/bash

ssh arkania "
  cd $(dirname $0)
  git pull
  composer install
"

# HACER EL DEPLOY.SH EN CLASE
```

Damos permisos de ejecución:

```console
chmod +x deploy.sh
```

> 💡 `deploy.sh` es un fichero que se incluye en el control de versiones.
