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
		$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/lib/getvideo/getvideo.php?videoid=' . $this->videoID . '&format=ipad';	
		$filetarget = dirname ($_SERVER['SCRIPT_FILENAME']) . '/content/video.mp4';
	
		# Download video and deal with errors 
		if (!$this->downloadVideo($url, $filetarget)) {
			echo "Video could not be downloaded, due to the following error: <pre>".htmlspecialchars($this->getErrorMessage())."</pre></p>";   
		};
			
		# Return video Location
		return $this->inputVideoLocation;
		#$soundtrackGenerator = new soundtrackGenerator;
		#$soundtrackGenerator->getSoundtrack($this->inputVideoLocation);
		
    }
	
    public function downloadVideo($url, $file_target) {  
        # Check for read/write permission for URL and target file
        if (!$rh = fopen($url, 'rb')) {
            $this->errorMessage = "Can not read origin url. \nIf HTTP/1.1 403 Forbidden error was shown, YouTube might be trying to display an Ad before the video. \nTry again with a different video.";
            return false;
        };
        
        if (!$wh = fopen($file_target, 'wb')) {
            $this->errorMessage = 'Can not write video to target folder.';
            return false;   
        };
        
        while (!feof($rh)) {
            if (fwrite($wh, fread($rh, 1024)) === FALSE) {
                   $this->errorMessage = 'Download error: Cannot write to file ('.$file_target.')';
                   return false;
               }
        }
        
        fclose($rh);
        fclose($wh);
        return true;
    }
    
}

?>