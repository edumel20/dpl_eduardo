<center>

# UT2-A1: Implantación de arquitecturas web

</center>

**_Nombre:_** Eduardo Rabadán Melián
**_Curso:_** 2º de Ciclo Superior de Desarrollo de Aplicaciones Web.

### ÍNDICE

- [Introducción](#id1)
- [Objetivos](#id2)
- [Material empleado](#id3)
- [Desarrollo](#id4)
- [Conclusiones](#id5)

#### **_Introducción_**. <a name="id1"></a>

En esta práctica vamos a ver cómo crear una calculadora básica con PHP usando Nginx como servidor web y PHP-FPM para procesar el código PHP. 

Nginx es un servidor web muy rápido que sirve archivos estáticos y también actúa como intermediario. PHP-FPM es el componente que permite a Nginx ejecutar scripts PHP.

La idea es tener un servidor Nginx que reciba las peticiones y delegue a PHP-FPM cuando necesite ejecutar código PHP. Esta configuración es muy común en producción por su buen rendimiento.

#### **_Objetivos_**. <a name="id2"></a>

- Instalar y configurar Nginx como servidor web
- Instalar y configurar PHP-FPM para procesar scripts PHP
- Crear una aplicación PHP funcional (calculadora básica)
- Configurar Nginx para que interconecte con PHP-FPM
- Verificar el correcto funcionamiento de la aplicación desplegada

#### **_Material empleado_**. <a name="id3"></a>

**Hardware:**
- Ordenador con conexión a red
- Máquina virtual con Linux (Ubuntu/Debian)

**Software:**
- Sistema operativo: Ubuntu 22.04 LTS
- Servidor web: Nginx
- Intérprete PHP: PHP-FPM (versión 8.3)

**Configuraciones realizadas:**
- Instalación de paquetes necesarios vía apt
- Configuración del bloque server de Nginx
- Ajuste de permisos en el directorio de publicación
- Configuración del socket de PHP-FPM

#### **_Desarrollo_**. <a name="id4"></a>

##### Nativo:

**Paso 1: Actualización del sistema e instalación de Nginx**

Primero, actualizamos los repositorios e instalamos el servidor web Nginx:

```bash
sudo apt update
sudo apt install nginx -y
```

**Paso 2: Instalación de PHP y PHP-FPM**

Instalamos PHP junto con PHP-FPM, que es el componente que permite la ejecución de scripts PHP:

```bash
sudo apt install php php-fpm -y
```

**Paso 3: Creación de la estructura del proyecto**

Los archivos de la calculadora ya están alojados en el directorio del proyecto:

```bash
ls /home/daw2/Escritorio/dpl_eduardo/ut2/a1/files/
```

**Paso 4: Configuración de Nginx**

Editamos el archivo de configuración del sitio para servir desde el directorio del proyecto:

```bash
sudo nano /etc/nginx/sites-available/calculadora
```

Contenido de la configuración:

```nginx
server {
    listen 80;
    server_name localhost;
    root /home/daw2/Escritorio/dpl_eduardo/ut2/a1/files;
    index calculadora_nativo.php;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Habilitamos el sitio y reiniciamos Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/calculadora /etc/nginx/sites-enabled/
sudo unlink /etc/nginx/sites-enabled/default
sudo systemctl restart nginx
```

**Paso 5: Ajuste de permisos**

Asignamos los permisos adecuados al directorio del proyecto:

```bash
sudo chown -R www-data:www-data /home/daw2/Escritorio/dpl_eduardo/ut2/a1/files
sudo chmod -R 755 /home/daw2/Escritorio/dpl_eduardo/ut2/a1/files
```
```bash
sudo chmod o+x /home/daw2
sudo chmod o+x /home/daw2/Escritorio
sudo chmod o+x /home/daw2/Escritorio/dpl_eduardo
sudo chmod o+x /home/daw2/Escritorio/dpl_eduardo/ut2
sudo chmod o+x /home/daw2/Escritorio/dpl_eduardo/ut2/a1
```
**Paso 6: Reiniciar servicios**
Reiniciamos tanto Nginx como PHP-FPM

```bash
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
```

**Paso 6: Verificación del funcionamiento**

Accedemos a la calculadora desde el navegador:

```bash
http://localhost/
```

![calculadora_nativo.php](https://github.com/edumel20/dpl_eduardo/blob/main/ut2/a1/calculadora_web.png?raw=true)

> **_NOTA:_** Si encontramos dificultades a la hora de realizar algún paso debemos explicar esas dificultades, los pasos seguidos para resolverlas y los resultados obtenidos.


#### **_Conclusiones_**. <a name="id5"></a>

En esta práctica hemos aprendido a configurar un entorno de desarrollo web completo con Nginx y PHP-FPM. Esta combinación es muy popular actualmente porque es rápida, segura y escalable.

Gracias a la separación entre el servidor web (Nginx) y el procesador PHP, conseguimos mejor rendimiento y estabilidad. Además, esta configuración es la base que utilizan muchos frameworks modernos como Laravel o Symfony.
