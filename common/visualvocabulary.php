<?php

include_once('vocabularyservices.php');
include_once('config.php');

$tematres_uri=$CFG_tematres_uri;

function jTopTermData($tematres_uri,$arrayVocabulary)
{		
		$arrayTerm=xmlVocabulary2array($tematres_uri,"fetchTopTerms","");	
		
		$jResponse='"id": "'.$tematres_uri.'",';
        $jResponse.='"name": "'.FixEncoding($arrayVocabulary["result"]["title"]).'",';
        $jResponse.='"children": [';
        $jResponse.=arrayVocabulary2JSON_JIT($arrayTerm,"");        
		$jResponse.=']';
	
return $jResponse;
}


function jTermData($tematres_uri,$term_id)
{
		$jResponse=null;
		
		$arrayTerm=xmlVocabulary2array($tematres_uri,"fetchTerm",$term_id);
		
		$stringTerm=$arrayTerm["result"]["term"]["string"];

        $jResponse.='"id": "'.$term_id.'",';
        $jResponse.='"name": "'.$stringTerm.'",';

        //abre relaciones del término
        $jResponse.='"children": [';


/*
 * related terms
*/
		$arrayRelatedTerm=xmlVocabulary2array($tematres_uri,"fetchRelated",$term_id);		
		if ($arrayRelatedTerm["resume"]["cant_result"]>'0') 
		{	
        $jResponse.='{
            "id": "RT'.$term_id.'",
            "name": "Related Terms",';
        $jResponse.='    "data": {
                "relation": "Related Terms",
			    "cantTerms": "'.$arrayRelatedTerm["resume"]["cant_result"].' terms."
            },
			"children": [';


        $jResponse.=arrayVocabulary2JSON_JIT($arrayRelatedTerm,"");       

		//Cierra related
		$jResponse.=']},';
	    }

/*
 * Narrow terms
*/
		$arrayNarrowTerm=xmlVocabulary2array($tematres_uri,"fetchDown",$term_id);
		if ($arrayNarrowTerm["resume"]["cant_result"]>'0') 
		{
		
        $jResponse.='{
            "id": "NT'.$term_id.'",
            "name": "Narrower terms",';
            
        $jResponse.='    "data": {
                "relation": "Narrower terms",
                "cantTerms": "'.$arrayNarrowTerm["resume"]["cant_result"].' terms."
            },
			"children": [';
 
        $jResponse.=arrayVocabulary2JSON_JIT($arrayNarrowTerm,"");

		//Cierra narrower
		$jResponse.=']},';        		
		}

/*
 * Broader terms
*/
		$arrayBroaderTerm=xmlVocabulary2array($tematres_uri,"fetchUp",$term_id);

		if ($arrayBroaderTerm["resume"]["cant_result"]>'1') 
		{
        $BTdata=arrayVocabulary2JHieraquical($arrayBroaderTerm,$term_id);

        $jResponse.='{
            "id": "BT'.$term_id.'",
            "name": "Broader terms",';
        $jResponse.='    "data": {
                "relation": "Broader terms ",
				"cantTerms": "'.$arrayBroaderTerm["resume"]["cant_result"].' terms."
            },
			"children": [';

			
        
        $jResponse.=$BTdata["rows"];

		//Cierra broather
		$jResponse.=']},';        
	    };



if(isset($BTdata["BT_term_id"]))
{

/*
 * Adjacent terms
*/
		$arrayAjacentTerm=xmlVocabulary2array($tematres_uri,"fetchDown",$BTdata["BT_term_id"]);
		if ($arrayAjacentTerm["resume"]["cant_result"]>'1') 
		{		
        $jResponse.='{
            "id": "AT'.$term_id.'",
            "name": "Adjacent terms",';
        $jResponse.='    "data": {
                "relation": " Adjacent Terms",
                "cantTerms": "'.$arrayAjacentTerm["resume"]["cant_result"].' terms."
            },
			"children": [';
 
        $jResponse.=arrayVocabulary2JSON_JIT($arrayAjacentTerm,"");

		//Cierra narrower
		$jResponse.=']},';        		
		}
}
		
		//Cierra Término
		$jResponse.='],';        

//Data del término

	    $jResponse.='"data": {';
        $jResponse.='         "relation": "<a href=\"index.php?tema_id='.$term_id.'\" title=\"'.$stringTerm.'\">'.$stringTerm.'</a>",';
        $jResponse.='         "terms": "<a href=\"index.php?tema_id='.$term_id.'\" title=\"'.$stringTerm.'\">'.$stringTerm.'</a>",';
        $jResponse.='         "cantTerms": "",';
		$jResponse.='			},';
                
return $jResponse;
}

