<?php
/*
* Copyright 2007-2013 Charles du Jeu - Abstrium SAS <team (at) pyd.io>
* This file is part of Pydio.
*
* Pydio is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Pydio is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with Pydio.  If not, see <http://www.gnu.org/licenses/>.
*
* The latest code can be found at <http://pyd.io/>.
*/
// catalan translation: Salva Gómez <salva.gomez at gmail.com>, 2015
$mess=array(
"Generic Conf Features" => "Configuracions genèriques",
"Let user create repositories" => "Permetre a l'usuari crear repositoris",
"Remember guest preferences" => "Recordar preferències de convidat",
"If the 'guest' user is enabled, remember her preferences accross sessions." => "Si l'usuari 'Convidat' està habilitat, recordar les seves preferències a través de sessions.",
"Configurations Management" => "Gestió de configuracions",
"Sets how the application core data (users,roles,etc) is stored." => "Estableix com s'emmagatzemen les dades bàsiques d'aplicació (usuaris, rols, etc.)",
"Default start repository" => "Repositori d'inici per defecte",
"Default repository" => "Repositori per defecte",
"Maximum number of shared users per user" => "Nombre màxim d'usuaris compartits per usuari",
"Shared users limit" => "Límit d'usuaris compartits",
"Core SQL Connexion" => "Conexió SQL Principal",
"SQL Connexion" => "Connexió SQL",
"Simple SQL Connexion definition that can be used by other sql-based plugins" => "Definició de connexió SQL simple que pot ser utilitzada per altres plugins basats en SQL",
"Preferences Saving" => "Preferències de desament",
"Skip user history" => "Treure preferències d'interfície",
"Use this option to avoid automatic reloading of the interface state (last folder, opened tabs, etc)" => "Utilitzeu aquesta opció per evitar la càrrega automàtica de l'estat de la interfície (darrera carpeta, pestanyes obertes, etc)",
"Internal / External Users" => "Usuaris interns / externs",
"Maximum number of users displayed in the users autocompleter" => "Nombre màxim d'usuaris mostrats a la llista auto emplenable d'usuaris",
"Users completer limit" => "Límit del emplenador d'usuaris",
"Minimum number of characters to trigger the auto completion feature" => "Nombre mínim de caràcters per activar la funció d'autocompletar",
"Users completer min chars" => "Nombre mínim de caràcters del completador d'usuaris",
"Do not display real login in parenthesis" => "No mostrar el nom d'inici de sessió real entre parèntesis",
"Hide real login" => "Amaga nom d'inici de sessió real",
"See existing users" => "Veure usuaris existents",
"Allow the users to pick an existing user when sharing a folder" => "Permetre als usuaris triar un usuari existent en compartir una carpeta",
"Create external users" => "Crear usuaris externs",
"Allow the users to create a new user when sharing a folder" => "Permetre als usuaris crear un nou usuari en compartir una carpeta",
"External users parameters" => "Paràmetres d'usuaris externs",
"List of parameters to be edited when creating a new shared user." => "Llista de paràmetres a editar en crear un nou usuari compartit.",
"Configuration Store Instance" => "Instància d'emmagatzematge de la configuració",
"Instance" => "Instància",
"Choose the configuration plugin" => "Seleccionar el connector de configuració",
"Name" => "Nom",
"Full name displayed to others" => "Mostrar el nom complet als altres",
"Avatar" => "Avatar",
"Image displayed next to the user name" => "Imatge mostrada al costat del nom d'usuari",
"Email" => "Correu electrònic",
"Address used for notifications" => "Adreça utilitzada per a les notificacions",
"Country" => "País",
"Language" => "Idioma",
"User Language" => "Idioma de l'usuari",
"Role Label" => "Etiqueta de rol",
"Users Lock Action" => "Acció de bloqueig d'usuaris",
"If set, this action will be triggered automatically at users login. Can be logout (to lock out the users), pass_change (to force password change), or anything else" => "Si s'estableix, aquesta acció s'activa automàticament en l'inici de sessió dels usuaris. Pot ser tancar la sessió (per bloquejar als usuaris), canviar contrasenya (per forçar el canvi de contrasenya), o qualsevol altra cosa",
"Worskpace creation delegation" => "Delegació de la creació de repositoris",
"Let user create repositories from templates" => "Permetre a l'usuari crear repositoris usant plantilles",
"Whether users can create their own repositories, based on predefined templates." => "Si els usuaris poden crear els seus propis repositoris, fer-ho basant-se en plantilles predefinides.",
"Users Directory Listing" => "Llistat del directori d'usuaris",
"Share with existing users from all groups" => "Compartir amb els usuaris existents de tots els grups",
"Allow to search users from other groups through auto completer (can be handy if previous option is set to false) and share workspaces with them" => "Permetre cercar usuaris d'altres grups a través d'autocompletar (pot ser útil si l'opció anterior s'estableix en False) i compartir repositoris amb ells",
"List existing from all groups" => "Llistar existents de tots els grups",
"If previous option is set to True, directly display a full list of users from all groups" => "Si l'opció anterior s'estableix en True, mostrar directament una llista completa dels usuaris de tots els grups",
"Roles / Groups Directory Listing" => "Llistat de rols / directori de grups",
"Display roles and/or groups" => "Mostra rols i / o grups",
"Users only (do not list groups nor roles)" => "Només usuaris (no llistar grups i rols)",
"Allow Group Listing" => "Permetre el llistat de grups",
"Allow Role Listing" => "Permetre el llistat de rols",
"Role/Group Listing" => "Llistat de rols / grups",
"List Roles By" => "Listar rols per",
"All roles" => "Tots els rols",
"User roles only" => "Només rols d'usuari",
"role prefix" => "Prefix del rols",
"Excluded Roles" => "Rols exclosos",
"Included Roles" => "Rols inclosos",
"Some roles should be disappered in the list.  list separated by ',' or start with 'preg:' for regex." => "Rols ocults a la llista. Llista separada per ',' o començar amb 'preg:' per expressions regulars regex.",
"Some roles should be shown in the list.  list separated by ',' or start with 'preg:' for regex." => "Rols que han de mostrar-se en la llista. Llista separada per ',' o començar amb 'preg:' per expressions regulars regex.",
"External Users Creation" => "Creació d'usuaris externs",
);