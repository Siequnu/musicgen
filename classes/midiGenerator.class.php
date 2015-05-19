<?php

class midiGenerator {
    
	
	public $errorMessage = '';
	public $midiInstructions;
	public $midiTimeStamp;
	public $timesRepeated;
	public $channel;
	public $globalTranspose = 0;
	
	
	public function __construct () {
		# Add Midi Header
		$this->midiInstructions = array ();
		$this->midiInstructions[] = 'MFile 0 1 650';	
	}
	
	
	public function getErrorMessage () {return $this->errorMessage;}
	
	
    public function generateMIDIHarmony ($settingsArray) {
		# Set received data from array
		$this->channel = $settingsArray['channel'];
		$this->timesRepeated = $settingsArray['timesRepeated'];
		$this->midiTimeStamp = $settingsArray['startTimeStamp'];
		$this->chordArray = $settingsArray['chordArray'];
		
		# Handle transposition conditions
		foreach ($this->chordArray as &$chord) {
			foreach ($chord as &$note) {
				$note = $note + $this->globalTranspose + $settingsArray['transposeAmount'];
			}
		}
		
		# Add track header
		$this->midiInstructions[] = 'MTrk';
		$this->midiInstructions[] = "0 PrCh ch=$this->channel p={$settingsArray['instrumentID']}";
		
		# Write body of MIDI	
		$this->writeMIDIBody ($settingsArray);
		
		# Add Midi Footer
		$this->midiInstructions[] = "$this->midiTimeStamp Meta TrkEnd";
		$this->midiInstructions[] = 'TrkEnd';
		
		# Signal success
		return true;
	}

	private function writeMIDIBody ($settingsArray) {
		switch ($settingsArray['voiceType']) {
			case 'chord':
				$this->writeBlockChords($this->chordArray, $settingsArray['chordLengthMS']);
				break;
			case 'arp':
				$this->writeArpegChords($this->chordArray, $settingsArray['chordLengthMS']);
				break;
			case 'accent':
				$this->writeAccentChords($this->chordArray, $settingsArray['chordLengthMS']);
				break;
			case 'bass':
				$this->writeBassChords($this->chordArray, $settingsArray['chordLengthMS']);
				break;
			case 'clap':
				$this->writeClapEvent($settingsArray['chordLengthMS'], $settingsArray['instrumentID']);
				break;
			default:
				$this->errorMessage = 'Insufficient or incorrect parameters were given to MIDI Generator.';
				return false;
		}
	}
	
	
	public function getMIDIFile () {
		$midiText = implode ("\n", $this->midiInstructions);
		
		# Determine the file name or end
		if (!$file = $this->createFileName ()) {return false;}
		# Send MIDI to MIDI conversion class
		require_once './lib/midi/midi.class.php';
		$midi = new Midi();
		$midi->importTxt ($midiText);
		$midi->saveMidFile ($file);
		
		# Signal success
		return $file;	
	}
	
	
	private function writeArpegChords ($array, $lengthPerChord) {
		$shortLength = $lengthPerChord/8;
		# Add main track with chords
		for ($x = 1; $x <= $this->timesRepeated; $x++) {
			foreach ($array as $chord) { // Open each chord array which contains 4 notes
				foreach ($chord as $note) { //Add each note to an array, when 4 notes are in array, print out On and Off MIDI info
					$noteArray [] = $note;
				}
				
				# Print On message for 4 notes. Repeat each chord twice
				for ($y = 1; $y <= 2; $y++) {
					foreach ($noteArray as $noteInNoteArray) {
					
					# Print On message for note	
					$this->midiInstructions[] = "$this->midiTimeStamp On ch=$this->channel n=$noteInNoteArray v=60";
					
					# Advances timestamp
					$this->midiTimeStamp = $this->midiTimeStamp + $shortLength;
					
					# Print Off message for note
					$this->midiInstructions[] = "$this->midiTimeStamp Off ch=$this->channel n=$noteInNoteArray v=60";
					}	
				}
				
				unset ($noteArray);
			}
		}	
	}
	
	
	private function writeBlockChords ($array, $lengthPerChord) {
		# Add main track with chords
		for ($x = 1; $x <= $this->timesRepeated; $x++) {
			foreach ($array as $chord) { // Open each chord array which contains 4 notes
				foreach ($chord as $note) { //Add each note to an array, when 4 notes are in array, print out On and Off MIDI info
					$noteArray [] = $note;
				}
				
				# Print On message for 4 notes 
				foreach ($noteArray as $noteInNoteArray) {
					$this->midiInstructions[] = "$this->midiTimeStamp On ch=$this->channel n=$noteInNoteArray v=50";
				}
				
				# Advance timestamp         
				$this->midiTimeStamp = $this->midiTimeStamp + $lengthPerChord;
				
				# Print Off message for same notes, time stamp ready for next set of On.        
				foreach ($noteArray as $noteInNoteArray) {
					$this->midiInstructions[] = "$this->midiTimeStamp Off ch=$this->channel n=$noteInNoteArray v=50";   
				}
				unset ($noteArray);
			}
		}
	}
	
