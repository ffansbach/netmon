<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show stacked area chart
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_area_stack.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(400, 300)); 

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Verfuegbarkeit im Laufe der letzten 12 Stunden', 12)),        
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);
$Legend->setPlotarea($Plotarea);        

// create the dataset
$Datasets[0] =& Image_Graph::factory('dataset'); 


for ($i=0; $i<=10; $i++) {

$Datasets[0]->addPoint('Denmark', 10); 
$Datasets[0]->addPoint('Norway', 3); 
$Datasets[0]->addPoint('Sweden', 8); 
$Datasets[0]->addPoint('Finland', 5);
}

// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Area', array($Datasets, 'stacked'));

// set a line color
$Plot->setLineColor('gray');

$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('blue@0.2');
$FillArray->addColor('yellow@0.2');
$FillArray->addColor('green@0.2');

// set a standard fill style
$Plot->setFillStyle($FillArray);
	
// output the Graph
$Graph->done();
?>
