<center>

# DESARROLLO DE LA CAPA DE DATOS CON PostgreSQL - TravelRoad

</center>

***Nombre:*** Eduardo Rabadán Melián  
***Curso:*** 2º de Ciclo Superior de Desarrollo de Aplicaciones Web  
---

### ÍNDICE

+ [Introducción](#id1)
+ [Objetivos](#id2)
+ [Material empleado](#id3)
+ [Desarrollo](#id4)
  - [PostgreSQL - Entorno de Desarrollo](#id4-1)
  - [PostgreSQL - Entorno de Producción](#id4-2)
  - [pgAdmin - Entorno de Desarrollo](#id4-3)
  - [pgAdmin - Entorno de Producción](#id4-4)
  - [Aplicación PHP - Entorno de Desarrollo](#id4-5)
  - [Aplicación PHP - Entorno de Producción](#id4-6)
  - [Despliegue](#id4-7)
+ [Conclusiones](#id5)

---

#### ***Introducción***. <a name="id1"></a>

Esta práctica tiene como objetivo principal preparar la infraestructura de la capa de datos para una aplicación web llamada TravelRoad. Se trabaja con PostgreSQL como sistema gestor de bases de datos, instalándolo tanto en entorno local (desarrollo) como en máquina remota (producción).

La aplicación TravelRoad permite gestionar una lista de lugares de viaje, distinguiendo entre lugares visitados y lugares por visitar. Se desarrolla una aplicación PHP que se conecta a la base de datos PostgreSQL para mostrar esta información.

#### ***Objetivos***. <a name="id2"></a>

Los objetivos de esta práctica son:

1. Instalar y configurar PostgreSQL en entornos de desarrollo y producción
2. Instalar y configurar pgAdmin para la administración visual de las bases de datos
3. Desarrollar una aplicación PHP que acceda a los datos de TravelRoad
4. Configurar virtual hosts con dominios específicos para cada servicio
5. Implementar un sistema de despliegue automatizado mediante script
6. Aplicar medidas de seguridad con certificados SSL

#### ***Material empleado***. <a name="id3"></a>

**Hardware:**
- Máquina local (desarrollo): Ubuntu/Debian
- Máquina remota (producción): Servidor Arkania (10.102.24.40)

**Software:**
- PostgreSQL 14+
- pgAdmin 4
- PHP 8.4
- Nginx
- Git

**Dominios configurados:**
- Desarrollo: php.travelroad.local, pgadmin.local
- Producción: php.travelroad.arkania.es, pgadmin.arkania.es

#### ***Desarrollo***. <a name="id4"></a>

---

##### ***PostgreSQL - Entorno de Desarrollo***. <a name="id4-1"></a>

**Instalación en local:**

```bash
sudo apt update
sudo apt install -y postgresql postgresql-contrib
```

**Configuración de credenciales (Desarrollo):**

| Parámetro | Valor |
|-----------|-------|
| Usuario | travelroad_user |
| Contraseña | dpl0000 |
| Base de datos | travelroad |
| Host | localhost |

**Creación de usuario y base de datos:**

```sql
sudo -u postgres createuser -P travelroad_user
sudo -u postgres createdb travelroad
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE travelroad TO travelroad_user;"
```

**Carga de datos de prueba:**

Se ejecuta el script SQL con los datos de prueba de TravelRoad para poblar la base de datos con lugares de viaje.

---

##### ***PostgreSQL - Entorno de Producción***. <a name="id4-2"></a>

**Conexión remota por SSH:**

```bash
ssh dplprod_alumno@10.102.24.40
```

**Instalación en servidor de producción:**

```bash
sudo apt update
sudo apt install -y postgresql postgresql-contrib
```

**Configuración de credenciales (Producción):**

| Parámetro | Valor |
|-----------|-------|
| Usuario | travelroad_prod_user |
| Contraseña | (Credencial segura de producción) |
| Base de datos | travelroad |
| Host | localhost |

**Nota:** Las credenciales de producción son distintas a las de desarrollo por motivos de seguridad.

---

##### ***pgAdmin - Entorno de Desarrollo***. <a name="id4-3"></a>

**Instalación:**

```bash
sudo apt install -y pgadmin4-web
sudo /usr/pgadmin4/bin/setup-web.sh
```

**Configuración:**
- Dominio: pgadmin.local
- Credenciales de acceso: admin / dpl0000
- Puerto: 443 (HTTPS)

**Configuración del servidor pgAdmin:**

1. Acceder a pgAdmin en: https://pgadmin.local
2. Iniciar sesión con las credenciales configuradas
3. Crear un nuevo servidor "TravelRoad"
4. Configurar la conexión con las credenciales de desarrollo

---

##### ***pgAdmin - Entorno de Producción***. <a name="id4-4"></a>

**Instalación:**

```bash
sudo apt install -y pgadmin4-web
sudo /usr/pgadmin4/bin/setup-web.sh
```

**Configuración:**
- Dominio: pgadmin.arkania.es
- Credenciales de acceso: admin_prod / (Contraseña segura)
- Certificado SSL: auto-firmado con Let's Encrypt
- Puerto: 443 (HTTPS)

**Seguridad:**
- Se ha añadido certificado de seguridad en la máquina de producción
- Configuración de HTTPS forzada

**URL de pgAdmin en Producción:**  
https://pgadmin.arkania.es

---

##### ***Aplicación PHP - Entorno de Desarrollo***. <a name="id4-5"></a>

**Instalación de extensión PostgreSQL para PHP:**

```bash
sudo apt install -y php8.2-pgsql
sudo systemctl restart apache2
```

**Estructura del proyecto:**

```
dpl_eduardo/ut4/a2/
├── README.md
└── travelroad/
    ├── index.php
    ├── config.php
    └── deploy.sh
```

**Código fuente de la aplicación:**

La aplicación PHP se encuentra en el repositorio GitHub. El código fuente está disponible en:

**Enlace al código fuente:**  
https://github.com/dpl-eduardo/dpl_eduardo/ut4/a2/travelroad

**Archivo index.php:**

El archivo principal de la aplicación conecta con la base de datos PostgreSQL y muestra los lugares de viaje, separándolos en dos listas: lugares por visitar y lugares ya visitados.

**Archivo config.php:**

Contiene las credenciales de acceso a la base de datos. Este archivo está excluido del control de versiones mediante .gitignore.

```php
<?php
$host = "localhost";
$dbname = "travelroad";
$user = "travelroad_user";
$password = "dpl0000";
?>
```

**Virtual Host para desarrollo:**

- Dominio: php.travelroad.local
- DocumentRoot: /home/daw/Escritorio/dpl_eduardo/ut4/a2/travelroad
- Puerto: 80

---

##### ***Aplicación PHP - Entorno de Producción***. <a name="id4-6"></a>

**Clonación del repositorio en producción:**

```bash
ssh dplprod_alumno@10.102.24.40
cd ~/dpl_eduardo/ut4/a2
git clone <repositorio> travelroad
```

**Configuración de config.php en producción:**

El archivo config.php contiene las credenciales de producción:

```php
<?php
$host = "localhost";
$dbname = "travelroad";
$user = "travelroad_prod_user";
$password = "(contraseña_producción)";
?>
```

**Virtual Host para producción:**

- Dominio: php.travelroad.arkania.es
- DocumentRoot: /home/dplprod_alumno/dpl_eduardo/ut4/a2/travelroad
- Puerto: 443 (HTTPS)
- Certificado SSL: Activado
- Redirección www: Configurada

**URL de la aplicación en Producción:**  
https://php.travelroad.arkania.es

---

##### ***Despliegue***. <a name="id4-7"></a>

**Script deploy.sh:**

Se ha creado un script de despliegue automatizado en la carpeta del repositorio:

```bash
#!/bin/bash
ssh dplprod_alumno@10.102.24.40 "cd dpl_eduardo/ut4/a2/travelroad && git pull"
```

**Otorgar permisos de ejecución:**

```bash
chmod +x travelroad/deploy.sh
```

**Funcionamiento del script:**

1. El script conecta por SSH al servidor de producción
2. Navega hasta el directorio del proyecto
3. Ejecuta `git pull` para obtener los últimos cambios
4. Los cambios se reflejan automáticamente en la aplicación de producción

**Prueba del script:**

Tras realizar cambios en el código de la aplicación:
1. Hacer commit de los cambios en Git
2. Ejecutar `./travelroad/deploy.sh`
3. Verificar que los cambios aparecen en https://php.travelroad.arkania.es

---

#### ***Conclusiones***. <a name="id5"></a>

La práctica ha permitido consolidar los conocimientos sobre:

1. **Administración de PostgreSQL:** Se ha aprendido a instalar, configurar y gestionar usuarios en PostgreSQL tanto en entorno local como en servidor remoto.

2. **Gestión de bases de datos:** La carga de datos de prueba y la conexión desde aplicaciones externas son fundamentales para el desarrollo web moderno.

3. **Administración visual con pgAdmin:** pgAdmin facilita enormemente la gestión de bases de datos, permitiendo operaciones complejas mediante interfaz gráfica.

4. **Desarrollo PHP con PostgreSQL:** La integración de PHP con PostgreSQL mediante la extensión pgsql permite crear aplicaciones web dinámicas que acceden a datos almacenados.

5. **Configuración de virtual hosts:** La configuración de dominios específicos para cada aplicación es esencial para organizar y acceder a los servicios de desarrollo y producción.

6. **Seguridad en producción:** La implementación de certificados SSL y credenciales diferenciadas entre entornos son buenas prácticas de seguridad que deben aplicarse en cualquier proyecto.

7. **Despliegue automatizado:** El script deploy.sh simplifica el proceso de actualización de la aplicación en producción, reduciendo errores manuales.

Esta infraestructura de datos sirve como base sólida para el desarrollo de aplicaciones web más complejas que requieran persistencia de datos.

---


