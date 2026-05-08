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
	
	(1) Ver lista de grupos.
	(2) Añadir un grupo.
	(3) Modificar un grupo.

	(0) Volver al menu principal.
"
MENUMODIFICARUSUARIOS="
Modificar usuarios.
	
	(1) Cambiar UID.
	(2) Cambiar Login Name.
	(3) Cambiar carpeta Home.
	(4) Cambiar Shell.
	(5) Añadir grupos secundarios.
	(6) Eliminar todos los grupos secundarios.
	(7) Cambiar grupo principal.
	(8) Bloquear al Usuario.
	(9) Desbloquear al Usuario.

	(0) Volver al menu usuarios.
"

# Funciones generales
imprimirMenu(){
	clear
	echo "${1}"
}

imprimirLista(){
	# Solo muestra los usuarios/grupos con id >= 1000, id <= 9999 (usuarios/grupos regulares, no del sistema) 
	# ${1} puede ser '/etc/passwd' o '/etc/group'
	lista="$(cat ${1} | cut --delimiter ':' --fields 1,3 | grep -e ':....')"
	echo "${lista}" | less
}

existe(){
	archivo="${1}"
	nombre="${2}"
	cat "${archivo}" | grep -qe "^${nombre}"
	return $?
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

ejecutarComando(){
	cmd="${1}"
	if ! error=$(${cmd} 2>&1); then
		echo "Hubo un error inesperado ejecutando el comando. No se realizo ninguna accion." 
		echo "Codigo de error del comando: $?"
		echo "Mensaje de error de comando: ${error}"
		echo "Comando ejecutado:"
		echo "${cmd}"
		echo
		read -p "[Presiona Enter]" poop
	else
		echo "Comando ejecutado correctamente."
	fi
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

# Funciones de usuarios
useraddEjecutar() {
	e_nombreUsuario="${1}"	
	e_terminalUsuario="${2}"	
	e_homeUsuario="${3}"	
	e_argumentogrupoPrincipalUsuario="${4}"	
	e_argumentogruposSecundariosUsuario="${5}"	
	e_argumentofechaExpiracionUsuario="${6}"	
	e_argumentocontrasenaUsuario="${7}"	
	cmd="useradd --create-home "
	cmd+="--home-dir ${e_homeUsuario} "
	cmd+="--shell ${e_terminalUsuario} "
	cmd+="${e_argumentogrupoPrincipalUsuario}"
	cmd+="${e_argumentogruposSecundariosUsuario}"
	cmd+="${e_fechaExpiracionUsuario}"
	cmd+="${e_argumentocontrasenaUsuario}"
	cmd+="${e_nombreUsuario}"
	if ! error=$(${cmd} 2>&1); then
		echo "Hubo un error inesperado creando el usuario. No se creo ningun usuario." 
		echo "Codigo de error de useradd: $?"
		echo "Mensaje de error de useradd: ${error}"
		echo "Comando ejecutado:"
		echo "${cmd}"
		echo
		read -p "[Presiona Enter]" poop
	else
		if [[ "${contrasenaUsuario}" != 'VACIO' ]]; then
			chpasswd <<< "${nombreUsuario}:${contrasenaUsuario}"
		fi
		echo "Usuario creado correctamente."
	fi
}

anadirUsuario(){
	ingresarInput 'el nombre del usuario' 'confirmar' 'obligatorio'
	nombreUsuario="${returnv}"
	#Comprobar si el usuario ya existe
	while $(existe "/etc/passwd" "${nombreUsuario}"); do
		echo "Ese usuario ya existe"
		echo
		read -p "[Presiona enter]"
		ingresarInput 'el nombre del usuario' 'confirmar' 'obligatorio'
		nombreUsuario="${returnv}"
	done
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
		argumentogruposSecundariosUsuario="-G${gruposSecundariosUsuario} "
	fi
	ingresarInput 'fecha de expiracion del usuario (formato: YYYY-MM-DD)' 'confirmar' 'VACIO'
	fechaExpiracionUsuario="${returnv}"
	if [[ "${fechaExpiracionUsuario}" == 'VACIO' ]]; then
		argumentofechaExpiracionUsuario=""
	else
		argumentofechaExpiracionUsuario="--expiredate ${fechaExpiracionUsuario} "
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
			argumentocontrasenaUsuario="--inactive ${numeroDiasContrasena} "
		fi
	fi
	
	useraddEjecutar "${nombreUsuario}" "${terminalUsuario}" "${homeUsuario}" "${argumentogrupoPrincipalUsuario}" "${argumentogruposSecundariosUsuario}" "${argumentofechaExpiracionUsuario}" "${argumentocontrasenaUsuario}"
}

modificarUsuario(){
	ingresarInput 'nombre del usuario a modificar' 'confirmar' 'obligatorio'
	nombreUsuario="${returnv}"
	salirModificarUsuario=0
	while [[ ${salirModificarUsuarios} == 0 ]]; do
		echo "Usuario a modificar: ${nombreUsuario}"
		imprimirMenu "${MENUMODIFICARUSUARIOS}"
		read -p "=> " input
		case "${input}" in 
			1)
				ingresarInput 'nuevo UID' 'confirmar' 'obligatorio'
				ejecutarComando "usermod --uid ${returnv} ${nombreUsuario}";;
			2)
				ingresarInput 'nuevo Login Name' 'confirmar' 'obligatorio'
				ejecutarComando "usermod --login ${returnv} ${nombreUsuario}";;
			3)
				ingresarInput 'nueva carpeta Home' 'confirmar' 'obligatorio'
				ejecutarComando "usermod --move-home --home ${returnv} ${nombreUsuario}";;
			4)
				ingresarInput 'nueva Shell' 'confirmar' 'obligatorio'
				ejecutarComando "usermod --shell ${returnv} ${nombreUsuario}";;
			5)
				ingresarinput 'grupos secundarios a añadir (formato: grupo1,grupo2,grupo3)' 'confirmar' 'obligatorio'
				ejecutarcomando "usermod --append --groups ${returnv} ${nombreusuario}";;
			6)
				if $(ingresarSiNo "Esta SEGURO de eliminar TODOS los grupos secundarios del usuario ${nombreUsuario}?"); then
					ejecutarcomando "usermod --groups \'\' ${nombreusuario}";;
				else
					echo "No se hizo ningun cambio."
					echo "[Presione Enter]"
					read
				fi;;
			7)
				ingresarinput 'nuevo grupo principal' 'confirmar' 'obligatorio'
				ejecutarcomando "usermod --gid ${returnv} ${nombreusuario}";;
			8)
				if $(ingresarSiNo "Esta SEGURO de BLOQUEAR al usuario ${nombreUsuario}? (Se puede revertir)"); then
					ejecutarcomando "usermod --lock ${nombreusuario}";;
				else
					echo "No se hizo ningun cambio."
					echo "[Presione Enter]"
					read
				fi;;
			9)
				if $(ingresarSiNo "Esta SEGURO de DESBLOQUEAR al usuario ${nombreUsuario}? (Se puede revertir)"); then
					ejecutarcomando "usermod --unlock ${nombreusuario}";;
				else
					echo "No se hizo ningun cambio."
					echo "[Presione Enter]"
					read
				fi;;
			0) salirModificarUsuario=1 ;;	
			*) ;;
		esac
		clear
	done
}

