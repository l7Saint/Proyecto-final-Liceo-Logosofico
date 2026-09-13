#!/bin/bash

menu(){

echo -e "\nBienvenid@ al menú de logs de Urbanaut\n"
echo -e "1)Inicios de sesión\n2)Inicios de sesión por SSH\n3)Uso de sudo\n4)Salir"
read -p "Elija una opción: " opcionMenu
}

inicioSesion(){
cat /var/log/secure
}

sesionSSH(){
grep 'sshd' /var/log/secure
}

usoSudo(){
grep "sudo:" /var/log/secure
}



while [[ $opcionMenu != 4 ]];do
menu
case $opcionMenu in
1)inicioSesion;;
2)sesionSSH;;
3)usoSudo;;
4);;
esac
done