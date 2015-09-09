<?php
/*
 *      vocabularyservices.php
 *      
 *      Copyright 2009 diego ferreyra <tematres@r020.com.ar>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */




/*
Funciones generales de parseo XML / General function for XML parser
*/

function xml2arraySimple($str) {
	$xml = simplexml_load_string($str);
	return simplexml2array($xml);
}


function simplexml2array($xml) {

if(is_object($xml))
{

	if (get_class($xml) == 'SimpleXMLElement') {
		$attributes = $xml->attributes();
		foreach($attributes as $k=>$v) {
			if ($v) $a[$k] = (string) $v;
		}
		$x = $xml;
		$xml = get_object_vars($xml);
	}
}	
	if (is_array($xml)) {
		if (count($xml) == 0) return (string) $x; // for CDATA
		foreach($xml as $key=>$value) {
			$r[$key] = simplexml2array($value);
		}
		if (isset($a)) $r['@'] = $a;// Attributes
		return $r;
	}
	return (string) $xml;
}




/*
Funciones de consulta de datos
*/


/*
Hacer una consulta y devolver un array
* $uri = url de servicios tematres
* +    & task = consulta a realizar
* +    & arg = argumentos de la consulta
*/
function xmlVocabulary2array($tematres_uri,$task,$arg){
	
	$url=$tematres_uri.'?task='.$task.'&arg='.$arg;
	
	$xml=file_get_contents($url) or die ("Could not open a feed called: " . $url);
	
	return xml2arraySimple($xml);
	}







/*
Recibe un array y lo publica como HTML
*/
function arrayVocabulary2htmlSearch($tematres_uri,$array,$tag_type="ul"){

	GLOBAL $message	;

	$rows='<h3>'.ucfirst($message["searchExpresion"]).' <i>'.$array["resume"]["param"]["arg"].'</i>'.' ('.$array["resume"]["cant_result"].')</h3>';
	
	if($array["resume"]["cant_result"]>"0")	{
		
	$rows.='<'.$tag_type.' class="search_result">';
	
	$i=0;
	foreach ($array["result"] as $key => $value){
				while (list( $k, $v ) = each( $value )){
					$i=++$i;
					//Controlar que no sea un resultado unico
					if(is_array($v)){
						$rows.='<li>';
						$rows.= ($v["no_term_string"]) ? '<em title="'.$message['UF'].'  '.$message['USE'].' '.FixEncoding($v["string"]).'">'.FixEncoding($v["no_term_string"]).'</em> '.$message['USE'].' ' : '';
						$rows.='<a href="index.php?tema_id='.$v["term_id"].'#t3" title="'.FixEncoding($v["string"]).'">'.FixEncoding($v["string"]).'</a>';
						$rows.='</li>';
			
						} else {

							//controlar que sea la ultima
							if(count($value)==$i){
								$rows.='<li>';
								$rows.= ($value["no_term_string"]) ? '<em title="'.$message["UF"].' '.$message["USE"].' '.FixEncoding($value["string"]).'">'.FixEncoding($value["no_term_string"]).'</em> '.$message['USE'].' ' : '';
								$rows.='<a href="index.php?tema_id='.$value["term_id"].'#t3" title="'.FixEncoding($value["string"]).'">'.FixEncoding($value["string"]).'</a>';
								$rows.='</li>';
								}
						}
					}

		}		
	$rows.='</'.$tag_type.'>';
	}
	else 
	{
	
	$arrayTerm=xmlVocabulary2array($tematres_uri,"fetchSimilar",urlencode($array["resume"]["param"]["arg"]));

	if (count($arrayTerm))
		{
		$rows.='<h4>'.ucfirst($message['suggestedSearchTerm']).' <a href="index.php?search_keyword='.FixEncoding($arrayTerm["result"]["string"]).'#t3" title="'.FixEncoding($arrayTerm["result"]["string"]).'">'.FixEncoding($arrayTerm["result"]["string"]).'</a>?</h4>';			
		}

	}

return $rows;
	}





function arrayVocabulary2JSON_JIT($array,$tag_type,$tema_id="0",$show_link="1"){
	
	GLOBAL $message;

	if($array["resume"]["cant_result"]>"0")	{

	$i=0;	
	$rows=null;
	foreach ($array["result"] as $key => $value){
				while (list( $k, $v ) = each( $value )){
					$i=++$i;
					//Controlar que no sea un resultado unico
					if(is_array($v)){
						if($v["term_id"]!==$tema_id)
							{
									$rows.='{';
									$rows.=JeachNode($v);
									$rows.='"children": []';
									$rows.='},';
							}
			
						} else {

							//controlar que sea la ultima
							if(count($value)==$i){
								//Que sea el mismo tema_id 
								if($value["term_id"]!==$tema_id)
									{			
									$rows.='{';					
									$rows.=JeachNode($value);
									$rows.='"children": []';
									$rows.='},';
									}
								}
						}
					}

		}		

	}

return $rows;
}


function arrayVocabulary2JHieraquical($array,$tema_id){
	
	GLOBAL $message;

	if($array["resume"]["cant_result"]>"0")	{

	$i=0;	
	$rows=null;
	foreach ($array["result"] as $key => $value){
				while (list( $k, $v ) = each( $value )){
					$i=++$i;
					if($i==($array["resume"]["cant_result"]-1))
					{
					
					//Controlar que no sea un resultado unico
					if(is_array($v)){
						if($v["term_id"]!==$tema_id)
							{
									$BT_term_id=($v["term_id"]);
									
									$rows.='{';
									$rows.=JeachNode($v);
									$rows.='"children": []';
									$rows.='},';
							}
			
						} else {

							//controlar que sea la ultima
							if(count($value)==$i){
								//Que sea el mismo tema_id 
								if($value["term_id"]!==$tema_id)
									{
									$BT_term_id=($value["term_id"]);
									$rows.='{';					
									$rows.=JeachNode($value);
									$rows.='"children": []';
									$rows.='},';
									}
								}
						}
					}
				}//Cierre del if='1'

		}		

	}

return array(	"rows"=>$rows,
				"BT_term_id"=>$BT_term_id);
}


function JeachNode($array)
{

	$rows='"id":"'.$array["term_id"].'",';
	$rows.='"name":"'.FixEncoding($array["string"]).'",';
	$rows.='"data": {';
	//$rows.='   "Term": "'.$array[string].'",';
	$rows.='   "Term": " <a href=\"index.php?tema_id='.$array["term_id"].'\">'.$array["string"].'</a>",';
	$rows.='   "relation": " <a href=\"index.php?tema_id='.$array["term_id"].'\">'.$array["string"].'</a> ",';
	$rows.='         "cantTerms": "",';
	$rows.='},';

return $rows;
}



/*
From http://ar2.php.net/utf8_encode
*/
function FixEncoding($x){
  if(mb_detect_encoding($x)=='UTF-8'){
    return $x;
  }else{
    return utf8_encode($x);
  }
} 
?>
