#!/bin/bash

menuOpciones(){
echo -e "Bienvenido al menú de configuración de Firewall\n"
echo -e "1)Bloquear IP\n2)Bloquear puerto\n3)Bloquear IP y puerto\n4)Salir\n"
read -p "Elija una de las opciones: " opcionUser
}

bloquearIP(){
read -p "¿Cuántas IP desea bloquear?: " cantidadIP
clear

if ! [[ "$cantidadIP" =~ ^[0-9]+$ ]]; then
cantidadIP=0
fi

for ((i=0; i < $cantidadIP; i++)); do
read -p "Ingrese la ip que quiera bloquear: " ipBloqueo
sudo firewall-cmd --permanent --add-rich-rule='rule family="ipv4" source address="'$ipBloqueo'" drop'
if [[ $? == 0 ]]; then
echo "La IP se bloqueó correctamente"
sudo firewall-cmd --reload
else
echo "No se pudo bloquear la IP"
fi
sudo firewall-cmd --list-rich-rules
done
}

bloquearPuerto(){
read -p "¿Cuántos puertos desea bloquear?: " cantidadPuertos
clear

if ! [[ "$cantidadPuertos" =~ ^[0-9]+$ ]]; then
cantidadPuertos=0
fi

for ((i=0; i < $cantidadPuertos; i++)); do
read -p "Ingrese el puerto que quiera bloquear: " puertoBloqueo
sudo firewall-cmd --permanent --add-rich-rule='rule family="ipv4" port port="'$puertoBloqueo'" protocol="tcp" drop'
if [[ $? == 0 ]]; then
echo "El puerto se bloqueó correctamente"
sudo firewall-cmd --reload
else
echo "No se pudo bloquear el puerto"
fi
sudo firewall-cmd --list-rich-rules
done
}

bloquearIPyPuerto(){
read -p "¿Cuántos desea bloquear?: " cantidadBloqueo
clear

if ! [[ "$cantidadBloqueo" =~ ^[0-9]+$ ]]; then
cantidadBloqueo=0
fi

for ((i=0; i < $cantidadBloqueo; i++)); do
read -p "Ingrese la ip que desea bloquear: " ip2Bloqueo
read -p "Ingrese el puerto que desea bloquear: " puerto2Bloqueo
sudo firewall-cmd --permanent --add-rich-rule='rule family="ipv4" source address="'$ip2Bloqueo'" port port="'$puerto2Bloqueo'" protocol="tcp" drop'
if [[ $? == 0 ]]; then
echo "Ambos se bloquearon correctamente"
sudo firewall-cmd --reload
else
echo "No se pudieron bloquear la IP y el puerto"
fi
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
