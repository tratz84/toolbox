<?php


function base_url($url) {
    $pos1 = strpos($url, '://');
    if ($pos1 !== false) {
        $pos1 += 3;
    } else {
        $pos1 = 0;
    }
    $pos2 = strpos($url, '/', $pos1);
    
    if ($pos2 !== false) {
        $url = substr($url, 0, $pos2);
    }
    
    return $url . '/';
}


function get_url($url, $opts=array()) {
    $ch = curl_init($url);
    
    if (isset($opts['headers'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $opts['headers']);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if (isset($opts['timeout'])) {
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $opts['timeout']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $opts['timeout']);
    }
    
    return curl_exec($ch);
}

function post_url($url, $data, $opts=array()) {
    $ch = curl_init($url);
    
    if (isset($opts['headers'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $opts['headers']);
    }
    
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $r = curl_exec($ch);
    
    curl_close($ch);
    
    return $r;
}
