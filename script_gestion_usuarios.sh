#!/usr/bin/env bash


#|===============================|#
#| Script de Gestion de Usuarios |#
#|===============================|#

# Menus
MENUINICIAL="
Script de Gestion de usuarios.

	(1) Administrar usuarios.
	(2) Administrar grupos.
	(3) Visudo.
		
	(0) Salir.
"
MENUUSUARIOS="
Administrar usuarios.

	(1) Ver lista de usuarios.
	(2) Añadir un usuario.
	(3) Modificar un usuario.
	(4) Eliminar un usuario.
	
	(0) Volver al menu principal.
"
MENUGRUPOS="
Administrar grupos.

	(0) Volver al menu principal.
"

# Funciones
imprimirMenu(){
	clear
	echo "${1}"
}

imprimirLista(){
	# Solo muestra los usuarios/grupos con id > 1000 (usuarios/grupos regulares, no del sistema) 
	# ${1} puede ser '/etc/passwd' o '/etc/group'
	lista="$(cat ${1} | cut --delimiter ':' --fields 1,3 | grep -e ':1...')"
	echo "${lista}" | less
}

ingresarSiNo(){
	clear
	salirIngresarSiNo=0
	sino_valorAIngresar="${1}"	
	while [[ ${salirIngresarSiNo} == 0 ]]; do
		echo "${sino_valorAIngresar}"
		read -p "[s/n]: " eleccion
		case "${eleccion}" in
			"s") 
				sino_returnv=0 
				salirIngresarSiNo=1
				;;
			"n") 
				sino_returnv=1 
				salirIngresarSiNo=1
				;;		
			*) echo "\'${eleccion}\' no es una eleccion valida." ;;
		esac
	done
	return ${sino_returnv}
}

ingresarInput(){
	mensajeObligatorio=""
	valorPorDefecto=""
	salirIngresar=0
	valorAIngresar="${1}"	
	while [[ ${salirIngresar} == 0 ]]; do
		if [[ "${3}" == 'obligatorio' ]]; then
			mensajeObligatorio='(OBLIGATORIO)'
		else
			# si el tercer argumento NO es 'obligatorio' pero tampoco esta
			# VACIO, entonces se toma como que es el "valor por defecto"
			if [ -n "${3}" ]; then
				valorPorDefecto="(Valor por defecto: ${3})"
			fi
		fi
		clear
		echo "Ingrese ${valorAIngresar}. ${mensajeObligatorio}${valorPorDefecto}"		
		echo
		read -p "=> " returnv
		salirIngresar=1
		# si el valor que ingreso el usuario esta vacio
		# y hay un "valor por defecto" se toma este para
		# la salida
		if [ -z "${returnv}" ] && [ -n "${valorPorDefecto}" ]; then
			returnv="${3}"
		fi
		# si el segundo argumento es 'confirmar' hace que el 
		# usuario checkee 2 veces antes de determinar el valor
		if [[ "${2}" == 'confirmar' ]]; then
			ingresarSiNo "Esta seguro que ${valorAIngresar} sea ${returnv}?"
			if [[ "$?" == 0  ]]; then
				salirIngresar=1
			else
				salirIngresar=0
			fi
		fi
		# si el tercer argumento es 'obligatorio' fuerza a 
		# que el valor que ingreso el usuario no sea VACIO
		if [[ "${3}" == 'obligatorio' && -z "${returnv}" ]]; then
			echo "No es valido que ${valorAIngresar} este vacio."
			salirIngresar=0
		fi
	done
}

