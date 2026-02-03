<center>

# UT4-A3 Administración de servidores de aplicaciones: Django


</center>

***Nombre:*** Eduardo Rabadán Melián
***Curso:*** 2º de Ciclo Superior de Desarrollo de Aplicaciones Web.

#### ***Desarrollo***.

Para **instalar Django** (y sus dependencias) basta con utilizar la herramienta de gestión de paquetes en Python denominada [pip](https://pip.pypa.io/en/stable/) (_Package Installer for Python_):

```console
pip install django
Collecting django
  Downloading Django-4.1.3-py3-none-any.whl (8.1 MB)
     ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ 8.1/8.1 MB 5.1 MB/s eta 0:00:00
Collecting asgiref<4,>=3.5.2
  Downloading asgiref-3.5.2-py3-none-any.whl (22 kB)
Collecting sqlparse>=0.2.2
  Downloading sqlparse-0.4.3-py3-none-any.whl (42 kB)
     ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ 42.8/42.8 kB 0:00:00
Installing collected packages: sqlparse, asgiref, django
Successfully installed asgiref-3.5.2 django-4.1.3 sqlparse-0.4.3
```

Podemos **comprobar la versión instalada de Django** con el siguiente comando:

```console
python -m django --version
4.1.3
```

### Creación del proyecto

Django proporciona la herramienta [django-admin](https://docs.djangoproject.com/en/4.1/ref/django-admin/) para crear la estructura base del proyecto:

```console
django-admin startproject main .
```

Comprobamos el contenido de la carpeta de trabajo:

```console
tree
.
├── manage.py
└── main
    ├── asgi.py
    ├── __init__.py
    ├── settings.py
    ├── urls.py
    └── wsgi.py

1 directory, 6 files
```

> 💡 A diferencia de Ruby on Rails, Django sigue un patrón "minimalista" donde el andamiaje es modesto y con muy pocos ficheros iniciales.

Podemos lanzar el **servidor de desarrollo** con la herramienta [manage.py](https://docs.djangoproject.com/en/4.1/ref/django-admin/) que ya viene incluida en el andamiaje del proyecto:

```console
./manage.py runserver
Watching for file changes with StatReloader
Performing system checks...

System check identified no issues (0 silenced).

You have 18 unapplied migration(s). Your project may not work properly until you apply the migrations for app(s): admin, auth, contenttypes, sessions.
Run 'python manage.py migrate' to apply them.
November 15, 2022 - 10:11:09
Django version 4.1.3, using settings 'main.settings'
Starting development server at http://127.0.0.1:8000/
Quit the server with CONTROL-C.
```

Ahora si accedemos a http://localhost:8000 tendremos **la pantalla de bienvenida** de un proyecto base Django:


### Código de aplicación

Ahora ya estamos en disposición de empezar a montar las distintas partes de nuestra aplicación web. Django sigue el patrón **MTV (Model-Template-View)** que es análogo al modelo MVC. Funciona de la siguiente manera:



> Fuente: https://espifreelancer.com/mtv-django.html

Un proyecto **Django está formado por "aplicaciones"**. Lo primero será crear nuestra primera aplicación:

```console
./manage.py startapp places
```

A través de este comando se ha creado una carpeta para alojar la aplicación `places` con el siguiente contenido:

```console
ls -l
total 152
-rw-rw-r-- 1 dplprod_alumno dplprod_alumno 131072 Dec 12 17:08 db.sqlite3
-rwxr-xr-x 1 dplprod_alumno dplprod_alumno    298 Jan 30 16:45 deploy.sh
drwxr-xr-x 3 dplprod_alumno dplprod_alumno   4096 Jan 30 16:16 main
-rwxr-xr-x 1 dplprod_alumno dplprod_alumno    660 Dec 12 17:08 manage.py
drwxr-xr-x 5 dplprod_alumno dplprod_alumno   4096 Jan 30 16:01 places
-rw-r--r-- 1 dplprod_alumno dplprod_alumno     34 Dec 12 18:12 requirements.txt
-rwxr-xr-x 1 dplprod_alumno dplprod_alumno    224 Jan 13 18:08 run.sh
```

Hemos de **activar esta aplicación** para que Django sea consciente de que existe. Para ello añadimos esta línea en el fichero `main/settings.py`:

```console
vi main/settings.py
```

> Contenido:

```python
...
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    # Nueva línea ↓
    'places.apps.PlacesConfig',
]
...
```

#### Acceso a la base de datos

Antes de nada debemos instalar un paquete de soporte denominado [psycopg](https://www.psycopg.org/) que viene a ser un driver para **conectar Python con bases de datos PostgreSQL**:

```console
pip install psycopg2
Collecting psycopg2
  Downloading psycopg2-2.9.5.tar.gz (384 kB)
     ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ 384.3/384.3 kB 0:00:00
  Preparing metadata (setup.py) ... done
  Building wheels for collected packages: psycopg2
  Building wheel for psycopg2 (setup.py) ... done
  Created wheel for psycopg2: filename=psycopg2-2.9.5-cp311-cp311-linux_aarch64.whl size=490810 sha256=fb4ced65205db48f43764e3023f3dfc013a00c9e4b33f7d94db4042bbe7b4be1
  Stored in directory: /tmp/pip-ephem-wheel-cache-srnl4xia/wheels/f9/08/b1/dddce0df8eee727ef4a56fb0da4f0230de9e127e5f234881d4
Successfully built psycopg2
Installing collected packages: psycopg2
Successfully installed psycopg2-2.9.5
```

Hay que establecer las **credenciales de acceso a la base de datos**:

```console
vi main/settings.py
```

Dejar la sección `DATABASES` tal que así:

```python
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql',
        'NAME': 'travelroad',
        'USER': 'travelroad_user',
        'PASSWORD': 'dpl0000',
        'HOST': 'localhost',
        'PORT': 5432,
    }
}
```

Django proporciona el subcomando [check](https://docs.djangoproject.com/en/4.1/ref/django-admin/#check) dentro de `manage.py` para comprobar que todo esté correcto:

```console
./manage.py check
System check identified no issues (0 silenced).
```

#### Modelos

En Django existe un [ORM](https://en.wikipedia.org/wiki/Object%E2%80%93relational_mapping) que permite **mapear clases escritas en Python con entidades relacionales de la base de datos** (PostgreSQL en este caso).

Vamos a escribir nuestro modelo de lugares:

```console
vi places/models.py
```

> Contenido:

```python
from django.db import models

class Place(models.Model):
    name = models.CharField(max_length=255)
    visited = models.BooleanField()

    class Meta:
        # ↓ necesario porque ya partimos de una tabla creada ↓
        db_table = "places"

    def __str__(self):
        return self.name
```

> 💡 Django añade automáticamente por defecto a todos sus modelos una clave primaria `id` que es única y autoincremental.

#### Vistas

Creamos la vista que gestionará las peticiones a la página principal:

```console
vi places/views.py
```

> Contenido:

```python
from django.http import HttpResponse
from django.template import loader

from .models import Place


def index(request):
    wished = Place.objects.filter(visited=False)
    visited = Place.objects.filter(visited=True)
    template = loader.get_template('places/index.html')
    context = {
        'wished': wished,
        'visited': visited,
    }
    return HttpResponse(template.render(context, request))
```

#### Plantillas

A continuación creamos la plantilla:

```console
mkdir -p places/templates/places
vi places/templates/places/index.html
```

> Contenido:

```html
<h1>My Travel Bucket List</h1>

<h2>Places I'd Like to Visit</h2>

<ul>
  {% for place in wished %}
  <li>{{ place }}</li>
  {% endfor %}
</ul>

<h2>Places I've Already Been To</h2>

<ul>
  {% for place in visited %}
  <li>{{ place }}</li>
  {% endfor %}
</ul>
```

#### URLs

Es necesario vincular cada URL con la vista que la gestionará.

Para ello, lo primero es crear el fichero de URLs para la "aplicación" `places`:

```console
vi places/urls.py
```

> Contenido:

```python
from django.urls import path

from . import views

app_name = 'places'

urlpatterns = [
    path('', views.index, name='index'),
]
```

Y ahora enlazamos estas URLs desde el fichero principal:

```console
vi main/urls.py
```

> Contenido:

```python
from django.contrib import admin
from django.urls import path
from django.urls import include, path

urlpatterns = [
    path('admin/', admin.site.urls),
    # NUEVA LÍNEA ↓
    path('', include('places.urls', 'places')),
]
```

### Probando la aplicación en local

Con todo esto ya estamos en disposición de probar nuestra aplicación en un entorno de desarrollo (local):

```console
./manage.py runserver
Watching for file changes with StatReloader
Performing system checks...

System check identified no issues (0 silenced).

You have 18 unapplied migration(s). Your project may not work properly until you apply the migrations for app(s): admin, auth, contenttypes, sessions.
Run 'python manage.py migrate' to apply them.
November 21, 2022 - 15:40:01
Django version 4.1.3, using settings 'main.settings'
Starting development server at http://127.0.0.1:8000/
Quit the server with CONTROL-C.
```

Si accedemos a http://localhost:8000 podemos observar el resultado esperado:


### Parametrizando la configuración

Queremos que las credenciales a la base de datos sea un elemento configurable en función del entorno en el que estemos trabajando.

Esto lo podemos conseguir (entre otros) mediante un paquete de Python denominado [prettyconf](https://github.com/osantana/prettyconf) que sirve para **cargar variables de entorno mediate un fichero de configuración**.

Realizamos la instalación del paquete (ojo tener activo el entorno virtual Python):

```console
pip install prettyconf
Collecting prettyconf
  Using cached prettyconf-2.2.1.tar.gz (11 kB)
  Preparing metadata (setup.py) ... done
  Building wheels for collected packages: prettyconf
  Building wheel for prettyconf (setup.py) ... done
  Created wheel for prettyconf: filename=prettyconf-2.2.1-py2.py3-none-any.whl size=9797 sha256=300e63f4a8afcfc43446bef750e270e9a2a070a28dc9e27c66c22276779bd65c
  Stored in directory: /home/sdelquin/.cache/pip/wheels/58/75/59/2aa05b767025506114499da0585a6004bd1b8171e0b141c577
Successfully built prettyconf
Installing collected packages: prettyconf
Successfully installed prettyconf-2.2.1
MIRAR EN CLASE RUTA PARA STORED IN DIRECTORY!!!!
```

Modificamos las siguientes líneas del fiche

```console
vi main/settings.py
```

> Contenido:

```python
...
from pathlib import Path
# ↓ Nueva línea
from prettyconf import config
# ↑ Nueva línea
...
DEBUG = config('DEBUG', default=True, cast=config.boolean)
ALLOWED_HOSTS = config('ALLOWED_HOSTS', default=[], cast=config.list)
...
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql',
        'NAME': config('DB_NAME', default='travelroad'),
        'USER': config('DB_USERNAME', default='travelroad_user'),
        'PASSWORD': config('DB_PASSWORD', default='dpl0000'),
        'HOST': config('DB_HOST', default='localhost'),
        'PORT': config('DB_PORT', default=5432, cast=int)
    }
}
...
```

Comprobamos que todo sigue estando correcto:

```console
./manage.py check
System check identified no issues (0 silenced).
```

### Especificación de requerimientos

De cara a que el proyecto pueda "reproducirse" en cualquier entorno virtual necesitamos especificar los requerimientos (dependencias) en un fichero `requirements.txt`.

Lo creamos y añadimos los paquetes utilizados:

```console
vi requirements.txt
```

> Contenido:

```
django
psycopg2
prettyconf
```

### Entorno de producción

Cuando nos vayamos al entorno de producción hay que realizar una serie de pasos:

1. Clonar el repositorio git.
2. Crear el entorno virtual (tal y como se ha visto).
3. Instalar las dependencias.
4. Fijar parámetros para el entorno de producción.
5. Montar el servidor de aplicación.
6. Configurar el _virtual host_ para Nginx.
7. Preparar el script de despliegue.

### Instalar las dependencias

Una vez clonado el repositorio y con el entorno virtual creado y activo, ejecutamos el siguiente comando para instalar las dependencias del proyecto:

```console
pip install -r requirements.txt
```

### Parámetros para el entorno de producción

Necesitamos un fichero `.env` que contendrá las configuraciones concretas para el entorno de producción:

```console
vi .env
```

> Contenido:

```ini
DEBUG=0
# ↓ Dominio en el que se va a desplegar la aplicación
ALLOWED_HOSTS=travelroad.dpl.arkania.es
#DB_PASSWORD='supersecret'
```

> 💡 Tener en cuenta que el fichero `.env` hay que dejarlo fuera de control de versiones.

### Servidor de aplicación

Existen [múltiples alternativas para el despliegue de una aplicación Django](https://docs.djangoproject.com/en/4.1/howto/deployment/) y un amplio abanico de servidores de aplicación.

En este caso vamos a elegir [gunicorn](<[https://](https://gunicorn.org/)>) como servidor [WSGI (Web Server Gateway Interface)](https://medium.com/@nachoad/que-es-wsgi-be7359c6e001) para Python.

#### gunicorn

La instalación de `gunicorn` es muy sencilla ya que se trata de un paquete del ecosistema Python:

```console
pip install gunicorn
Collecting gunicorn
  Using cached gunicorn-20.1.0-py3-none-any.whl (79 kB)
Requirement already satisfied: setuptools>=3.0 in ./.venv/lib/python3.11/site-packages (from gunicorn) (65.5.0)
Installing collected packages: gunicorn
Successfully installed gunicorn-20.1.0
```

Una vez instalado, tenemos a nuestro alcance un script de gestión que permite lanzar el servidor:

```console
gunicorn main.wsgi:application
[2022-11-21 16:13:06 +0000] [280469] [INFO] Starting gunicorn 20.1.0
[2022-11-21 16:13:06 +0000] [280469] [INFO] Listening at: http://127.0.0.1:8000 (280469)
[2022-11-21 16:13:06 +0000] [280469] [INFO] Using worker: sync
[2022-11-21 16:13:06 +0000] [280470] [INFO] Booting worker with pid: 280470
```

#### Supervisor

Dado que el servidor WSGI **debemos mantenerlo activo y con la posibilidad de gestionarlo** (arrancar, parar, etc.) hemos de buscar alguna herramienta que nos ofrezca estas posibilidades.

Una alternativa es usar [servicios systemd](https://es.wikipedia.org/wiki/Systemd), como hemos visto anteriormente (Java Spring).

Pero en esta ocasión vamos a usar [Supervisor](http://supervisord.org/) que es un **sistema cliente/servidor que permite monitorizar y controlar procesos en sistemas Linux/UNIX**... ¡Y además está escrito en Python!

Para instalarlo ejecutamos el siguiente comando:

```console
sudo apt install -y supervisor
[sudo] password for dpl_eduardo:
Leyendo lista de paquetes... Hecho
Creando árbol de dependencias... Hecho
Leyendo la información de estado... Hecho
Paquetes sugeridos:
  supervisor-doc
Se instalarán los siguientes paquetes NUEVOS:
  supervisor
0 actualizados, 1 nuevos se instalarán, 0 para eliminar y 88 no actualizados.
Se necesita descargar 309 kB de archivos.
Se utilizarán 1.738 kB de espacio de disco adicional después de esta operación.
Des:1 http://deb.debian.org/debian bullseye/main arm64 supervisor all 4.2.2-2 [309 kB]
Descargados 309 kB en 0s (636 kB/s)
Seleccionando el paquete supervisor previamente no seleccionado.
(Leyendo la base de datos ... 235947 ficheros o directorios instalados actualmente.)
Preparando para desempaquetar .../supervisor_4.2.2-2_all.deb ...
Desempaquetando supervisor (4.2.2-2) ...
Configurando supervisor (4.2.2-2) ...
Created symlink /etc/systemd/system/multi-user.target.wants/supervisor.service → /lib/systemd/system/supervisor.service.
Procesando disparadores para man-db (2.9.4-4) ...
```

Podemos comprobar que el servicio está levantado y funcionando:

```console
sudo systemctl status supervisor
● supervisor.service - Supervisor process control system for UNIX
     Loaded: loaded (/lib/systemd/system/supervisor.service; enabled; vendor preset: enabled)
     Active: active (running) since Mon 2022-11-21 16:25:09 WET; 56s ago
       Docs: http://supervisord.org
   Main PID: 282865 (supervisord)
      Tasks: 1 (limit: 2251)
     Memory: 16.0M
        CPU: 92ms
        CGroup: /system.slice/supervisor.service
             └─282865 /usr/bin/python3 /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf

nov 21 16:25:09 lemon systemd[1]: Started Supervisor process control system for UNIX.
nov 21 16:25:10 lemon supervisord[282865]: 2022-11-21 16:25:10,061 CRIT Supervisor is running as ro>
nov 21 16:25:10 lemon supervisord[282865]: 2022-11-21 16:25:10,061 WARN No file matches via include>
nov 21 16:25:10 lemon supervisord[282865]: 2022-11-21 16:25:10,061 INFO RPC interface 'supervisor' >
nov 21 16:25:10 lemon supervisord[282865]: 2022-11-21 16:25:10,063 CRIT Server 'unix_http_server' r>
nov 21 16:25:10 lemon supervisord[282865]: 2022-11-21 16:25:10,063 INFO supervisord started with pi>
```

Supervisor viene con la herramienta `supervisorctl`, pero inicialmente un usuario "ordinario" no tiene permisos para usarla:

```console
supervisorctl status
error: <class 'PermissionError'>, [Errno 13] Permission denied: file: /usr/lib/python3/dist-packages/supervisor/xmlrpc.py line: 560
```

Para que un usuario no privilegiado pueda usar el servicio, la estrategia a seguir es añadir un grupo `supervisor` con permisos para ello, y luego unir al usuario a dicho grupo.

```console
sudo groupadd supervisor
```

Editamos la configuración de Supervisor:

```console
sudo vi /etc/supervisor/supervisord.conf
```

Cambiar (y añadir) lo siguiente a partir de la línea 5:

```ini
...
chmod=0770               ; socket file mode (default 0700)
chown=root:supervisor    ; grupo 'supervisor' para usuarios no privilegiados
...
```

Reiniciamos el servicio para que surtan efectos los cambios realizados:

```console
sudo systemctl restart supervisor
```

Ahora añadimos el usuario al grupo creado:

```console
sudo usermod -a -G supervisor dpl_eduardo
Añadiendo al usuario `dpl_eduardo' al grupo `supervisor' ...
Añadiendo al usuario dpl_eduardo al grupo supervisor
Hecho.
```

> Para que el cambio de grupo sea efectivo, **HABRÁ QUE SALIR Y VOLVER A ENTRAR EN LA SESIÓN**.

Una vez de vuelta en la sesión podemos comprobar que ya no se produce ningún error al lanzar el controlador de supervisor con nuestro usuario habitual:

```console
supervisorctl help

default commands (type help <topic>):
=====================================
add    exit      open  reload  restart   start   tail
avail  fg        pid   remove  shutdown  status  update
clear  maintail  quit  reread  signal    stop    version
```

#### Script de servicio

Aunque no es totalmente obligatorio, sí puede ser de utilidad que tengamos un **script de servicio** para nuestra aplicación que se encargue de levantar `gunicorn`:

```console
vi run.sh
```

> Contenido:

```bash
#!/bin/bash

cd $(dirname $0)
source .venv/bin/activate
gunicorn -b unix:/tmp/travelroad.sock main.wsgi:application
```

Damos permisos de ejecución:

```console
chmod +x run.sh
```

#### Configuración Supervisor

Lo que nos queda es **crear la configuración de un proceso supervisor** que lance nuestro servicio WSGI como servidor de aplicación para la aplicación Django.

```console
sudo vi /etc/supervisor/conf.d/travelroad.conf
```

> Contenido:

```ini
[program:travelroad]
user = dpl_eduardo
command = /run.sh #mirar en clase
autostart = true
autorestart = true
stopsignal = INT
killasgroup = true
stderr_logfile = /var/log/supervisor/travelroad.err.log
stdout_logfile = /var/log/supervisor/travelroad.out.log
```

Ahora ya podemos añadir este proceso:

```console
supervisorctl reread
travelroad: available

supervisorctl add travelroad
travelroad: added process group

supervisorctl status
travelroad                       RUNNING   pid 6018, uptime 0:00:23
```

#### Nginx

Por último nos queda configurar el _virtual host_ para derivar las peticiones al servidor WSGI:

```console
sudo vi /etc/nginx/conf.d/travelroad.conf MIRAR EN CLASE
```

> Contenido:

```nginx
server {
    listen 8080;
    server_name travelroad;

    location / {
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_pass http://unix:/tmp/travelroad.sock;
    }


    location /static/ {
        alias /home/dplprod_alumno/dpl_eduardo/ut4/a3/travelroad_django/static/;
    }
}
```

> 💡 Tener en cuenta que la ruta del socket tiene que coincidir con el script de servicio `run.sh`.

Recargamos la configuración de Nginx para que los cambios surtan efecto:

```console
sudo systemctl reload nginx
```

### Aplicación en producción

Ya podemos acceder a http://travelroad (o el dominio de producción que corresponda) obteniendo el resultado esperado:



Tener en cuenta que cuando actualicemos el código de la aplicación será necesario recargar el script de servicio para que `gunicorn` vuelva a servir la aplicación con los cambios realizados:

```console
supervisorctl restart travelroad
travelroad: stopped
travelroad: started
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
 ssh dplprod_alumno@10.102.23.40 "
  cd /home/dpl_eduardo/ut4/a3/travelroad_django
  git pull

	source .venv/bin/activate
	supervisorctl restart travelroad
	./manage.py runserver 0.0.0.0:8000
  
"
```
#### deploy.sh
```bash
#!/bin/bash

ssh dplprod_alumno@10.102.24.40 "
  cd /home/dplprod_alumno/dpl_eduardo/ut4/a3/travelroad_django
  git pull

  source .venv/bin/activate
  pip install -r requirements.txt

  # python manage.py migrate
  # python manage.py collectstatic --no-input

  supervisorctl restart travelroad
"

```

Damos permisos de ejecución:

```console
chmod +x deploy.sh
```

> 💡 `deploy.sh` es un fichero que se incluye en el control de versiones.


