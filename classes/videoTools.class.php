<?php

class videoTools {


	public $videoFilepath;
	public $audioFilepath;
	public $outputDirectory;
	public $outputFilepath;
	public $videoID;


    public function __construct() {
		# Do nothing
    }


	public function getErrorMessage () {return $this->errorMessage;}

	/*
	 * Set the class properties for default paths
	 *
	 */
	public function setDefaultPaths ($inputVideoLocation) {

		# Create file and set permissions
		$originalUmask = umask (0000);
		$outputFolder = dirname ($_SERVER['SCRIPT_FILENAME']) . '/output/';
		$outputFilepath = tempnam ($outputFolder, $this->videoID . '-');
		umask ($originalUmask);
		rename ($outputFilepath, $outputFilepath . '.mp4');
		$outputFilepath = $outputFilepath . '.mp4';
		chmod ($outputFilepath, 0775);

		# Set path to original video file
		if (!$this->setVideoFilepath ($inputVideoLocation)) {
			return false;
		}

		# Check origin video is readable
		if (!is_readable($this->videoFilepath)) {
			$this->errorMessage = 'Content video is not readable. Check read permissions.';
			return false;
		}

		# Set and check output folder is writeable
		if (!$this->setOutputFilepath ($outputFolder, $outputFilepath)) {
			return false;
		}

		return true;
	}

	/*
	 * Set class property videoFilepath
	 *
	 * @param str $path Path to source video
	 */
	public function setVideoFilepath ($path) {
		$this->videoFilepath = $path;
		if (!is_file ($this->videoFilepath)) {
			$this->errorMessage = 'No source video found.';
			return false;
		}
		return true;
	}

	/*
	 * Set class property outputFilepath and outputDirectory
	 *
	 * @param str $outputFolder Path to output folder
	 * @param str $outputFilepath Output filepath
	 */
	public function setOutputFilepath ($outputFolder, $outputFilepath) {
		if (!is_writable ($outputFolder)) {
			$this->errorMessage = "Can't write to output directory.";
			return false;
		}
		$this->outputDirectory = $outputFolder;
		$this->outputFilepath = $outputFilepath;
		return true;
	}

	/*
     * Generate HTML5 video tag for video at a given location
     *
     * @param str $location Filename (within running directory) of the videofile
     *
     * @return str HTML code
     */
    public function getVideoHTMLTag ($location) {
        $html = "<video src=\"{$location}\" width=600 controls=\"controls\">
                Your browser does not support the VIDEO element
                </video>
				<p>Refresh the page to generate a different soundtrack.";
        return $html;
    }


	/*
	 * Calculate duration of a video file
	 *
	 * @return str Duration (eg. 00:01:43.24)
	 */
	public function getVideoDuration () {
		$cmd = "ffmpeg -i {$this->videoFilepath} 2>&1 | grep Duration | awk '{print $2}' | tr -d ,";
		$output = $this->shellExec ($cmd);
		if (!$output) {
			$output = $this->shellExecLocal($cmd);
		}
		return $output;
	}


	/*
	 * Executes a command and returns output
	 *
	 * @param str $cmd The command
	 *
	 * @return str The result of the program
	 */
	private function shellExecLocal ($cmd) {

		# Check if local binaries of ffmpeg and ffprobe are present
		$ffmpegLocation = dirname ($_SERVER['SCRIPT_FILENAME']) . '/ffmpeg';
		if (file_exists($ffmpegLocation)) {
			$cmd = './' . $cmd;
			$output = shell_exec ($cmd);
		}
		return $output;
	}


	/*
	 * Executes a command and returns output
	 *
	 * @param str $cmd The command
	 *
	 * @return str The result of the program
	 */
	private function shellExec ($cmd) {
		if (substr(php_uname(), 0, 5) == "Linux"){
			$output = shell_exec ($cmd);
		} else {
			$cmd = '/usr/local/bin/' . $cmd;
			$output = shell_exec ($cmd);
		}
		return $output;
	}