anadirUsuario(){
	ingresarInput 'el nombre del usuario' 'confirmar' 'obligatorio'
	nombreUsuario="${returnv}"
	ingresarInput 'la terminal del usuario' 'confirmar' '/bin/bash'
	terminalUsuario="${returnv}"	
	ingresarInput 'la carpeta Home del usuario' 'confirmar' "/home/${nombreUsuario}"
	homeUsuario="${returnv}"	
	# si no elige ningun grupo principal, se toma el grupo del mismo nombre del usuario
	ingresarInput 'grupo principal del usuario' 'confirmar' "${nombreUsuario}"
	grupoPrincipalUsuario="${returnv}"
	if [[ "${grupoPrincipalUsuario}" != "${nombreUsuario}" ]]; then
		argumentogrupoPrincipalUsuario="--gid ${grupoPrincipalUsuario}"
	fi
	ingresarInput 'grupos secundarios del usuario' 'confirmar' 'VACIO'
	gruposSecundariosUsuario="${returnv}"
	if [[ "${gruposSecundariosUsuario}" == 'VACIO' ]]; then
		argumentogruposSecundariosUsuario=""
	else
		argumentogruposSecundariosUsuario="-G${gruposSecundariosUsuario}"
	fi
	ingresarInput 'fecha de expiracion del usuario (formato: YYYY-MM-DD)' 'confirmar' 'VACIO'
	fechaExpiracionUsuario="${returnv}"
	if [[ "${fechaExpiracionUsuario}" == 'VACIO' ]]; then
		argumentofechaExpiracionUsuario=""
	else
		argumentofechaExpiracionUsuario="--expiredate ${fechaExpiracionUsuario}"
	fi
	ingresarInput 'contrasena inicial del usuario' 'confirmar' 'VACIO'
	contrasenaUsuario="${returnv}"
	if [[ "${contrasenaUsuario}" == 'VACIO' ]]; then
		argumentocontrasenaUsuario=""
	else
		# cuando es -1 el valor la contrasena nunca expira
		ingresarInput 'numero de dias antes que la contrasena expire' 'confirmar' '-1'
		numeroDiasContrasena=${returnv}
		if [[ "${numeroDiasContrasena}" != '-1' ]]; then
			argumentocontrasenaUsuario="--inactive ${numeroDiasContrasena}"
		fi
	fi

	#me cago en bash que manda los putos argumentos vacios como strings vacios y useradd los parsea mal
	#arreglar esta cagada
	if ! error=$(useradd \
	--create-home \
	--shell "${terminalUsuario}" \
	--home-dir "${homeUsuario}" "${argumentogrupoPrincipalUsuario}" "${argumentogruposSecundariosUsuario}" "${argumentofechaExpiracionUsuario}" "${argumentocontrasenaUsuario}" "${nombreUsuario}" 2>&1)
	then
		echo "Hubo un error inesperado creando el usuario. No se creo ningun usuario." 
		echo "Codigo de error de useradd: $?"
		echo "Mensaje de error de useradd: ${error}"
		echo "Comando ejecutado:"
		echo "
useradd \
--create-home \
--shell ${terminalUsuario} \
--home-dir ${homeUsuario} \
${argumentogrupoPrincipalUsuario} ${argumentogruposSecundariosUsuario} ${argumentofechaExpiracionUsuario} ${argumentocontrasenaUsuario} ${nombreUsuario}"
		read
	else
		if [[ "${contrasenaUsuario}" != 'VACIO' ]]; then
			chpasswd <<< "${nombreUsuario}:${contrasenaUsuario}"
		fi
		echo "Usuario creado correctamente."
	fi
}

administrarUsuarios(){
	salirMenuUsuarios=0
	while [[ ${salirMenuUsuarios} == 0 ]]; do
		imprimirMenu "${MENUUSUARIOS}"
		read -p "=> " input
		case "${input}" in 
			1) imprimirLista '/etc/passwd' ;;
			2) anadirUsuario ;;
			3) ;;
			4) ;;
			0) salirMenuUsuarios=1 ;;
			*) ;;
		esac
		clear
	done
}

administrarGrupos(){
	salirMenuGrupos=0
	while [[ ${salirMenuGrupos} == 0 ]]; do
		imprimirMenu "${MENUGRUPOS}"
		read -p "=> " input
		case "${input}" in 
			1) imprimirLista '/etc/group' ;;
			2) ;;
			3) ;;
			0) salirMenuGrupos=1 ;;
			*) ;;
		esac
		clear
	done
}

# -- Funcion Main --
main() {
	salirMenuPrincipal=0
	while [[ ${salirMenuPrincipal} == 0 ]]; do
		imprimirMenu "${MENUINICIAL}"
		read -p "=> " input
		case "${input}" in 
			1) administrarUsuarios ;;
			2) administrarGrupos ;;
			3) 
				if [ -z "${EDITOR}" ]; then
					ingresarInput 'editor de terminal a usar' 'confirmar' 'vi'
					EDITOR=${returnv}
				fi
				visudo ;;
			0) salirMenuPrincipal=1 ;;
			*) ;;
		esac
		clear
	done
	exit
}
main
