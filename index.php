<?php

/*
	 _____ ____   _    ____                _           
	| ____|  _ \ / \  |  _ \ ___  __ _  __| | ___ _ __ 
	|  _| | |_) / _ \ | |_) / _ \/ _` |/ _` |/ _ \ '__|
	| |___|  __/ ___ \|  _ <  __/ (_| | (_| |  __/ |   
	|_____|_| /_/   \_\_| \_\___|\__,_|\__,_|\___|_|   


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

session_start();
if(!is_array($_SESSION)){
	$_SESSION = array();
}
set_time_limit(30);
ignore_user_abort(0);
ini_set("expose_php", 0);
ini_set("log_errors", 0);
ini_set("magic_quotes_gpc", 0);
ini_set('default_charset', 'UTF-8');
date_default_timezone_set('Europe/Madrid');

include_once("functions.php");
include_once("templates.php");
if(!isset($_GET['page'])){ $_GET['page'] = ""; }
switch($_GET['page']){
	case "read":
		if(isset($_GET['book'])){
			include_once("read.php");
		}else{
			$books = array();
			$itemHandler = opendir("./books/");
				while(($item=readdir($itemHandler)) !== false){
					if($item == '..' or $item == '../' or $item == '.'){continue;}
					$id = $item;
					$item = "./books/".$item;
					if(is_dir($item)){
						$xml = simplexml_load_file($item."/info.xml");
					  $books[] = array('id' => $id, 'desc' => $xml->description[0], 'edition' => $xml->edition[0], 'title' => $xml->title[0], 'subtitle' => $xml->subtitle[0], 'cover' => $xml->cover[0], 'author' => $xml->author[0], 'img' => $xml->image[0]);
					}
				   }
			$array = array('booklist' => '');
			$count = 0;
			foreach($books as $arr){
				if(($count % 4) == 0 and $count > 0){ $array['booklist'] .= '</ul><div style=height:240px; >&nbsp;</div><br/><ul style="list-style-type: none;">';}
				if($arr['img'] != ""){
					$img = "<img src='books/".$arr['id']."/".$arr['img']."' width='120'/>";
				}else{
					$img = '';
				}
				$array['booklist'] .= '<li style="float: left;"><a href="index.php?page=read&book='.$arr['id'].'" title="'.addslashes($arr['desc']).'" style="text-decoration:none;"><div class="book"><div style="background:'.$arr['cover'].';" class=bgcover id=bgcover'.$arr['id'].' >&nbsp;</div><div class=main >'.$arr['title'].'</div><div class=sub >'.$arr['subtitle'].'</div><div class=bimg >'.$img.'</div><div class=auth >- '.$arr['author'].' -</div><div class=edition >'.$arr['edition'].'</div></div></a></li>';
				$count++;
			}
			$array['count'] = $count;
			echo $template['header'], parsetemplate($template['read_select'], $array), $template['footer'];
		}
		break;
	case "upload":
 		if(!$_POST){header("Location: index.php"); die(); }
		$tipo_archivo = strrchr($_FILES['book_file']['name'], '.'); 
		if (!((strpos($tipo_archivo, "epa") || strpos($tipo_archivo, "zip")) && ($_FILES['book_file']['size'] < 10000000))) { 
		   	echo $template['header'], "<div class=title>Error</div><br/>El libro que has enviado no tiene una extension correcta o es mas grande que 10MB", $template['footer']; 
		}else{ 
			$name = md5(time().mt_rand(0, 2000000) );
		   	if (move_uploaded_file($_FILES['book_file']['tmp_name'], "./uploads/".$name.".zip" ) ){ 
				 $zip = new ZipArchive;
				 $res = $zip->open("./uploads/".$name.".zip");
				 if ($res === TRUE) {
					 mkdir( "./books/".$name);
					 $zip->extractTo("./books/".$name);
					 $zip->close();
		   			echo $template['header'], "<div class=title>Hecho</div><br/>El libro ha sido guardado y descomprimido<br><br><a href=index.php?page=read&book=".$name." style=color:white class=button >Leer ahora</a> ", $template['footer'];
				 } else {
		   			echo $template['header'], "<div class=title>Error</div><br/>No se puede descomprimir el archivo", $template['footer'];
					rmdir("./books/".$name);
				 } 
				unlink("./uploads/".$name.".zip");
		   	}else{ 
		   	echo $template['header'], "<div class=title>Error</div><br/>ha ocurrido un error al guardar el archivo", $template['footer']; 
		   	} 
		} 
		break;

	default:
		echo $template['header'], $template['index'], $template['footer'];
}
die();
?>
