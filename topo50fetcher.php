<?php

$availableFileList = "topo50list.txt";

$wantedMapNamesList = "TeAraroalist.min.txt";

$baseUrl = "http://topo.linz.govt.nz/Topo50_raster_images/GeoTIFFTopo50";
$targetDir = "topos/";
	 
//http://topo.linz.govt.nz/Topo50_raster_images/GeoTIFFTopo50/

$latestFileList = array();

function fileToKey($filename)
{
	$nameEndPos = strpos($filename, "_GeoTif");
	$name = substr($filename, 0, $nameEndPos);
	$version = substr($filename, -8, -4);
	$version = str_ireplace("-",".",$version);
	
	return array($name, floatval($version));
}

$handle = fopen($availableFileList, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        // process the line read.
        $insert = false;
        $lineParts = explode(" ", $line);
        $filename = trim($lineParts[count($lineParts)-1]);
        
        
        list($mapName, $mapVersion) = fileToKey($filename);
        
        if(array_key_exists($mapName, $latestFileList))
        {
        	if (	$latestFileList[$mapName]['version'] < $mapVersion)
        	{
        		$insert = true;
        	}
        	
        } else {
        	$insert = true;
        }
        
        if ($insert)
        {
        	$latestFileList[$mapName] = array('version'=>$mapVersion, 'filename'=> $filename);
        }
        
    }

	foreach($latestFileList as $key => $latestFile)
	{
		echo $key ." = " .$latestFile['filename']."\n";
	}


    fclose($handle);
} else {
    echo "Could not open latest file list";
    die;
} 

$latestWantedFiles = array();

$handle = fopen($wantedMapNamesList, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
	
	$latestWantedFiles[] = $latestFileList[trim($line)]['filename'];
	
	
	}
    fclose($handle);
} else {
    echo "Could not open wanted file list";
    die;
} 

foreach($latestWantedFiles as $latestWantedFile)
{
	echo "Downloading ".$baseUrl."/".$latestWantedFile." to $targetDir".$latestWantedFile."\n";
	exec("curl ".$baseUrl."/".$latestWantedFile." > $targetDir".$latestWantedFile);
	
	
}

//	exec("curl http://topo.linz.govt.nz/Topo50_raster_images/GeoTIFFTopo50/CF14_GeoTifv1-01.tif > $targetDir".$latestWantedFile);