$tema_id = isset($_GET["tema_id"]) ? $_GET["tema_id"] : null ;

$Jcontent = ($tema_id) ? jTermData($tematres_uri,$tema_id) : jTopTermData($tematres_uri,$arrayVocabulary);


?>

var labelType, useGradients, nativeTextSupport, animate;

(function() {
  var ua = navigator.userAgent,
      iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
      typeOfCanvas = typeof HTMLCanvasElement,
      nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
      textSupport = nativeCanvasSupport 
        && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
  //I'm setting this based on the fact that ExCanvas provides text support for IE
  //and that as of today iPhone/iPad current text support is lame
  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
  nativeTextSupport = labelType == 'Native';
  useGradients = nativeCanvasSupport;
  animate = !(iStuff || !nativeCanvasSupport);
})();

var Log = {
  elem: false,
  write: function(text){
    if (!this.elem) 
      this.elem = document.getElementById('log');
    this.elem.innerHTML = text;
    this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px';
  }
};


function init(){
    //init data
   var json = {
	<?php 
	//Ugly way to fix error
	$Jcontent=str_replace("},]", "}]", $Jcontent);
	echo $Jcontent;
	?>		
    }
    //end
    var infovis = document.getElementById('infovis');
    var w = infovis.offsetWidth - 50, h = infovis.offsetHeight - 50;
    
    //init Hypertree
    //Cambiar color DAF
    var ht = new $jit.Hypertree({
      //id of the visualization container
      injectInto: 'infovis',
      //canvas width and height
      width: w,
      height: h,
      //Change node and edge styles such as
      //color, width and dimensions.
        Node: {
            dim: 9,
            color: "#f00"
        },
        
        Edge: {
            lineWidth: 2,
            color: "#088"
        },
        
        onBeforeCompute: function(node){
			//DAF 20122009
            //Log.write("centering");
        },
      //Attach event handlers and add text to the
      //labels. This method is only triggered on label
      //creation
      onCreateLabel: function(domElement, node){
		//DAF 20122009
            //domElement.innerHTML = node.name;
			if (node.id > 0) {            
				domElement.innerHTML = "<a href=index.php?tema_id=" + node.id +">" + node.name + "</a>";
            }
			else 
			{
				domElement.innerHTML = node.name;
			}
		  
              $jit.util.addEvent(domElement, 'click', function () {
              ht.onClick(node.id);
          });
      },
      //Change node styles when labels are placed
      //or moved.
      onPlaceLabel: function(domElement, node){
          var style = domElement.style;
          style.display = '';
          style.cursor = 'pointer';
          if (node._depth <= 1) {
              style.fontSize = "0.8em";
              style.color = "#ddd";

          } else if(node._depth == 2){
              style.fontSize = "0.7em";
              style.color = "#555";

          } else {
              style.display = 'none';
          }

          var left = parseInt(style.left);
          var w = domElement.offsetWidth;
          style.left = (left - w / 2) + 'px';
      },
      
      onAfterCompute: function(){
          //DAF 20122009
          //Log.write("done");
          
          //Build the right column relations list.
          //This is done by collecting the information (stored in the data property) 
          //for all the nodes adjacent to the centered node.
          var node = ht.graph.getClosestNodeToOrigin("current");
          var html = "<h4>" + node.name + "</h4><b>Connections:</b>";
          html += "<ul>";
          node.eachAdjacency(function(adj){
              var child = adj.nodeTo;
              if (child.data) {
				  //DAF 24122009
                  //var rel = (child.data.band == node.name) ? child.data.relation : node.data.relation;
                  //html += "<li>" + child.name + " " + "<div class=\"relation\">(relation: " + rel + ")</div></li>";
					var rel = (child.id == node.id) ? child.data.relation : child.data.cantTerms;                    
                    var theLink = (child.id>0) ? "<a href=index.php?tema_id=" + child.id +">" + child.name + " </a>" : child.name;
                    html += "<li>" + theLink + "<div class=\"relation\">" + rel +  "</div></li>";                  
                  
              }
          });
          html += "</ul>";
          $jit.id('inner-details').innerHTML = html;
      }
    });
    //load JSON data.
    ht.loadJSON(json);
    //compute positions and plot.
    ht.refresh();
    //end
    ht.controller.onAfterCompute();
}
