<?php

class chordSequences {
    
    
    public function getErrorMessage() {return $this->errorMessage;}
    
    
    public function __construct () { 
        $this->chordSequences = array  (
            '0'   => array ('C', 'em7', 'am', 'F'),
            '1'   => array ('C', 'C763', 'am', 'F'),
            '2'   => array ('C', 'C763', 'am', 'd65'),
            '3'   => array ('C', 'Ab', 'Eb', 'G'),
            '4'   => array ('C', 'Eb', 'Ab', 'F'),
            '5'   => array ('C', 'am', 'C6', 'G'),
            '6'   => array ('C', 'am', 'F', 'G'),
            '7'   => array ('C', 'Ab6', 'F64', 'F64'),
            '8'   => array ('C', 'G', 'am', 'F',),
        );   
    }
    
    
    public function getChordSequence($musicStyleKey) {
        # Get random chord sequence from array
        $possibilityCount = count ($this->chordSequences);
        $randomChoice = mt_rand (0, $possibilityCount-1);
        return $this->chordSequences[$randomChoice];
    }

}

?>