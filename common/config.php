<?php
#   TemaTres VisualVocabulary : Web aplication to create visual representations based on controlled vocabularies #
#                                                                        #
#   Copyright (C) 2009-2010 Diego Ferreyra tematres@r020.com.ar
#   Distribuido bajo Licencia GNU Public License, versión 2 (de junio de 1.991) Free Software Foundation
#   Este es el archivo LEAME.TXT
###############################################################################################################

/*
TemaTres URI source
*/
//$CFG_tematres_uri = "http://xxxx.yourserver.com/tematres/services.php";

$CFG_tematres_uri = "http://www.vocabularyserver.com/scot/services.php";



/*
Visual vocabulary language
* See languages in ../lang
*/
$CFG_lang="en_US";



$xml=file_get_contents($CFG_tematres_uri.'?task=fetchVocabularyData') or die ("Could not open a feed called: " . $CFG_tematres_uri);
$arrayVocabulary=xml2arraySimple($xml);

?>
