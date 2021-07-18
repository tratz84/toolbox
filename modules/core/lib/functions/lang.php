<?php




use core\Context;


function t_loadlang() {
    static $lang = null;
    
    if ($lang === null) {
        $lang = array();
        $selectedLang = Context::getInstance()->getSelectedLang();
        
        $modules = Context::getInstance()->getEnabledModules();
        foreach($modules as $m) {
            $langPath = realpath( module_path( $m ) . "/lang/" );
            $p = realpath( module_path( $m ) . "/lang/".$selectedLang.".php" );
            if ($p && strpos($p, $langPath) === 0) {
                $lang_module = load_php_file($p);
                if (is_array($lang_module)) {
                    $lang = array_merge($lang, $lang_module);
                }
            }
        }
        
        $lang = apply_filter('lang', $lang);
    }
    
    return $lang;
}

function tf($str) {
    $arguments = func_get_args();
    
    $str = t( $arguments[0] );
    
    // skip first argument
    $params = array_splice($arguments, 1);
    
    // format & return
    return vsprintf($str, $params);
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


/**
 * strOrder() - returns right naming for order/invoice
 *   TODO: move this to invoice-module
 */
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

