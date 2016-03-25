<?php
/*
* Copyright 2007-2014 Charles du Jeu - Abstrium SAS <team (at) pyd.io>
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

/* *****************************************************************************
* Initial translation:  Martin Schaible <martin@martinschaible.ch>
***************************************************************************** */ 

/* Do not use HTML entities! It would mess up everything */ 
$mess=array(
"Client Plugin" => "Desktop-Client",
"Browser-based rich interface. Contains configurations for theming, custom welcome message, etc." => "Browser-basierte Oberfläche. Beinhaltet die Konfiguraton der Themen, kundenspezifische Willkommens-Mitteilung, usw.",
"Main Options" => "Grundeinstellungen",
"Theme" => "Thema",
"Theme used for display" => "Thema für die Anzeige der Oberfläche",
"Start Up Screen" => "Startbildischirm",
"Title Font Size" => "Schriftgrösse des Titels",
"Font sized used for the title in the start up screen" => "Schriftgrösse des Titels auf der Anmelde-Seite",
"Custom Icon" => "Logo",
"URI to a custom image to be used as start up logo" => "Logo auf der die Anmelde-Seite",
"Icon Width" => "Breite des Bildes",
"Width of the custom image (by default 35px)" => "Breite des Bildes (Standard 35px)",
"Welcome Message" => "Willkommens-Mitteilung",
"An additionnal message displayed in the start up screen" => "Eine zusätzliche Mitteilung für die Anmelde-Seite",
"Client Session Config" => "Konfiguration der Client-Sitzung",
"Client Timeout" => "Sitzungs-Timeout",
"The length of the client session in SECONDS. By default, it's copying the server session length. In most PHP installation, it will be 1440, ie 24minutes. You can set this value to 0, this will make the client session 'infinite' by pinging the server at regular occasions (thus keeping the PHP session alive). This is not a recommanded setting for evident security reasons." => "Die Länge der Sitzung in Sekunden. Standardmässig wird die Konfiguration des Servers genutzt, welche bei den meisten PHP-installationen 1440 Sekunden (24 Minuten) beträgt. Sie können den Wert auf 0 setzen, was eine unendlich lange Client-Sitzung ergibt. In diesem Fall wird der Server regelmässig angepingt und die PHP-Sitzung bleibt bestehen). Diese Einstellung ist aus Sicherheitsgründen nicht empfehlenswert.",
"Warning Before" => "Warnung vor Sitzungs-Timeout",
"Number of MINUTES before the session expiration for issuing an alert to the user" => "Den Benutzer warnen bevor die Sitzung abläuft. (Anzahl an Minuten vor dem Ablauf der Sitzung)",
"Google Analytics" => "Google Analytics",
"Analytics ID" => "Analytics-ID",
"Id of your GA account, something like UE-XXXX-YY" => "ID Ihres GA-Kontos (Beispiel: UE-XXXX-YY)",
"Analytics Domain" => "Analytics-Domain",
"Set the domain for your analytics reports (not mandatory!)" => "Setzen Sie die Domain für Ihre Analytics-Berichte (Nicht zwingend!)",
"Analytics Events" => "Ereignisse protokollieren",
"Use Events Logging, experimental only implemented on download action in Pydio" => "Ereignisse protokollieren. Aktuell noch experimentell und nur für das Herunterladen von Dateien implementiert.",
"Icon Only" => "Nur mit Bild",
"Skip the title, only display an image" => "Nur das Bild wird angezeigt. Der Titel wird ausgeblendet.",
"Icon Path (Legacy)" => "Bildpfad (veraltet)",
"Icon Height" => "Höhe des Bildes",
"Height of the custom icon (with the px mention)" => "Höhe des Bildes (px als Einheit angeben)",
"Top Toolbar" => "Obere Werkzeugleiste",
"Title" => "Titel",
"Append a title to the image logo" => "Ein Titel für das Logo hinzufügen",
"Logo" => "Logo",
"Replace the top left logo in the top toolbar" => "Logo oben links in der Werkzeugleiste",
"Logo Height" => "Höhe des Logos",
"Manually set a logo height" => "Die Höhe des Logos setzen",
"Logo Width" => "Breite des Logos",
"Manually set a logo width" => "Die Breite des Logos setzen",
"Logo Top" => "Abstand von oben",
"Manually set a top offset" => "Entfernung des Logos vom oberen Rand",
"Logo Left" => "Abstand von links",
"Manually set a left offset" => "Entfernung des Logos vom linken Rand",
"Additional JS frameworks" => "Zusätzliche Javascript-Frameworks",
"Additional JS frameworks description" => "Eine kommagetrennte Liste von Pfaden zu Javascript-Dateien, welche VOR dem Start des Pydio-Frameworks geladen werden müssen.",
"Login Screen" => "Anmelde-Seite",
"Welcome Page" => "Willkommen-Seite",
"Replace the logo displayed in the welcome page" => "Logo auf der Willkommen-Seite",
"Custom Background (1)" => "Hintergrundbild (1)",
"Background Attributes (1)" => "Anzeigeeigenschaften (1)",
"Custom Background (2)" => "Hintergrundbild (2)",
"Background Attributes (2)" => "Anzeigeeigenschaften (2)",
"Custom Background (3)" => "Hintergrundbild (3)",
"Background Attributes (3)" => "Anzeigeeigenschaften (3)",
"Custom Background (4)" => "Hintergrundbild (4)",
"Background Attributes (4)" => "Anzeigeeigenschaften (4)",
"Custom Background (5)" => "Hintergrundbild (5)",
"Background Attributes (5)" => "Anzeigeeigenschaften(5)",
"Custom Background (6)" => "Hintergrundbild (6)",
"Background Attributes (6)" => "Anzeigeeigenschaften (6)",
"Image used as a background" => "Bild, das im Hintergrund angezeigt wird. (z.B. auf der Anmelde-Seite)",
"Attributes of the image used as a background" => "Eigenschaften des Hintergrund-Bildes",
"Center in Page (no-repeat)" => "In Seite zentriert (Nicht-wiederholend)",
"Fetch Window (repeat vertically)" => "Ganzes Fenster (Vertikal wiederholend)",
"Fetch Window (no repeat)" => "Ganzes Fenster (Nicht-wiederholend)",
"Tile (repeat both directions)" => "Nebeneinander (In beide Richtungen wiederholend)",
"Set up some application parameters. If you enable Emails, please use the Test button to check if your php is correctly configured." => "Grundeinstellungen der Anwendung festlegen. Bei aktivem E-Mail-Versand kann die PHP-Konfiguration mit einem Klick auf 'Mailversand mit dieser Konfiguration testen' überprüft werden.",
"Detected Encoding" => "Server-Encoding",
"Detected Server Path" => "Server-Pfad",
"Application Title" => "Titel der Anwendung",
"Page Background Images" => "Hintergrundbilder",
"Minisite" => "Minisite",
"Minisite Logo" => "Logo",
"Top-right logo displayed on minisite page" => "Logo, das auf Minisites angezeigt wird",
);
