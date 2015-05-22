<?php

class instrumentSets {
    
    public function getErrorMessage() {return $this->errorMessage;}
    
    public function __construct () {
        
        $this->musicStyleTable = array (
            '0'   => array ('melody', 'pad', 'accent', 'bass', 'clap'),
            '1'   => array ('melody', 'pad', 'accent', 'bass', 'clap'),
            '2'   => array ('melody', 'pad'),
            '3'   => array ('drumset',),
        );
        
        $this->melodyInstruments = array  (
            '0'   => 'piano',
            '1'   => 'marimba',
            '2'   => 'celesta',
            '3'   => 'vibraphone',
            '4'   => 'plucked guitar',
            '5'   => 'voice doo',
        );
        
        $this->padInstruments = array (
            '0'   => 'string ensemble',
            '1'   => 'smooth organ',
            '2'   => 'vibraphone',
            '3'   => 'drawbar organ',
            '4'   => 'electro pad',
            '5'   => 'funky chord pad',
            '6'   => 'electro pad 2',
            '7'   => 'dreamy pad',
            '8'   => 'voice doo',

        );
        
        $this->accentInstruments = array (
            '0'   => 'glockenspiel',
            '1'   => 'vibraphone',
            '2'   => 'piano',
            '3'   => 'plucked guitar',
            '4'   => 'harp',
            '5'   => 'dampened guitar',
        );
        
        $this->bassInstruments = array (
            '0'   => 'electro bass',
            '1'   => 'smooth organ',
            '2'   => 'theater organ',
            '3'   => 'voice doo',

        );
        
        $this->drumsetInstruments = array (
            '0'   => 'analogue set',
            '1'   => 'techno set',
        );
        
        $this->clapInstruments = array (
            '0'   => 'clap',
        );
    
        $this->midiInstrumentID = array (
            '1'     => 'piano',
            '2'     => 'honkytonk',
            '4'     => 'celesta',
            '5'     => 'drawbar organ',
            '6'     => 'harpsichord',
            '7'     => 'clavi',
            '8'     => 'vibraphone',
            '9'     => 'glockenspiel',
            '010'   => 'vibraphone',
            '011'   => 'tubular bells',
            '015'   => 'marimba',
            '016'   => 'bells',
            '017'   => 'clavi',
            '018'   => 'bright piano',
            '020'   => 'smooth organ',
            '023'   => 'church organ',
            '025'   => 'harmonium',
            '027'   => 'harmonica',
            '028'   => 'electric harpsichord',
            '029'   => 'electric harpsichord 2',
            '030'   => 'plucked guitar',
            '031'   => 'plucked shamisen',
            '032'   => 'dampened guitar',
            '033'   => 'plucked harpsichord',
            '034'   => 'theater organ',
            '036'   => 'distort guitar',
            '040'   => 'plucked ?',
            '042'   => 'electro pad',
            '043'   => 'electro pad 2',
            '044'   => 'robot harpsichord',
            '046'   => 'funky chord pad',
            '049'   => 'dreamy pad',
            '054'   => 'string ensemble',
            '055'   => 'plucked strings',
            '056'   => 'harp',
            '057'   => 'timpani',
            '058'   => '?',
            '060'   => 'string ensemble 2',
            '065'   => 'voice doo',
            '068'   => 'harpsichord',
            '069'   => 'harpsichord 2',
            '070'   => 'brass',
            '071'   => 'harder brass',
            '072'   => 'tuba',
            '073'   => 'nasal brass',
            '074'   => 'brass ensemble',
            '075'   => 'trumpet ensemble',
            '098'   => 'electro bass',   
        );   
    }
    
    
    public function getInstrumentation($musicStyleKey) {
        # Find what instruments are needed for music style
        if ($this->musicStyleTable[$musicStyleKey] === false) {
            $this->errorMessage = 'No chords progressions found for chosen style of music.';
            return false;
        } else {
            $instrumentTypeArray = $this->musicStyleTable[$musicStyleKey];
        }
        
        # Find a type of instrument for each part
        $instrumentArray = $this->getInstrumentForPart ($instrumentTypeArray);
    
        return $instrumentArray;
    }
    
    
    public function getRandomEntryFromArray ($array) {
        $length = count ($array);
        $randomChoice = mt_rand (0, $length-1);
        return $array[$randomChoice];
    }
    
    
    public function getInstrumentForPart ($instrumentTypeArray) {
        foreach ($instrumentTypeArray as $instrumentType) {
            # Get random instrument style from corresponding array of choices
            if (!$instrumentType === false) {
                switch ($instrumentType) {
                    case 'melody':
                        $instrumentArray[0] = $this->getRandomEntryFromArray($this->melodyInstruments);
                        break;
                    case 'pad':
                        $instrumentArray[1] = $this->getRandomEntryFromArray($this->padInstruments);
                        break;
                    case 'accent':
                        $instrumentArray[2] = $this->getRandomEntryFromArray($this->accentInstruments);
                        break;
                    case 'bass':
                        $instrumentArray[3] = $this->getRandomEntryFromArray($this->bassInstruments);
                        break;
                    case 'clap':
                        $instrumentArray[4] = $this->getRandomEntryFromArray($this->clapInstruments);
                        break;
                    case 'drumset':
                        $instrumentArray[5] = $this->getRandomEntryFromArray($this->drumsetInstruments);
                        break;
                    default:
                        echo '';
                        break;
                }
                
            }
        }
        
        # Swap instruments for their ID's
        foreach ($instrumentArray as &$instrumentName) {
            $instrumentName = array_search($instrumentName, $this->midiInstrumentID);
        }
        return $instrumentArray;
    }


}
    
?>