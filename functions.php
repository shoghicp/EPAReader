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

function parsetemplate ($template, $array){
	return preg_replace('#\{([a-z0-9\-_]*?)\}#Ssie', '( ( isset($array[\'\1\']) ) ? $array[\'\1\'] : \'\' );', $template);
}

function xml_escape($str){
	return htmlentities($str,ENT_QUOTES);
}

function cleanPath($string){
	return str_replace('.xml', '', $string);
}

function xml_attribute($object, $attribute){
	if(isset($object[$attribute]))
		return (string) $object[$attribute];
}
function find_files($path, $pattern, $callback){
	$path = rtrim(str_replace("\\", "/", $path), '/') . '/*';
	foreach(glob($path) as $fullname) {
		if(is_dir($fullname)) {
			find_files($fullname, $pattern, $callback);
		}elseif(preg_match($pattern, $fullname)) {
			call_user_func($callback, $fullname);
		}
	}
}

function loadAdventure($save){
	$lines = explode("\r\n",$save);
	$ret = array();
	foreach($lines as $num => $line){
		switch($num){
			case 1:
				$ret['book'] = base64_decode($line);
				break;
			case 2:
				$ret['score'] = $line;
				break;
			case 3:
				$ret['page'] = $line;
				break;
			case 4:
				$ret['flags'] = array();
				$flags = explode(";",$line);
				foreach($flags as $flag){
					if($flag != ""){
						$a = explode(",",$flag);
						$ret['flags'][$a[0]] = $a[1];
					}
				}
				break;
			default:
				continue;
				break;
		
		}
	}
	return $ret;
}

function saveAdventure($book,$score,$flags,$page){
	$saveFile = "EPA_SAVEFILE_". EPA_VERSION ."\r\n";
	$saveFile .= base64_encode($book)."\r\n"; //base64 titulo+edicion
	$saveFile .= $score."\r\n"; //puntuaje
	$saveFile .= $page."\r\n"; //pagina actual
	foreach($flags as $obj => $val){
		if($obj != ""){
			$saveFile .= $obj.":".(($val==true) ? "1":"0").";"; // flag:valor;
		}
	}
	$saveFile .= "\r\n";
	return $saveFile;
}

function zip($source, $destination){
	if(file_exists($source) === true){
		$zip = new ZipArchive();
		if($zip->open($destination, ZIPARCHIVE::CREATE) === true){
			$source = realpath($source);
			if(is_dir($source) === true){
				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

				foreach ($files as $file){
					$file = realpath($file);

					if(is_dir($file) === true){
						$zip->addEmptyDir(str_replace(array($source.'\\',$source.'/'), '', $file . '/'));
					}elseif(is_file($file) === true){
						$zip->addFromString(str_replace(array($source.'\\',$source.'/'), '', $file), file_get_contents($file));
					}
				}
			}elseif(is_file($source) === true){
				$zip->addFromString(basename($source), file_get_contents($source));
			}
		}
		return $zip->close();
	}
	return false;
}

?>
