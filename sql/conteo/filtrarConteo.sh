#!/bin/bash
# Script para filtrar el dataset de conteo vehicular de la IM
# https://catalogodatos.gub.uy/es/dataset/conteo-vehicular-en-las-principales-avenidas-de-montevideo

# Primer punto en la esquina superior izquierda 
latitud1="-34.8861260"
longitud1="-56.1682944"

# Segundo punto en la esquina inferior derecha
latitud2="-34.8933556"
longitud2="-56.1542466"

archivoConteo="${1}"
if ! [[ -f "${archivoConteo}" ]]; then
	echo 'Archivo de conteo no especificado o no existente'
	exit
fi
outputFiltrado="${2}"
if [[ -z "${outputFiltrado}" ]]; then
	echo 'Archivo de output no especificado.'
	exit
fi

# $8 es el campo de la latitud y $9 es el campo de la longitud
echo "Filtrando el archivo original, puede tardar unos segundos."
awk -F','\
	-v latitud1="${latitud1}"\
	-v longitud1="${longitud1}"\
	-v latitud2="${latitud2}"\
	-v longitud2="${longitud2}"\
	'$8 < latitud1 && $8 > latitud2 && $9 > longitud1 && $9 < longitud2'\
	"${archivoConteo}" > .tmp

cant_lineas=$( cat .tmp | wc -l )
cant_lineas_orig=$( cat "${archivoConteo}" | wc -l )
cant_filtradas=$(( $cant_lineas_orig - $cant_lineas ))
echo "Filtradas ${cant_filtradas} lineas del archivo original de ${cant_lineas_orig} por no coincidir en el area."

echo "Sacando campos innecesarios, puede tardar unos segundos."
# Solo escribir los campos en el nuevo CSV que sean los sig.:
# 3: hora
# 4: fecha
# 8: latitud
# 9: longitud
# 10: volumen_hora
awk -F','\
	-v OFS=','\
	'{print $3, $4, $8, $9, $10}'\
	.tmp >> "${outputFiltrado}"

rm .tmp
echo "Listo."
