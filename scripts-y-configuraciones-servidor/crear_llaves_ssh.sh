#!/bin/bash

# ARCHIVOS:
#	id_rsa: llave privada (tiene que estar en la maquina que se conecta por ssh)
#	id_rsa.pub: llave publica (tiene que estar en el servidor al cual te conectas por ssh)

echo "El servidor SSH tiene que estar configurado para utilizar contraseña para que este script funcione correctamente."
echo "Después se puede cambiar para utilizar solo llave."
echo
echo "Es recomendable ponerle una contraseña a la llave privada."

# El argumento "-t rsa" significa que use el algoritmo RSA y el argumento "-b 4096" determina el tamano de la llave. Mientras mas grande, mas seguro.
# El argumento "-f ~/id_rsa" indica que la llave se guarde en ese archivo 
ssh-keygen -f ~/.ssh/id_rsa -t rsa -b 4096
read -p "Usuario con el que iniciar sesion en el servidor: " user
read -p "Dominio del servidor/Direccion IP: " host

#ssh-copy-id instala la llave publica en el servidor
ssh-copy-id -i ~/.ssh/id_rsa.pub ${user}@${host}
if [[ "$?" == 0 ]] then;
	echo "Llave publica y privada creadas correctamente e instaladas."
	rm ~/.ssh/id_rsa.pub
else
	echo "Hubo un error."
fi
