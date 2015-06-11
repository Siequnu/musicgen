<?php

class harmonyCatalog {

    # Construct an index
    public function __construct() {

        $this->harmonyIndex = array(
            'C'        => array ('c', 'e', 'g', 'c'),
            'C763'     => array ('e', 'd', 'g', 'c'),
            'c'        => array ('c', 'd#', 'g', 'c'),
            'c7'       => array ('c', 'd#', 'g', 'bb'),
            'C6'       => array ('e', 'g', 'c', 'g'),
            'D'        => array ('d', 'f#', 'a', 'd'),
            'Db'       => array ('c#', 'f', 'g#', 'c#'),
            'ddim7'    => array ('d', 'f', 'g#', 'c'),
            'ddim56'   => array ('f', 'g#', 'b', 'd'),
            'd'        => array ('d', 'f', 'a', 'd'),
            'd65'      => array ('f', 'd', 'a', 'd'),
            'D6'       => array ('f#', 'a', 'd', 'a'),
            'd7'       => array ('d', 'f', 'a', 'c'),
            'E'        => array ('e', 'g#', 'b', 'e'),
            'em7'      => array ('e', 'g', 'b', 'd'),
            'Eb'       => array ('d#', 'g', 'bb', 'd#'),
            'Eb6'      => array ('bb', 'd#', 'g', 'd#'),
            'e'        => array ('e', 'g', 'b', 'e'),
            'E6'       => array ('g#', 'e', 'b', 'e'),
            'e'        => array ('e', 'g', 'b', 'e'),
            'F'        => array ('f', 'a', 'c', 'f'),
            'F6'       => array ('a', 'c', 'f', 'c'),
            'F64'      => array ('c', 'f', 'a', 'c'),
            'f'        => array ('f', 'g#', 'c', 'f'),
            'f#dim'    => array ('f#', 'a', 'c', 'f#'),
            'f#dim6'   => array ('a', 'c', 'f#', 'c'),
            'G'        => array ('g', 'b', 'd', 'g'),
            'g'        => array ('g', 'bb', 'd', 'g'),
            'Gb'       => array ('f#', 'bb', 'c#', 'f#'),
            'G6'       => array ('b', 'd', 'g', 'd'),
            'gdim6'    => array ('bb', 'c#', 'g', 'c#'),
            'ghalfdim' => array ('g', 'bb', 'c#', 'f'),
            'a'        => array ('a', 'c', 'e', 'a'),
            'adim7'    => array ('a', 'c', 'd#','g'),
            'am'       => array ('a', 'c', 'e', 'a'),
            'Ab'       => array ('g#', 'c', 'd#', 'g#'),
            'A6'       => array ('c#', 'e', 'a', 'a'),
            'Ab6'      => array ('c', 'd#', 'g#', 'd#'),
            'bdim'     => array ('b', 'd', 'f', 'b'),
            'bdim7'    => array ('b', 'd', 'f', 'a'),
            'bdim56'   => array ('d', 'f', 'a', 'b'),
            'bhalfdim' => array ('b', 'd', 'f', 'g#'),
            'B6'       => array ('d', 'f#', 'b', 'b'),
            'Bb6'      => array ('d', 'f', 'bb', 'f'),
            'Bb'       => array ('bb', 'd', 'f', 'bb'),
            'bb'       => array ('bb', 'c#', 'f', 'bb'),

        );

        $this->keyboardLayout = array(
            '0'  => 'c',
            '1'  => 'c#',
            '2'  => 'd',
            '3'  => 'd#',
            '4'  => 'e',
            '5'  => 'f',
            '6'  => 'f#',
            '7'  => 'g',
            '8'  => 'g#',
            '9'  => 'a',
            '10' => 'bb',
            '11' => 'b',
            '12' => 'c',
            '13' => 'c#',
            '14' => 'd',
            '15' => 'd#',
            '16' => 'e',
            '17' => 'f',
            '18' => 'f#',
            '19' => 'g',
            '20' => 'g#',
            '21' => 'a',
            '22' => 'bb',
            '23' => 'b',
            '24' => 'c',
            '25' => 'c#',
            '26' => 'd',
            '27' => 'd#',
            '28' => 'e',
            '29' => 'f',
            '30' => 'f#',
            '31' => 'g',
            '32' => 'g#',
            '33' => 'a',
            '34' => 'bb',
            '35' => 'b',
            '36' => 'c',
            '37' => 'c#',
            '38' => 'd',
            '39' => 'd#',
            '40' => 'e',
            '41' => 'f',
            '42' => 'f#',
            '43' => 'g',
            '44' => 'g#',
            '45' => 'a',
            '46' => 'bb',
            '47' => 'b',
            '48' => 'c',

        );

        $this->keyboardLayoutWithNoteNumbers = array(
            '0'  => 'c1',
            '1'  => 'c#1',
            '2'  => 'd1',
            '3'  => 'd#1',
            '4'  => 'e1',
            '5'  => 'f1',
            '6'  => 'f#1',
            '7'  => 'g1',
            '8'  => 'g#1',
            '9'  => 'a1',
            '10' => 'bb1',
            '11' => 'b1',
            '12' => 'c2',
            '13' => 'c#2',
            '14' => 'd2',
            '15' => 'd#2',
            '16' => 'e2',
            '17' => 'f2',
            '18' => 'f#2',
            '19' => 'g2',
            '20' => 'g#2',
            '21' => 'a2',
            '22' => 'bb2',
            '23' => 'b2',
            '24' => 'c3',
            '25' => 'c#3',
            '26' => 'd3',
            '27' => 'd#3',
            '28' => 'e3',
            '29' => 'f3',
            '30' => 'f#3',
            '31' => 'g3',
            '32' => 'g#3',
            '33' => 'a3',
            '34' => 'bb3',
            '35' => 'b3',
            '36' => 'c4',
            '37' => 'c#4',
            '38' => 'd4',
            '39' => 'd#4',
            '40' => 'e4',
            '41' => 'f4',
            '42' => 'f#4',
            '43' => 'g4',
            '44' => 'g#4',
            '45' => 'a4',
            '46' => 'bb4',
            '47' => 'b4',
            '48' => 'c5',
            '49' => 'c#5',
            '50' => 'd5',
            '51' => 'd#5',
            '52' => 'e5',
            '53' => 'f5',
            '54' => 'f#5',
            '55' => 'g5',
            '56' => 'g#5',
            '57' => 'a5',
            '58' => 'bb5',
            '59' => 'b5',
            '60' => 'c6',
        );

    }
}

?>