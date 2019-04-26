<?php



function generateName() {
    
    $p1 = array('a', 'e', 'i', 'o', 'u', 'y');
    $p2 = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'z');
    
    $uglyWords = array('kut', 'sex', 'tyfus', 'tifis');
    
    if (rand(0, 2) == 0) {
        $c = array();
        $c[] = $p1[ rand(0, count($p1)-1) ];
        $c[] = $p2[ rand(0, count($p2)-1) ];
        $c[] = $p1[ rand(0, count($p1)-1) ];
        $c[] = $p2[ rand(0, count($p2)-1) ];
        $c[] = $p1[ rand(0, count($p1)-1) ];
        $c[] = $p2[ rand(0, count($p2)-1) ];
    } else if (rand(0, 1) == 2) {
        $c[] = $p2[ rand(0, count($p2)-1) ];
        $c[] = $p1[ rand(0, count($p1)-1) ];
        $c[] = $p1[ rand(0, count($p1)-1) ];
        $c[] = $p2[ rand(0, count($p2)-1) ];
        $c[] = $p1[ rand(0, count($p1)-1) ];
        $c[] = $p2[ rand(0, count($p2)-1) ];
    } else {
        $c = array();
        $c[] = $p1[ rand(0, count($p1)-1) ];
        $c[] = $p1[ rand(0, count($p1)-1) ];
        $c[] = $p2[ rand(0, count($p2)-1) ];
        $c[] = $p1[ rand(0, count($p1)-1) ];
        $c[] = $p2[ rand(0, count($p2)-1) ];
        $c[] = $p1[ rand(0, count($p1)-1) ];
    }
    
    $code = ucfirst( implode('', $c) );
    
    return $code;
}

function randA2Z() {
    $r = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    
    return $r[ rand(0, count($r)-1) ];
}
