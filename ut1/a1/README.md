
<center>

# UT1-A1 Documentación y sistema de control de versiones


</center>

***Nombre:*** Aarón Álvarez Ródriguez y Eduardo Rabadán Melián
***Curso:*** 2º de Ciclo Superior de Desarrollo de Aplicaciones Web.

### ÍNDICE

+ [Introducción](#id1)
+ [Objetivos](#id2)
+ [Material empleado](#id3)
+ [Desarrollo](#id4)
+ [Conclusiones](#id5)


#### ***Introducción***. <a name="id1"></a>

Repaso sobre control de versiones

#### ***Objetivos***. <a name="id2"></a>

Aquí explicamos los objetivos que se pretenden alcanzar al realizar la práctica.

Practicar control de versiones

#### ***Material empleado***. <a name="id3"></a>

Enumeramos el material empleado tanto hardware como software y las configuraciones que hacemos (configuraciones de red por ejemplo) 

- PC
- Git
- Github
- VScode

#### ***Desarrollo***. <a name="id4"></a>

En esta parte explicamos detalladamente los pasos que seguimos para realizar la práctica incluyendo capturas de pantalla y explicando que vemos en ellas.

**1. El alumnado trabajará por parejas: user1 y user2. Indicar quién es user1 y quién es user2.**

user1: Aarón

user2: Eduardo

**2. user1 creará un repositorio público llamado git-work en su cuenta de GitHub, añadiendo un README.md y una licencia MIT.**

![alt text](./images/image.png)

**3. user1 clonará el repo y añadirá los ficheros: index.html, bootstrap.min.css y cover.css. Luego subirá los cambios al upstream.**

``` git
git clone https://github.com/aaralvrod/git-work.git
```

``` bash
touch index.html bootstrap.min.css cover.css
```

``` git
git add .
git commit -m "Añadidos ficheros"
git push 
```

**4. user2 creará un fork de git-work desde su cuenta de GitHub.**

![alt text](images/fork.png)

**5. user2 clonará su fork del repo.**

``` git
git clone https://github.com/edumel20/git-work.git
```

**6. user1 creará una issue con el título "Add custom text for startup contents".**

![alt text](images/image-1.png)

**7. user2 creará una nueva rama custom-text y modificará el fichero index.html personalizándolo para una supuesta startup.**

``` git
git checkout -b custom-text
```

Se modifica el index.html

``` git
git add index.html
git commit -m "Modificado fichero"
git push origin custom-text
```

**8. user2 enviará un PR a user1.**

![alt text](images/pullrequest.png)

**9. user1 probará el PR de user2 en su máquina (copia local) creando previamente un remoto denominado upstream, y realizará ciertos cambios en su copia local que luego deberá subir al propio PR.**

``` git
git remote add upstream https://github.com/edumel20/git-work.git
git fetch upstream
git checkout -b
```

Se realizan cambios en el index.html

``` git
git add index.html
git commit -m ""
git push upstream custom-text
```


**10. user1 y user2 tendrán una pequeña conversación en la página del PR, donde cada usuario incluirá, al menos, un cambio más.**

![alt text](images/conversacion.png)

**11. user1 finalmente aprobará el PR, cerrará la issue creada (usando una referencia a la misma) y actualizará la rama principal en su copia local.**

![alt text](images/merged.png)

**12. user2 deberá incorporar los cambios de la rama principal de upstream en su propia rama principal.**

``` git
git checkout main
git pull origin main
```

**13. user1 creará una issue con el título "Improve UX with cool colors".**

![alt text](images/newissues.png)

**14. user1 cambiará la línea 10 de cover.css a: color: purple;**

![alt text](images/cambio1.png)

**15. user1 hará simplemente un commit local en main → NO HACER git push.**

``` git
git add .
git commit -m 'Modificado color linea 10'
```

**16. user2 creará una nueva rama cool-colors y cambiará la línea 10 de cover.css a: color: darkgreen;**

``` git 
git checkout -b cool-colors
```
![alt text](images/image.png)

``` git
git add .
git commit -m 'Modificado color linea 10'
git push origin cool-colors
```

**17. user2 enviará un PR a user1.**



**18. user1 probará el PR de user2 (en su copia local). A continuación tratará de mergear el contenido de la rama cool-colors en su rama principal y tendrá que gestionar el conflicto: Dejar el contenido que viene de user2.**

``` git
git fetch upstream
git checkout -b cool-colors upstream/cool-colors
git checkout main
git merge cool-colors
```
Se mantiene color del user2

``` git
git add .
git commit
```

**19. Después del commit para arreglar el conflicto, user1 modificará la línea 11 de cover.css a: text-shadow: 2px 2px 8px lightgreen;**

![alt text](images/cambio2.png)

**20. user1 hará un commit especificando en el mensaje de commit el cambio hecho (sombra) y que se cierra la issue creada (usar referencia a la issue). A continuación subirá los cambios a origin/main.**

``` git
git add cover.css
git commit -m 'Añadido lightgreen a text-shadow, cerrado #2'
git push origin main
```

**21. user1 etiquetará esta versión (en su copia local) como 0.1.0 y después de subir los cambios creará una "release" en GitHub apuntando a esta etiqueta.**

``` git
git tag 0.1.0
git push origin 0.1.0
```

> ***IMPORTANTE:*** si estamos capturando una terminal no hace falta capturar todo el escritorio y es importante que se vea el nombre de usuario.

Si encontramos dificultades a la hora de realizar algún paso debemos explicar esas dificultades, que pasos hemos seguido para resolverla y los resultados obtenidos.

#### ***Conclusiones***. <a name="id5"></a>

Repaso importante sobre control de versiones
