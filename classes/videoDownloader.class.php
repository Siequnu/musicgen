<?php

class videoDownloader {
    
	public $formSubmitted;
	public $videoID;
    public $inputVideoLocation;
	
	
    public function __construct () {
    }
    
	
	public function getErrorMessage () {return $this->errorMessage;}
    
	
    public function main ($videoID) {        
        # Set input video location
        $this->inputVideoLocation = dirname ($_SERVER['SCRIPT_FILENAME']) . '/content/video.mp4';

		# Assign passed on data (video ID)
		$this->videoID = $videoID;
				
		# Build URL and path to target video file
		$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/lib/getvideo/getvideo.php?videoid=' . $this->videoID . '&format=18';	
		$filetarget = dirname ($_SERVER['SCRIPT_FILENAME']) . '/content/';
	
		# Download video and deal with errors 
		if (!$this->downloadVideo($url, $filetarget)) {
			echo "Video could not be downloaded, due to the following error: <pre>".htmlspecialchars($this->getErrorMessage())."</pre></p>";   
		};
			
		# Return video Location
		return $this->inputVideoLocation;
		
    }
	
    public function downloadVideo($url, $file_target) {  
		
		# Check if target directory is writeable
		if (!is_writeable($file_target)) {
			$this->errorMessage = 'Can not write to output directory.';
			return false;
		}
		
		# Set local filepath for curl
		$file_target = 'content/video.mp4';
		
		# Download file
		$cmd = "curl -L -o {$file_target} '{$url}'";
		exec ($cmd, $output, $exitStatus);
		if (!$exitStatus === 0) {
			$this->errorMessage = 'Download failed due to an error with cURL.';
			return false;
		}
        return true;
    }
    
}

?>