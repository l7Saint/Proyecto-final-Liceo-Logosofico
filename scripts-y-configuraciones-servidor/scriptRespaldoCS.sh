#!/bin/bash

#Script de creacion de respaldo
CARPETA_LOGS="."

checkLogFolder() {
	if ! [ -d "${CARPETA_LOGS}" ]; then
		mkdir "${CARPETA_LOGS}"	
	fi	
}

loggear(){
	echo -n "[$(date +%c)]: " >> $(date +%Y-%m-%d).log
	echo -n "[$(date +%c)]: "
	echo "${1}" >> $(date +%Y-%m-%d).log
	echo "${1}" 
}

menuOpciones(){
echo -e "Bienvenido al script de Respaldo del servidor\n"
echo -e "1)Respaldo local\n2)Respaldo remoto (utilizando scp)\n3)Programar respaldo con CRON\n4)Salir\n"
read -p "Elija una de las opciones: " opcionUser

}

respaldolocal(){
checkLogFolder
    if [[ -n "$1" ]]; then
        directorio="$1"
    else
        read -p "¿Qué directorio desea respaldar?: " directorio
    fi

if [[ -d "$directorio" ]]; then

	echo "El directorio existe"
	fechaActual=$(date +%d-%m-%Y--%H-%M-%S)
	archivoTAR="/tmp/$(basename "$directorio")_$fechaActual.tar.bz2"

	loggear "Creando respaldo de $directorio"

	tar -cjvf "$archivoTAR" "$directorio"

	loggear "Respaldo creado: $archivoTAR"

else

	loggear "El directorio no existe, intente de nuevo"

fi
}

respaldoRemotoSCP(){
checkLogFolder
read -p "¿Qué directorio desea respaldar?: " directorio

if [[ -d "$directorio" ]]; then

	echo "El directorio existe"

	fechaActual=$(date +%d-%m-%Y--%H-%M-%S)
	archivoTAR="/tmp/$(basename "$directorio")_$fechaActual.tar"

	loggear "Creando respaldo de $directorio"

	tar -czvf "$archivoTAR" "$directorio"

	read -p "¿Cuál es el nombre del usuario?: " nombreUser
	read -p "¿Cuál es la ip del equipo?: " ip

	loggear "Enviando respaldo a $nombreUser@$ip"

	scp "$archivoTAR" $nombreUser@$ip:/home/$nombreUser
	if [[ $? != 0 ]]; then
		loggear "Algo paso mal enviando el respaldo"
	else
		loggear "Respaldo enviado"
	fi
else

	loggear "El directorio no existe, intente de nuevo"

fi
}

respaldoCRON(){
checkLogFolder

read -p "¿Qué directorio desea respaldar?: " directorio

  if [[ ! -d "$directorio" ]]; then
        echo "El directorio no existe."
        return 1
    fi

echo "Parámetros de CRON:"
echo -e "Ingrese los siguientes valores (para cualquier valor coloque *): "

read -p "Minutos (0-59): " minuto
read -p "Horas (0-23): " hora
read -p "Día del mes (1-31): " diaMes
read -p "Mes (1-12): " mes
read -p "Día de la semana (0-7): " diaSemana

stringCron="$minuto $hora $diaMes $mes $diaSemana"
rutaAlscript="$(realpath "$0")"

comandoAejecutar="$stringCron $rutaAlscript respaldolocal \"$directorio\""

echo "Información:"
echo "$comandoAejecutar"

(crontab -l 2>/dev/null; echo "$comandoAejecutar") | crontab -

loggear "Se agregó la tarea CRON ($comandoAejecutar)"

echo "Se cambio el archivo correctamente"


}

while [[ $opcionUser != 4 ]];do
menuOpciones
case $opcionUser in
1)respaldolocal;;
2)respaldoRemotoSCP;;
3)respaldoCRON;;
4);;
esac
done

