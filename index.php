<?php 
include_once('common/vocabularyservices.php');
include_once('common/config.php');
include_once('lang/'.$CFG_lang.'.php');

$search_keyword = isset($_GET["search_keyword"]) ? $_GET["search_keyword"] : null ;
$tema_id = isset($_GET["tema_id"]) ? $_GET["tema_id"] : null ;

if($search_keyword)
	{
		$xmlSearchTerm=file_get_contents($CFG_tematres_uri.'?task=search&arg='.urlencode($search_keyword)) or die ("Could not open a feed called: " . $CFG_tematres_uri);
		$arraySearchTerm=xml2arraySimple($xmlSearchTerm);

		$rowsSearchTerm=arrayVocabulary2htmlSearch($CFG_tematres_uri,$arraySearchTerm,"ul");
	}
	else 
	{
		$rowsSearchTerm=null;
	}	;



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo FixEncoding($arrayVocabulary["result"]["title"]);?></title>

<!-- CSS Files -->
<link type="text/css" href="css/base.css" rel="stylesheet" />
<link type="text/css" href="css/Hypertree.css" rel="stylesheet" />

<!--[if IE]><script language="javascript" type="text/javascript" src="Extras/excanvas.js"></script><![endif]-->

<!-- JIT Library File -->
<script language="javascript" type="text/javascript" src="common/jit.js"></script>

<!-- Source File -->
<script language="javascript" type="text/javascript" src="common/visualvocabulary.php?tema_id=<?php echo $tema_id;?>"></script>
</head>

<body onload="init();">

<div id="header-container">
        <h1><a href="index.php"><?php echo FixEncoding($arrayVocabulary["result"]["title"]);?></a></h1> 

</div>

<div id="container">

<div id="center-container">
    <div id="infovis"></div>    
</div>

<div id="right-container">

<div id="search_result">
 <form id="search_term" class="standard_form" name="search_term" method="GET" action="index.php">
    <h5>
     <label for="search_keyword"><span class="translatable" lang="Search">Search</span>:</label>
    </h5>
    <input autocomplete="off" id="search_keyword" class="text_input" name="search_keyword"  value="" maxlength="150" type="text">
    <input name="search" value="Search" type="submit">
 </form>
 
 <?php echo $rowsSearchTerm;?>
 
</div>		
<div id="inner-details"></div>


</div>

<div id="log"></div>
</div>
<div id="footer" class="text">
Powered by <a href="http://www.vocabularyserver.com">TemaTres Visual Vocabulary</a>.
</div>

</body>
</html>
