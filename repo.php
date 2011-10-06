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

include_once("config.php");

if(defined("REPO") and REPO == true){
	header('Content-type: text/plain');
	$itemHandler = opendir("./books/");
	while(($item=readdir($itemHandler)) !== false){
	if($item == '..' or $item == '../' or $item == '.'){continue;}
		$id = $item;
		$item = "./books/".$item;
		if(is_dir($item)){
			$xml = simplexml_load_file($item."/info.xml");
			$genre = "VAR";
			if($xml->genre){
				$genre = $xml->genre;
			}
			$author = explode("\n",$xml->author);
			$title = explode("\n",$xml->title);
			echo EPAREADER_URL ."index.php?page=download&book=".$id."|".utf8_decode(trim($author[0]))."|".utf8_decode(trim($title[0]))."|".$genre."\n";
		}
	}
}
die();
?>