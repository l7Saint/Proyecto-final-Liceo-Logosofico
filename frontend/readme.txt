El frontend sera basado en lo que hizo lucia en canva.
Link: https://www.canva.com/design/DAHJRYbAfbc/B3exiPNgeSRB6F0dzbXv4Q/edit


==Guia general de diseno del frontend==

La idea general es dividir la pagina por pantallas, es decir, diferentes links que sirvan
para un proposito, una posible accion que se pueda hacer dentro de la pagina. Por ejemplo
una pantalla podria ser la pantalla de Login, en donde tengas que meter tus credenciales
para iniciar sesion, otra puede ser una pantalla en la que veas informacion de tu cuenta
y otra puede ser el mapa que te indique donde es mas probable que haya estacionamiento o
no.

=Lista de pantallas (NO TERMINADA)=:
-> Home (Redirige a login, nosotros, contacto. no esta decidido que cosas todavia)
-> Login (en donde el usuario inicia sesion)
-> Registrar (en donde el usuario crea una cuenta)
-> Mi cuenta (en donde el usuario puede ver informacion mas detallada de su cuenta)
-> Nosotros (Informacion general de la empresa, nosotros o ni idea)
-> Mapa (???)(En donde el usuario podria ver en que lugar es mas probable encontrar lugar
libre)
Despues no estaria seguro si anadir pantallas como "Mi Vehiculo", "Historial" o
"Estadisticas" porque todavia no sabemos bien que se va a poder hacer en la pagina.

Hay una plantilla que mas o menos dicta el aspecto general de cada pantalla de la pagina
que es "plantilla.html", tambien hay un archivo general de estilos css "style.css".
Se quiere hacer una pagina que tenga tanto un sidebar a la izquierda como un navbar, ambos
serian de utilidad a la hora de manejarse en la pagina.

- navbar --> Manejo general dentro de la pagina, (ej. Links que te lleven al Login, ver tu
cuenta, mapa, etc...)

- sidebar --> Manejo dentro de la pantalla que el usuario este en ese momento, es decir,
si yo estoy en el login el sidebar tiene que tener alguna forma de ayudarme o controlar el
Login (ej. que en el sidebar este los campos para iniciar sesion junto con el boton)

Despues de tanto el navbar como el sidebar estaria el contenido en si de la pantalla (ej.
el mapa, informacion de tu cuenta, etc...).