	private function writeAccentChords ($array, $lengthPerChord) {
		$accentNoteLength = $lengthPerChord/4;
		for ($x = 1; $x <= $this->timesRepeated; $x++) {
			foreach ($array as $chord) { // Open each chord array which contains 4 notes				
				# Find compatible notes to start accent run with
				$note = $this->findAccentNotes($chord);
				
				# Set position in displacementArray
				$positionInDisplacementArray = 0;
				
				# Set displacement array
				$noteDisplacement = array (
					'1'  => '0',
					'2'  => '7',
					'3'  => '5',
					'4'  => '-12',
				);
				
				if (!$note === false) {
					for ($r = 1; $r <= 4; $r++) {
						# Advance displacement array
						$positionInDisplacementArray++;
						$note = $note + $noteDisplacement[$positionInDisplacementArray];

						$this->midiInstructions[] = "$this->midiTimeStamp On ch=$this->channel n=$note v=60";
						
						# Advance timestamp         
						$this->midiTimeStamp = $this->midiTimeStamp + $accentNoteLength;
						
						# Print Off message for same notes, time stamp ready for next set of On.        
						$this->midiInstructions[] = "$this->midiTimeStamp Off ch=$this->channel n=$note v=60";
						
					}
				} else {
					$this->midiTimeStamp = $this->midiTimeStamp + $lengthPerChord;
				}
				
				unset ($noteArray);
				unset ($startingNote);
			}
		}
	}
	
	private function writeBassChords ($array, $lengthPerChord) {
		# Add main track with chords
		for ($x = 1; $x <= $this->timesRepeated; $x++) {
			foreach ($array as $chord) { // Open each chord array which contains 4 notes
				foreach ($chord as $note) { //Add each note to an array, when 4 notes are in array, print out On and Off MIDI info
					$noteArray [] = $note;
				}
				
				# Print on for random note from this chord to play on beat 1
				$this->midiInstructions[] = "$this->midiTimeStamp On ch=$this->channel n=$noteArray[0] v=60";
				
				# Advance timestamp         
				$this->midiTimeStamp = $this->midiTimeStamp + $lengthPerChord;
				
				# Print Off message for same notes, time stamp ready for next set of On.        
				$this->midiInstructions[] = "$this->midiTimeStamp Off ch=$this->channel n=$noteArray[0] v=60";
	
				unset ($noteArray);
			}
		}
	}
	
	private function writeClapEvent ($lengthPerChord, $instrumentID) {
		# Define sublength
		$subLength = $lengthPerChord / 8;

		# Define clap pattern
		$clapPatternArray = array (
			'0'  => 'off',
			'1'  => 'off',
			'2'  => 'on',
			'3'  => 'on',
			'4'  => 'off',
			'5'  => 'off',
			'6'  => 'on',
			'7'  => 'off',
		);
		
		for ($repeats = 1; $repeats <= $this->timesRepeated * 4; $repeats++) {
			
			foreach ($clapPatternArray as $clapTiming) {
				
				switch ($clapTiming) {
					case 'off':
						# Advance timestamp
						$this->midiTimeStamp = $this->midiTimeStamp + $subLength;
						break;
					case 'on':
						# Print on for clap
						$this->midiInstructions[] = "$this->midiTimeStamp On ch=$this->channel n=40 v=50";
					
						# Advance timestamp         
						$this->midiTimeStamp = $this->midiTimeStamp + $subLength;
					
						# Print Off message for clap, time stamp ready for next set of On.        
						$this->midiInstructions[] = "$this->midiTimeStamp Off ch=$this->channel n=40 v=50";
						break;
					default:
						# Advance timestamp
						$this->midiTimeStamp = $this->midiTimeStamp + $subLength;
						break;
				}
			}
			
		}
	}
	
	private function findAccentNotes ($array) {
		foreach ($array as $note) {
			if (!array_search($note - 5, $array)) {
				# Not this note;
			} else {
				#This is tonic note
				return $note;
			}
			if (!array_search($note - 17, $array)) {
				# Not this note;
			} else {
				#This is tonic note
				return $note;
			}
		}	
	}
	
	
	
	private function createFileName () {
		
		# Check if directory is writable
		$directory  = dirname ($_SERVER['SCRIPT_FILENAME']) . '/output/';
		if (!is_writable ($directory)) {
			$this->errorMessage = 'Could not write to output directory.';
			return false;
		}
		
		# Create a unique filename
		$originalUmask = umask (0000);
		$file = tempnam ($directory, 'midi');
		umask ($originalUmask);
		rename ($file, $file . '.midi');
		chmod ($file . '.midi', 0770);
		return $file . '.midi';
	}
	
}

?>