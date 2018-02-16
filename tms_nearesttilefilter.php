<?php

$coordinateFile = "./teararoa_coords.txt";
$zoomLevel = 16;//Not much point making this lower than 12
$skipCoords = 1;
$margin = 2;//number of tiles either side of point that get included

$tileSource = "./tiles/";
$tileDest = "./filteredtiles/";

$globalmaptilesPyPath = "./globalmaptiles.py";

$usefulFiles = array();


function addMarginAround($z, $x, $y, $margin, &$usefulFiles)
{
	error_log("Adding margin around ".$z." ". $x. " ". $y);
	for($theX = $x-$margin; $theX <= $x+$margin; $theX++)
	{
		for($theY = $y-$margin; $theY <= $y+$margin; $theY++)
		{
    		$usefulFiles[$z][$theX][$theY] = true;
		}
	}
}

$handle = fopen($coordinateFile, "r");
if ($handle) {
	$lineCounter = 0;
    while (($line = fgets($handle)) !== false) {
    	$lineCounter++;
    	if($lineCounter < $skipCoords)
    	{
    		continue;
    	}
    	$lineCounter = 0;
    	
    
        $lineParts = explode(",", $line);
    	$longitude = trim($lineParts[0]);
    	$latitude = trim($lineParts[1]);
    	$cmd = "$globalmaptilesPyPath $zoomLevel $latitude $longitude";
    	error_log("Executing : ".$cmd);
		exec($cmd, $output);
//		error_log("output = ".print_r($output,true));
//		die;
    	$outputParts = explode(" ",$output[count($output)-1]);
    	
		$x = $outputParts[1];
		$y = $outputParts[2];
    	
    	addMarginAround($zoomLevel, $x, $y, $margin, $usefulFiles);	
    }
}

foreach($usefulFiles as $z => $filesAtZoom)
{
	error_log("Looping through zoom : ".$z);
	foreach($filesAtZoom as $x => $filesAtX)
	{
		foreach($filesAtX as $y => $exists)
		{
			$tileDir = $z."/".$x;
			$tilePath = $tileDir."/".$y.".png";
			if(file_exists($tileSource.$tilePath))
			{
				if(!file_exists($tileDest.$tileDir))
				{
					error_log("making dir :".$tileDest.$tileDir);
					mkdir($tileDest.$tileDir, 0777, true);
				}
				copy($tileSource.$tilePath, $tileDest.$tilePath);
			} else {
				error_log("Do not have this file to copy: ".$tileSource.$tilePath);
			}
		}
	}
}