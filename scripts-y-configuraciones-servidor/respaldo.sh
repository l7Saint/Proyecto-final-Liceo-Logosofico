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

main(){
	if [[ "$#" != 4 ]]; then
		loggear "Cantidad erronea de argumentos. argNumero=$#"
		exit
	fi
	checkLogFolder
	dir="$1"
	sshkey="$2"
	user="$3"
	host="$4"

	backupname=$(echo "${dir}" | sed "s/\//-/g")
	fecha=$(date +%Y-%m-%d)
	backupname="${fecha}_${backupname}"
	loggear "${backupname}"

	loggear $(tar -jcvf "/tmp/${backupname}.tar.bz2" "${dir}")
	loggear $(rsync -aiv -e "ssh -i ${sshkey}" "/tmp/${backupname}.tar.bz2" ${user}@${host}) 
}

main $@
