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

$id = addslashes(str_replace('.', '', $_GET['book']));
if(!isset($_GET['n'])){ $_GET['n'] = ""; }
$page = addslashes(str_replace('.', '', $_GET['n']));
$infoXml = simplexml_load_file("books/".$id."/info.xml");
if($page != ""){
	if(!is_array($_SESSION[$id])){
		$_SESSION[$id] = array('score' => 0, 'flags' => array());
	}
	if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPod')){ 
		echo parsetemplate($template['header'], array('meta' => '--><meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/><script type="text/javascript">window.addEventListener("load", function(){setTimeout(scrollTo, 0, 0, 1);}, false);$(document).ready(function(){$("#header").remove();$("#margin").remove();$(".bookpage").height(400);$(".bookpage").width(320);$(".bookpage").css("border", "none");$(".bookpage").css("left", -35);});</script><!--'));
		$ajax = false;
	}else{
		echo $template['header'].'<div class="title">'.$infoXml->title[0].'</div><br/>';
		$ajax = true;
	}
	if($page == "" or $page < 0 ){
		$page = 1;
	}
	$pageXml = simplexml_load_file("books/".$id."/".$page.".xml");
	if($pageXml->score[0] != 0){
		$_SESSION[$id]['score'] += $pageXml->score[0];
	}
	if($pageXml->bgimage[0] != ""){
		$background = 'url(books/'.$id.'/'.$pageXml->bgimage[0].') no-repeat';
	}elseif($pageXml->bgcolor[0] != ""){
		$background = $pageXml->bgcolor[0];
	}else{
		$background = 'black';
	}
	echo '<div class="bookpage" style="background:'.$background.';">';
	echo '<div class="text"><span style="font-size:13px;font-weight:bold;">'.$pageXml->title[0].'</span><br>'.$pageXml->text[0].'<br><span style="font-size:10px;">'.$pageXml->pretext .'</span>';
	if($pageXml->flags){
		foreach($pageXml->flags->flag as $flag){
			$_SESSION[$id]['flags'][xml_attribute($flag, 'name')] = xml_attribute($flag, 'value'); 		
		}
	}
	if($pageXml->options){
		foreach($pageXml->options->option as $option){
			if($ajax===true){
				$link = "<div style=color:white; onclick=\"$('div#content').load('index.php?page=read&book=".$id."&n=".cleanPath($option['target'])."&ajax=1');\" class=option >".$option."</div>";
			}else{
				$link = "<div style=color:white; onclick=\"go('index.php?page=read&book=".$id."&n=".cleanPath($option['target'])."');\" class=option >".$option."</div>";
			}			
			if(isset($option['true'])){
				$optione = explode(",",$option['true']);
				foreach($optione as $option2){
					if($_SESSION[$id]['flags'][$option2] != true and $option2 != ""){
						continue;
					}
				}
			}
			if(isset($option['false'])){
				$optione = explode(",",$option['false']);
				foreach($optione as $option2){
					if($_SESSION[$id]['flags'][$option2] != false and $option2 != ""){
						continue;
					}
				}
			}
			echo $link;
		}
	}
	echo '</div>';
	if($pageXml->continue[0] != ""){
	
		if($ajax===true){
			echo "<a style=color:white; onclick=\"$('div#content').load($(this).attr('href') + '&ajax=1');return false;\" href='index.php?page=read&book=".$id."&n=".cleanPath($pageXml->continue[0]['target'])."' class=button >".$pageXml->continue[0]."</a>";
		}else{
			echo "<a style=color:white; href='index.php?page=read&book=".$id."&n=".cleanPath($pageXml->continue[0]['target'])."' class=button >".$pageXml->continue[0]."</a>";
		}
	}
	if(isset($pageXml->final[0])){
		echo '<div class=final >Final del libro.<br/>Tu puntacion es <b>'.$_SESSION[$id]['score'].'</b><br/><a href="index.php" style="color:white;">Inicio</a></div>'; 
		$_SESSION[$id] = array('score' => 0, 'flags' => array());
	}
	echo '</div><br/><div><a href="index.php?page=edit&book='.$id.'&n='.$page.'" class="button">Editar</a></div>'.$template['footer'];

}else{
	$_SESSION[$id] = array('score' => 0, 'flags' => array());
	echo $template['header'].'<div class=title >'.$infoXml->title[0].'</div><br/>Pulsa en el libro para empezar a leerlo.<br/>';
				if($infoXml->image[0] != ""){
					$img = "<img src='books/".$id."/".$infoXml->image[0]."' width='196'/>";
				}else{
					$img = '';
				}
				$contacts = "";
				if(isset($infoXml->contacts)){
					$list = "";
					foreach($infoXml->contacts->contact as $contact){
						switch($contact["type"]){
							case "twitter":
								$user = str_replace(array("@","twitter.com","www.","https","http","://","/","#!"),"",$contact);
								$list .= "<br/>&nbsp;&nbsp;<a target=\"_blank\" style=\"color:white;text-decoration:none;font-size:12px;\" href=\"http://twitter.com/".$user."\">@".$user."</a>";
								break;
							case "email":
								$list .= "<br/>&nbsp;&nbsp;<a target=\"_blank\" style=\"color:white;text-decoration:none;font-size:12px;\" href=\"mailto:".$contact."\">".$contact."</a>";							
								break;
							default:
								$list .= "<br/>".$contact;
								break;
						}
					}
					$contacts .= "<br/><br/><span style=\"text-decoration:underline;font-weight:bold;\">Contacto</span>".$list;
				}
				if(isset($infoXml->url)){
					$contacts .= "<br/>URL: <a target=\"_blank\" href=\"".$infoXml->url."\" style=\"color:white;\">".$infoXml->url ."</a>";
				}
				echo '<a href="index.php?page=read&book='.$id.'&n='.str_replace('.xml', '', $infoXml->init[0]) .'" style="text-decoration:none;"><div class="bookg"><div style="background:'.$infoXml->cover[0].';" class=bgcover id=bgcover'.$id.' >&nbsp;</div><div class=main >'.$infoXml->title[0].'</div><div class=sub >'.$infoXml->subtitle[0].'</div><div class=bimg >'.$img.'</div><div class=auth >- '.$infoXml->author[0].' -</div><div class=edition >'.$infoXml->edition[0].'</div></div></a></li>';
				echo '<br/><div style="width:600px;">'.addslashes($infoXml->description[0]).$contacts.'<br/><br/><a href="index.php?page=download&book='.$id.'" class="button">Descargar</a>&nbsp;&nbsp;<a href="index.php?page=edit&book='.$id.'" class="button">Editar</a>&nbsp;&nbsp;<a href="index.php?page=delete&book='.$id.'" onclick="return confirm(\'Esta seguro?\');" class="button">Borrar</a></div></div><br/>';
				echo '<script type="text/javascript">
					  $(document).ready(function(){
					   $(".bgcover").fadeTo(0, 0);
						$(".bgcover").fadeTo(1000, 0.4);
					  });
					</script>';
	echo $template['footer'];
}

?>
