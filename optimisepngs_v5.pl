#!/usr/bin/perl

use File::Slurp;
use File::Copy;
use File::Find;
use strict;
use warnings;

#gdalbuildvrt -srcnodata b4 -hidenodata merged.vrt *.tif
#gdal2tiles.py merged.vrt tiles
#find ./ -type f -size 334c -exec rm -f {} \;
#find ./ -depth -empty -type d -exec rmdir {} \;
#/Users/craig/Documents/development/iOS/topomaps/optimisepngs.pl tiles/

foreach my $incomingDir (@ARGV) {

	
	my @files = read_dir($incomingDir);
	foreach my $zoomlevel(@files)  
	{
        if(-d "$incomingDir/$zoomlevel")
        {
        	
        
			my @subdirs = read_dir("$incomingDir/$zoomlevel");
			print "Starting zoomlevel $zoomlevel \n";
            print "  ".scalar @subdirs . " folders to scan\n";
            my $folderCount = scalar @subdirs;
            my $index = 1;
			foreach my $x(@subdirs)
			{	
                print "    Starting subfolder ".$index. " of ".$folderCount." (".$incomingDir."/".$zoomlevel."/".$x.")\n"; 
				if(-d "$incomingDir/$zoomlevel/$x")
				{
				    qx(/Applications/Utilities/pngnq -n 256 $incomingDir/$zoomlevel/$x/*.png);
				
					my @mapfiles = read_dir("$incomingDir/$zoomlevel/$x");
                    foreach my $y(@mapfiles)
                    {
                    	$_ ="$incomingDir/$zoomlevel/$x/$y";
                    	my $oldfile = $_;
                    	
   						s/-nq8//g;
   						rename($oldfile, $_);
                    }
				}
                $index++;
			}
		}
	}

   
}