<?php




use core\Context;


function t_loadlang() {
    $lang = array();
    
    $modules = Context::getInstance()->getEnabledModules();
    foreach($modules as $m) {
        $p = module_path( $m ) . "/lang/nl_NL.php";
        if (file_exists($p)) {
            $lang_module = load_php_file($p);
            if (is_array($lang_module)) {
                $lang = array_merge($lang, $lang_module);
            }
        }
    }
    
    return $lang;
}


function t($str) {
    static $lang = null;
    
    if ($lang === null) {
        $lang = t_loadlang();
    }
    
    if (array_key_exists($str, $lang)) {
        return $lang[$str];
    } else {
        return $str;
    }
}

function t_lc($str) {
    return strtolower(t($str));
}



function strOrder($no) {
    static $orderType = null;
    if ($orderType === null) {
        $orderType = Context::getInstance()->getSetting('invoice__orderType');
    }
    
    if ($orderType == 'invoice') {
        switch ($no) {
            case 2 :
                return 'Facturen';
            case 3 :
                return 'Facturatie';
            case 1 :
            default :
                return 'Factuur';
        }
    } else {
        switch ($no) {
            case 2 :
                return 'Orders';
            case 3 :
                return 'Orders';
            case 1 :
            default :
                return 'Order';
        }
        
    }
    
}