eliminarUsuario(){
	ingreseInput 'usuario a ELIMINAR' 'confirmar' 'obligatorio'
	nombreUsuario="${returnv}"
	if $(ingresarSiNo "Esta completamente seguro de eliminar al usuario ${nombreUsuario}?"); then
		ejecutarComando "userdel ${nombreUsuario}"	
	else
		echo "No se hizo ningun cambio."
		echo "[Presione Enter]"
		read
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
			3) modificarUsuario ;;
			4) eliminarUsuario ;;
			0) salirMenuUsuarios=1 ;;
			*) ;;
		esac
		clear
	done
}

# Funciones de grupos
groupaddEjecutar(){
	e_gidGrupo="${1}"
	e_argumentousuarioInicialesGrupo="${2}"
	e_nombreGrupo="${3}"
	cmd="groupadd "
	cmd+="--gid ${e_gidGrupo}"
	cmd+="${e_argumentousuarioInicialesGrupo}"
	cmd+="${e_nombreGrupo}"
	if ! error=$(${cmd} 2>&1); then
		echo "Hubo un error inesperado creando el grupo. No se creo ningun grupo." 
		echo "Codigo de error de groupadd: $?"
		echo "Mensaje de error de groupadd: ${error}"
		echo "Comando ejecutado:"
		echo "${cmd}"
		echo
		read -p "[Presiona Enter]" poop
	else
		echo "Grupo creado correctamente."
	fi
}

anadirGrupo(){
	ingresarInput 'gid del grupo' 'confirmar' 'obligatorio'	
	gidGrupo="${returnv}"
	ingresarInput 'nombre del grupo' 'confirmar' 'obligatorio'	
	nombreGrupo="${returnv}"
	ingresarInput 'usuarios iniciales del grupo' 'confirmar' 'VACIO'	
	usuariosInicialesGrupo="${returnv}"
	if [[ "${usuariosInicialesGrupo}" == "VACIO" ]]; then
		argumentousuariosInicialesGrupo=""
	else
		argumentousuariosInicialesGrupo="--users ${usuariosInicialesGrupo}"
	fi
	groupaddEjecutar "${gidGrupo}" "${usuariosInicialesGrupo}" "${argumentousuariosInicialesGrupo}"
}

eliminarGrupo(){
	ingresarInput 'gid/nombre del grupo a eliminar' 'confirmar' 'obligatorio'
	cmd='groupdel ${returnv}'
	if ! error=$({cmd} 2>&1); then
		echo "Hubo un error inesperado eliminando el grupo. No se elimino ningun grupo." 
		echo "Codigo de error de groupdel: $?"
		echo "Mensaje de error de groupdel: ${error}"
		echo "Comando ejecutado:"
		echo "${cmd}"
		read -p "[Presiona Enter]" poop
	else
		echo "Grupo eliminado correctamente."
	fi
}

administrarGrupos(){
	salirMenuGrupos=0
	while [[ ${salirMenuGrupos} == 0 ]]; do
		imprimirMenu "${MENUGRUPOS}"
		read -p "=> " input
		case "${input}" in 
			1) imprimirLista '/etc/group' ;;
			2) anadirGrupo ;;
			3) eliminarGrupo ;;
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
			3) if [ -z "${EDITOR}" ]; then
				ingresarInput 'editor de terminal a usar' 'confirmar' 'vi'
				EDITOR="${returnv}"
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