	/*
	 * Parse an output file from ffprobe to get timings
	 *
	 * @param str $filepath Path to ffprobe output file
	 *
	 * @return array Array with timings
	 */
	public function parseCutSceneFile ($filepath) {

		# Set location
		$txt_file = file_get_contents($filepath);

		# Parse into rows
		$rows = explode("\n", $txt_file);
		foreach ($rows as $frameInfo) {
			$explodedRows[] = explode('|', $frameInfo);
		}

		# Remove last element (contains no timing information)
		array_pop($explodedRows);

		# Parse timings line (from 'pkt_pts_time=2.080000' to '2.080')
		foreach ($explodedRows as $frame) {
			$sceneChangeTime = $frame[3];
			$sceneChangeTime = explode('=', $sceneChangeTime);
			$sceneChangeTime = $sceneChangeTime[1]; // 2.080000
			$timeSplitDecimalPoint = explode ('.', $sceneChangeTime);
			$strLen = strlen ($timeSplitDecimalPoint[0]); // 2.08000 -> 2080; 19.8788 -> 19878
			$joinedData = implode ($timeSplitDecimalPoint); // 2080
			$finalFormattedTime[] = substr ($joinedData, 0, ($strLen + 3));
		}

		return $finalFormattedTime;
	}


	/* Set class property audioFilepath
	 *
	 * @param str $path Path to converted audio file
	 *
	 */
	public function setAudioFilepath ($path) {
		$this->audioFilepath = $path;
		if (!is_readable ($this->audioFilepath)) {
			$this->errorMessage = "Can't read converted audio file. File is not present or check read permissions.";
			return false;
		}
		return true;
	}


	/*
	 * Uses ffmpeg to write new audio on a video
	 */
	public function mergeAudioWithVideo () {
		# Define command
        $cmd = "ffmpeg -y -i \"{$this->audioFilepath}\" -i \"{$this->videoFilepath}\" -preset ultrafast -strict experimental \"{$this->outputFilepath}\"";
		$exitStatus = $this->execCmd ($cmd);

		# Handle errors
		if ($exitStatus != 0) {
            # Try local version of ffprobe in folder
            $exitStatus = $this->execLocalCMD ($cmd);
        }

		# Deal with error messages
		if ($exitStatus != 0) {
            $this->errorMessage = 'The video file could not be rendered, due to an error with ffmpeg.';
            return false;
        }

		return true;
	}


    /*
     * Converts a MIDI file to WAV.
     *
     * @param str $file Filepath of MIDI file to be converted
     *
     * @return bool True if operation succeded, False if error occured.
     */
    public function convertMIDIToWAV ($midiFilepath) {
        # Convert MIDI file to WAV using timidity in shell
        # Define command
		$cmd = "timidity -Ow \"{$midiFilepath}\"";

		# Execute command
		$exitStatus = $this->execCmd ($cmd);

		# Deal with error messages
		if ($exitStatus != 0) {
            #echo nl2br (htmlspecialchars (implode ("\n", $output)));
            $this->errorMessage = 'The WAV file could not be created, due to an error with the converter.';
            return false;
        }

		# Set $this->audioFilepath
		$pathToMusicFile = dirname ($_SERVER['SCRIPT_FILENAME']) . '/output/' . pathinfo ($midiFilepath, PATHINFO_FILENAME) . '.wav';

		if (!$this->setAudioFilepath ($pathToMusicFile)) {
			$this->errorMessage = 'The converted audio file could not be found.';
			return false;
		}

        return true;
    }


    /*
     * Generate HTML5 audio tag for audio at a given location
     *
     * @param str $location Filename (within running directory) of the audiofile
     *
     * @return str HTML code
     */
    public function getAudioHTMLTag ($location) {
        $html = "<audio src=\"{$location}\" controls=\"controls\">
                Your browser does not support the AUDIO element
                </audio>";
        return $html;
    }


	/*
	 * Executes a command on a binary in the index.php directory and returns an exit status
	 *
	 * @param str $cmd The command
	 *
	 * @return bool The exit status
	 */
	private function execLocalCMD ($cmd) {

		# Check if local binaries of ffmpeg and ffprobe are present
		$ffmpegLocation = dirname ($_SERVER['SCRIPT_FILENAME']) . '/ffmpeg';
		if (file_exists($ffmpegLocation)) {
			$cmd = './' . $cmd;
			exec ($cmd, $output, $exitStatus);
		}
		return $exitStatus;
	}


	/*
	 * Executes a command and returns an exit status
	 *
	 * @param str $cmd The command
	 *
	 * @return bool The exit status
	 */
	private function execCmd ($cmd) {
		if (substr(php_uname(), 0, 5) == "Linux"){
			exec ($cmd, $output, $exitStatus);
		} else {
        $cmd = '/usr/local/bin/' . $cmd;
        exec ($cmd, $output, $exitStatus);
		}
		return $exitStatus;
	}


}

?>