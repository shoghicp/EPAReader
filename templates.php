<?php
/*
	 _____ ____   _    ____ 			   _		   
	| ____|  _ \ / \  |  _ \ ___  __ _	__| | ___ _ __ 
	|  _| | |_) / _ \ | |_) / _ \/ _` |/ _` |/ _ \ '__|
	| |___|  __/ ___ \|  _ <  __/ (_| | (_| |  __/ |   
	|_____|_| /_/	\_\_| \_\___|\__,_|\__,_|\___|_|   


	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Lesser General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

 *
 * @author shoghicp@gmail.com
 *

*/

$template = array('header' => "", 'footer' => '');

if(!isset($_GET['ajax']) || $_GET['ajax'] == 0){
$template['header'] = <<<HEADER
<html>
<head>
<title>EPAReader &bull; Lector de libros "Elige tu propia aventura"</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="functions.js"></script>
<!-- {meta} -->
</head>
<body>
<div id="header">
	<div id="text-left">EPAReader</div>
	<div id="menu">
		<a href="index.php">Inicio</a>
		  
		<a href="index.php?page=new">Nuevo</a>
		  
		<a href="index.php?page=read">Leer</a>
	</div>
</div>
<div id="margin" style="height:85px;"> </div>
<div id="content">
HEADER;

$template['footer'] = <<<FOOTER
</div>
</body>
</html>
FOOTER;

}else{
	$template['header'] = "";
	$template['footer'] = "";
}

$template['index'] = <<<INDEX
<div class="title">Inicio</div>
<br/>
<input type="button" class="button big" style="color:white;" onclick="go('index.php?page=new');" value="Nuevo"/>
	
<input type="button" class="button big" style="color:white;" onclick="go('index.php?page=read');" value="Leer"/>
INDEX;

$template['read_select'] = <<<READ
<div class="title">Seleccionar libro a leer</div>
<br/>
La libreria tiene {count} libros.
<br/>
<ul style="list-style-type: none;">
{booklist}
</ul>
<script type="text/javascript">
  $(document).ready(function(){
   $(".bgcover").fadeTo(0, 0);
	$(".bgcover").fadeTo(1000, 0.4);
  });
</script>
READ;

$template['new'] = <<<NEW
<div class="title">Enviar nuevo Libro EPA</div>
<br/>
<form action="index.php?page=upload" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
Libro: <input name="book_file" type="file"/>
<br><span style="font-size:10px;"> (extensiones aceptadas: <i>.epa, .zip</i>)</span>
<br/>
<br/>
<input type="submit" value="Enviar" style="color:white;" class="button"/>
</form>
<br/>
<br/>
<div class="title">Descargar desde un repositorio</div>
<br/>
<form action="index.php?page=repo" method="post">
Repositorio: <select name="repo">
<option value="http://www.emezeta.com/EPA/list.txt" selected>Emezeta</option>
</select>
<br/><br/>
<input type="submit" value="Recibir" style="color:white;" class="button"/>
</form>
<br/>
<br/>
<div class="title">Crear nuevo Libro EPA</div>
<br/>
<form action="index.php?page=create" method="post"enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
<table>
<tr>
<th style="text-decoration:underline;">Obligatorio</th><td></td></tr><tr>
<td>Titulo</td><td><input name="book_title" type="text"/></td>
</tr><tr>
<td>Autor</td><td><input name="book_author" type="text"/></td>
</tr><tr>
<td>Descripcion</td><td><textarea name="book_description"></textarea></td>
</tr><tr>
<td>Edicion</td><td><input name="book_edition" type="text"/></td>
</tr><tr>
<td>EPA Ver</td><td>$EPA_VERSION</td>
</tr><td></td></tr><tr>
<th style="text-decoration:underline;">Opcional</th><td></td></tr><tr>
<td>Portada</td><td><select name="book_cover"><option value="blue" selected style="color:blue;">Azul</option><option value="red" style="color:red;">Rojo</option><option value="green" style="color:green;">Verde</option><option value="yellow" style="color:yellow;">Amarillo</option></td>
</tr><tr>
<td>Subtitulo</td><td><input name="book_subtitle" type="text"/></td>
</tr><tr><td>Imagen</td><td><input name="book_image" type="file"/></td></tr><tr>
<td colspan="2"><span style="font-size:10px;"> (extensiones aceptadas: <i>.jpg, .png</i>)</span></td></tr><tr>
</table>
<br/>
<input type="submit" value="Crear" style="color:white;" class="button"/>
</form>
NEW;

?>
