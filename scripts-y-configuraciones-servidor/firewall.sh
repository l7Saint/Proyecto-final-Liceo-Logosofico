#!/bin/bash

menuOpciones(){
echo -e "Bienvenido al menú de configuración de Firewall\n"
echo -e "1)Bloquear IP\n2)Bloquear puerto\n3)Bloquear IP y puerto\n4)Salir\n"
read -p "Elija una de las opciones: " opcionUser
}

bloquearIP(){
read -p "¿Cuántas IP desea bloquear?: " cantidadIP
clear

for ((i=0; i < $cantidadIP; i++)); do
read -p "Ingrese la ip que quiera bloquear: " ipBloqueo
sudo firewall-cmd --add-rich-rule='rule family="ipv4" source address="'$ipBloqueo'" drop'
echo "La IP se bloqueó correctamente"
sudo firewall-cmd --list-rich-rules
done
}

bloquearPuerto(){
read -p "¿Cuántos puertos desea bloquear?: " cantidadPuertos
clear

for ((i=0; i < $cantidadPuertos; i++)); do
read -p "Ingrese el puerto que quiera bloquear: " puertoBloqueo
sudo firewall-cmd --add-rich-rule='rule family="ipv4" port port="'$puertoBloqueo'" protocol="tcp" drop'
echo "El puerto se bloqueó correctamente"
sudo firewall-cmd --list-rich-rules
done
}

bloquearIPyPuerto(){
read -p "¿Cuántos desea bloquear?: " cantidadBloqueo
clear

for ((i=0; i < $cantidadBloqueo; i++)); do
read -p "Ingrese la ip que desea bloquear: " ip2Bloqueo
read -p "Ingrese el puerto que desea bloquear: " puerto2Bloqueo
sudo firewall-cmd --add-rich-rule='rule family="ipv4" source address="'$ip2Bloqueo'" port port="'$puerto2Bloqueo'" protocol="tcp" drop'
echo "Ambos se bloquearon correctamente"
sudo firewall-cmd --list-rich-rules
done
}

while [[ $opcionUser != 4 ]];do
menuOpciones
case $opcionUser in
1)bloquearIP;;
2)bloquearPuerto;;
3)bloquearIPyPuerto;;
4);;
esac
done
