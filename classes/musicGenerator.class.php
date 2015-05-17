<?php

class musicGenerator {
	
	public $musicStyleKey;
	public $numberOfVerses;
	public $videoID;
	public $instrumentArray;
	public $sequenceOfHarmony;
	public $soundtrackOnly;
	
    public function __construct () {
        include_once './classes/chordSequences.class.php';
        include_once './classes/instrumentSets.class.php';
        include_once './classes/harmonyLogic.class.php';
        include_once './classes/midiGenerator.class.php';
		include_once './classes/videoTools.class.php';
		include_once './classes/videoDownloader.class.php';
        
        # Define music styles
        $this->musicStyles = array (
            '0'   => 'Generic YouTube',
            '1'   => 'Electronic YouTube',
            '2'   => 'Piano',
            '3'   => 'Drums only intense'
        ); 
        
    }

    public function getErrorMessage() {return $this->errorMessage;}
	
	
    public function main() {
            
		# Generate form to get link to youtube video
        if (!isSet($formData)) {
            $formData = $this->generateForm();
		}

		# If form has been completed, continue
		if ($formData) {
			# Assign form data
			$this->assignFormData($formData);
			
			# Setup music generator
			$this->setupMusicGenerator();
			
			# Setup video data
			if ($this->soundtrackOnly === false) {
			$this->setupVideoData();
			}
			
			# Write tracks
			$this->writeMIDITracks();
			
			# Join audio and video
			$this->processAudioAndVideo();
		}
    }
    
	
	/*
	 * Sets up conditions to generate unique music: random transposition, instrument set, chord sequence
	 */
	public function setupMusicGenerator () {
		# Set up Generator and HarmonyLogic
		$this->midiGenerator = new midiGenerator;
		$harmonyLogic = new harmonyLogic;
			
		# Set random transposition
		$randomTransposition = mt_rand (1, 12);
		$this->midiGenerator->globalTranspose = $randomTransposition;
		
		# Get random instrument set
		$instrumentSets = new instrumentSets;
		$this->instrumentArray = $instrumentSets->getInstrumentation($this->musicStyleKey);
			
		# Get random chord sequence that fits style from array 
		$chordSequences = new chordSequences;
		$randomChordSequence = $chordSequences->getChordSequence($this->musicStyleKey);
		# Deal with errors
		if ($randomChordSequence === false) {
			echo "Music generation encountered the following error: <pre>".htmlspecialchars($this->getErrorMessage())."</pre></p>";       
		}
		
		# Get sequence of harmony
		$this->sequenceOfHarmony = $harmonyLogic->getHarmony($randomChordSequence);
		
		return true;
	}
	
	
	/*
	 * Downloads video, sets filepaths and analyses video
	 */
	private function setupVideoData () {
		# Download and analyse video
		$this->videoDownloader = new videoDownloader;
		$inputVideoLocation = $this->videoDownloader->main($this->videoID);
		
		# Set videoFilapath and outputFilepath paths
		$this->videoTools = new videoTools;
		if (!$this->videoTools->setDefaultPaths ($inputVideoLocation)) {
			echo "Music generation encountered the following error: <pre>".htmlspecialchars($this->videoTools->getErrorMessage())."</pre></p>"; 
		}
		
		# Get cutscenes
		#$cutScenesFile = $this->videoTools->getCutScenes();
		#end ($cutScenesFile);
		#$lastSceneChangeKey = key($cutScenesFile);
		#$lastSceneChangeTime = $cutScenesFile[$lastSceneChangeKey];
		
		# At 1 chord per second, how many chords needed. * 4 chords per repetition = number of verses
		#$numberOfRepetitions = $lastSceneChangeTime / 4 / 2000;
		
		$videoDuration = ($this->videoTools->getVideoDuration()); // 00:01:43.14
		$videoDuration = explode ('.', $videoDuration);
		
		# Calculate duration in number of seconds
		$minutes = explode (':', $videoDuration[0]);
		$seconds = ($minutes[0] * 3600) + ($minutes[1] * 60) + $minutes[2];
		$this->numberOfVerses = ($seconds/6.2);	
	}
	
	
	/*
	 * Writes MIDI tracks for each instrument
	 */
	public function writeMIDITracks () {
		# Set trackstart
		$timeStamp = 0;
			
		# Set defaults common to all tracks
		$settingsArray = array();
		$settingsArray['startTimeStamp'] = 1000;
		$settingsArray['chordArray'] = $this->sequenceOfHarmony;
		$settingsArray['chordLengthMS'] = 2000;
		$settingsArray['timesRepeated'] = $this->numberOfVerses;

		# Create harmony for pad and add to MIDI instructions
		$settingsArray['channel'] = 1;
		$settingsArray['voiceType'] = 'chord';
		$settingsArray['instrumentID'] = $this->instrumentArray[1];
		$settingsArray['transposeAmount'] = 24;
		$this->midiGenerator->generateMIDIHarmony ($settingsArray);
		
		# Create and add arpeggio
		$settingsArray['channel'] = 2;
		$settingsArray['voiceType'] = 'arp';
		$settingsArray['instrumentID'] = $this->instrumentArray[0];
		$settingsArray['transposeAmount'] = 36;
		$this->midiGenerator->generateMIDIHarmony ($settingsArray);
		
		# Create accent
		$settingsArray['channel'] = 3;
		$settingsArray['voiceType'] = 'accent';
		$settingsArray['instrumentID'] = $this->instrumentArray[2];
		$settingsArray['transposeAmount'] = 48;
		$this->midiGenerator->generateMIDIHarmony ($settingsArray);
		
		# Create bass
		$settingsArray['channel'] = 4;
		$settingsArray['voiceType'] = 'bass';
		$settingsArray['instrumentID'] = $this->instrumentArray[3];
		$settingsArray['transposeAmount'] = 24;
		$this->midiGenerator->generateMIDIHarmony ($settingsArray);
		
		# Insert clap at key locations
		$settingsArray['channel'] = 10;
		$settingsArray['voiceType'] = 'clap';
		$settingsArray['transposeAmount'] = 0;
		$settingsArray['instrumentID'] = 1; // Chanel 10 is percussion, program 1 is default basic drum kit
		$this->midiGenerator->globalTranspose = 0;
		$this->midiGenerator->generateMIDIHarmony ($settingsArray);
	}
	
	
	/*
	 * Converts MIDI to WAV, converges audio and video files, displays in browser
	 */
	public function processAudioAndVideo () {
		# Get file, convert to WAV and output HTML5
		$audioFileLocation = $this->processMIDItoWAV();
		
		if ($this->soundtrackOnly === true) {
			echo $this->getAudioHTMLTag ($audioFileLocation);
			return;
		}
		
		# Merge audio and video
		$pathToMusicFile = dirname ($_SERVER['SCRIPT_FILENAME']) . '/output/' . pathinfo ($audioFileLocation, PATHINFO_FILENAME) . '.wav';
		$this->videoTools->setAudioFilepath ($pathToMusicFile);

		# Merge generated audio with video and display in browser
		if (!$this->videoTools->mergeAudioWithVideo()) {
			echo "\n<p>The audio could not be merged with the video, due to the following error: <pre>".htmlspecialchars($this->videoTools->getErrorMessage())."</pre></p>";
		} else {
			# Echo HTML5 tag with video file
			$pathToVideoFile = './output/finalvideo.mp4';
			echo $this->videoTools->getVideoHTMLTag ($pathToVideoFile);	
		}
	}
	
	
	/*
	 * Convert MIDI file to WAV
	 *
	 * @return str Path to converted WAV file
	 */
	private function processMIDItoWAV () {
		# Get complete file location from MIDI Generator
		$file = $this->midiGenerator->getMIDIFile ();
		# Deal with result messages
		if (!$file) {
			echo "\n<p>The MIDI file could not be created, due to the following error: <pre>".htmlspecialchars($this->midiGenerator->getErrorMessage())."</pre></p>";
			return false;
		} 
		
		# Convert MIDI file to WAV
		$this->convertMIDIToWAV ($file);
		
		# Return location of generated WAV file
		$location = '/musicgen/output/' . pathinfo ($file, PATHINFO_FILENAME) . '.wav';
		return $location;	
	}
	
	
	/*
	 * Assigns form data to variables
	 */
	private function assignFormData ($formData) {
		# Get style choice identifier key
        $this->musicStyleKey = $this->getMusicStyleKey($formData['Music Style']);
        if ($this->musicStyleKey === false) {
			echo "Music could not be generated, due to the following error: <pre>".htmlspecialchars($this->getErrorMessage())."</pre></p>";   
		};
		
		# Assign video ID
		$this->videoID = $formData['url'];
		
		# Check if only soundtrack generation
		$this->soundtrackOnly = ($formData['radiobuttons'] === 'Generate soundtrack file only' ? true : false);
		
		if ($this->soundtrackOnly === true) {
			if (isSet($formData['verses'])) {
				$this->numberOfVerses = $formData['verses'];
			} else {
				# Set default length
				$this->numberOfVerses = 4;
			}
		}
		
	}
	
	
	/*
	 * Generates and processes an input form
	 */
	public function generateForm () {
        # Load the form module 
        require_once ('./lib/ultimateform/ultimateForm.php');
        require_once ('./lib/ultimateform/pureContent.php');
        require_once ('./lib/ultimateform/application.php');
        
        # Create a form instance 
        $form = new form (array (
            'get'                    => 'true',
            'div'                    => 'form-download',
            'submitButtonText'       => 'Generate Music',
			'formCompleteText'       => false,
			'requiredFieldIndicator' => false,
			'submitButtonAccesskey'  => false,
        ));
        
        $form->heading (2, 'Generative Soundtrack Creation');
        $form->heading ('p', 'Please complete the form below');
		
		# A set of radio buttons
		$form->radiobuttons (array (
		'name'					=> 'radiobuttons',
		'values'			    => array ('Generate video with soundtrack', 'Generate soundtrack file only',),
		'title'					=> 'Generation options',
		'description'			=> '',
		'output'				=> array (),
		'required'				=> true,
		'default'				=> 'Generate soundtrack file only',
		));
		
        # Create a standard input box
        $form->input (array (
        'name'					=> 'url',
        'title'					=> 'YouTube URL',
        'description'			=> '',
        'output'				=> array (),
        'size'					=> 32,
        'maxlength'				=> '',
        'default'				=> 'P6JfInyQI9Q',
        'regexp'				=> '',
        ));
        
		
		# Create a standard input box
        $form->input (array (
        'name'					=> 'Music Style',
        'title'					=> 'Style of soundtrack music',
        'description'			=> '',
        'output'				=> array (),
        'size'					=> 32,
        'maxlength'				=> '',
        'default'				=> 'Generic YouTube',
        'regexp'				=> '',
        ));
		
		 # Create a standard input box
		$form->input (array (
        'name'					=> 'verses',
        'title'					=> 'Number of verses',
        'description'			=> 'Not used if a YouTube URL is given',
        'output'				=> array (),
        'size'					=> 32,
        'maxlength'				=> '',
        'default'				=> '4',
        'regexp'				=> '',
        ));
        
		# Process form and return result
        $result = $form->process ();
		return $result;
    }
	
	/*
	 * Gets style identifier key for selected musical style
	 *
	 * @return int Style choice key
	 */
    public function getMusicStyleKey ($styleChoice) {
        $styleChoiceKey = array_search ($styleChoice, $this->musicStyles);
        
        if ($styleChoiceKey === false) {
            $this->errorMessage = 'Selected style not found.';
            return false;
        }
        return $styleChoiceKey;
    }

    
    
    /*
     * Converts a MIDI file to WAV.
     *
     * @param str $file Filepath of MIDI file to be converted
     *
     * @return bool True if operation succeded, False if error occured.
     */ 
    public function convertMIDIToWAV ($file) {
        # Convert MIDI file to WAV using timidity in shell
        $cmd = "/usr/local/bin/timidity -Ow \"{$file}\"";   
        #echo $cmd;
        exec ($cmd, $output, $exitStatus);
        if ($exitStatus != 0) {
            #echo nl2br (htmlspecialchars (implode ("\n", $output)));
            echo "\n<p><pre>The WAV file could not be created, due to an error with the converter.</pre></p>";  
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
    
}

?>