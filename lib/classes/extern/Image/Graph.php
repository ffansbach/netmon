<?php

include 'Image/Graph.php';     
$Graph =& Image_Graph::factory('graph', array(400, 300)); 
$Plotarea =& $Graph->addNew('plotarea'); 
$Dataset =& Image_Graph::factory('dataset'); 
$Dataset->addPoint('Denmark', 10); 
$Dataset->addPoint('Norway', 3); 
$Dataset->addPoint('Sweden', 8); 
$Dataset->addPoint('Finland', 5); 
$Plot =& $Plotarea->addNew('bar', &$Dataset); 
$Graph->done(); 

?>