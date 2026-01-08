<?php




use core\exception\InvalidStateException;

function sm_default_cipher() {
    static $selected_cipher = null;
    
    if ($selected_cipher != null)
        return $selected_cipher;
    
    $ciphers = openssl_get_cipher_methods();
    
    if (in_array('aes-256-cbc', $ciphers)) {
        $selected_cipher = 'aes-256-cbc';
    }
    
    if ($selected_cipher == null) {
        foreach($ciphers as $c) {
            if (strpos($c, '-cbc') !== false) {
                $selected_cipher = $c;
                break;
            }
        }
    }
    
    return $selected_cipher;
}

function sm_random_password($len=64) {
    $tokens = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-+=';
    
    $len = strlen($tokens);
    
    $t = '';
    for($x=0; $x < $len; $x++) {
        $p = rand(0, $len-1);
        
        $t .= $tokens[$p];
    }
    
    return $t;
}


function sm_encrypt_message( $message, $pass=null, $opts=array() ) {
    $cipher = sm_default_cipher();
    
    if ( $cipher == null ) {
        throw new InvalidStateException( 'No valid cipher found' );
    }
    
    if ($pass == null) {
        $pass = sm_random_password(10);
    }
    
    
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    
    $tag = isset($opts['tag']) ? $opts['tag'] : null;
    
    $ciphertext = openssl_encrypt($message, $cipher, $pass, 0, $iv, $tag);
    
    // all info to decrypt
    return [
        'iv'         => $iv,
        'password'   => $pass,
        'cipher'     => $cipher,
        'ciphertext' => $ciphertext
    ];
}

function sm_decrypt_message( $pass, $iv, $ciphertext, $opts=array() ) {
    if (isset($opts['cipher']) && $opts['cipher']) {
        $cipher = $opts['cipher'];
    }
    else {
        $cipher = sm_default_cipher();
    }
    if ( $cipher == null ) {
        throw new InvalidStateException( 'No valid cipher found' );
    }
    
    $tag = isset($opts['tag']) ? $opts['tag'] : null;
    
    $original_plaintext = openssl_decrypt($ciphertext, $cipher, $pass, 0, $iv, $tag);
    
    return $original_plaintext;
}











