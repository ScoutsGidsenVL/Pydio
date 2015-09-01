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
 *
 * Core extensions and icons supported. You can add a line in this file to support
 * more extensions.
 * Array is ("extension", "icon_name", "key of the message in the i18n file")

 */
defined('AJXP_EXEC') or die( 'Access not allowed');

$RESERVED_EXTENSIONS = [
    "folder"    => ["ajxp_folder", "folder.png", 8],
    "unkown"    => ["ajxp_empty", "mime_empty.png", 23]
];

$EXTENSIONS = [
    // ALL FILES TYPES
    ["mid", "midi.png", 9],
    ["txt", "txt2.png", 10],
    ["sql","txt2.png", 10],
    ["js","javascript.png", 11],
    ["gif","image.png", 12],
    ["jpg","image.png", 13],
    ["html","html.png", 14],
    ["htm","html.png", 15],
    ["rar","archive.png", 60],
    ["gz","zip.png", 61],
    ["tgz","archive.png", 61],
    ["z","archive.png", 61],
    ["ra","video.png", 16],
    ["ram","video.png", 17],
    ["rm","video.png", 17],
    ["pl","source_pl.png", 18],
    ["zip","zip.png", 19],
    ["wav","sound.png", 20],
    ["php","php.png", 21],
    ["php3","php.png", 22],
    ["phtml","php.png", 22],
    ["exe","exe.png", 50],
    ["bmp","image.png", 56],
    ["png","image.png", 57],
    ["css","css.png", 58],
    ["mp3","sound.png", 59],
    ["m4a","sound.png", 59],
    ["aac","sound.png", 59],
    ["xls","spreadsheet.png", 64],
    ["xlsx","spreadsheet.png", 64],
    ["xlt","spreadsheet.png", 64],
    ["xltx","spreadsheet.png", 64],
    ["ods","spreadsheet.png", 64],
    ["sxc","spreadsheet.png", 64],
    ["csv","spreadsheet.png", 64],
    ["tsv","spreadsheet.png", 64],
    ["doc","word.png", 65],
    ["docx","word.png", 65],
    ["dot","word.png", 65],
    ["dotx","word.png", 65],
    ["odt","word.png", 65],
    ["swx","word.png", 65],
    ["rtf","word.png", 65],
    ["ppt","presentation.png", 446],
    ["pps","presentation.png", 446],
    ["odp","presentation.png", 446],
    ["sxi","presentation.png", 446],
    ["pdf","pdf.png", 79],
    ["mov","video.png", 80],
    ["avi","video.png", 81],
    ["mpg","video.png", 82],
    ["mpeg","video.png", 83],
    ["mp4","video.png", 83],
    ["m4v","video.png", 83],
    ["ogv","video.png", "Video"],
    ["webm","video.png", "Video"],
    ["wmv","video.png", 81],
    ["swf","flash.png", 91],
    ["flv","flash.png", 91],
    ["tiff","image.png", "TIFF"],
    ["tif","image.png", "TIFF"],
    ["svg","image.png", "SVG"],
    ["psd","image.png", "Photoshop"],
    ["ers","horo.png", "Timestamp"],
    ["dwg","dwg.png", "DWG"],
];
