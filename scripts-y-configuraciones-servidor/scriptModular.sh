#!/bin/bash

menu(){
echo -e "Bienvenid@ al script modular del servidor\nOpciones:"
echo -e "1)Gestión de usuarios y grupos\n2)Configuración de firewall\n3)Respaldo\n4)Logs\n5)Salir"
read -p "Escriba un número para acceder al script:" opcionScript
}

while [[ $opcionScript != 5 ]];do
menuOpciones
case $opcionScript in
1)gestion_usuarios.sh;;
2)firewall.sh;;
3)scriptRespaldoCS.sh;;
4)logsFedora.sh;;
5)
esac
done

