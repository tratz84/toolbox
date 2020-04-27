<?php


use core\ObjectContainer;
use core\container\TabContainer;
use core\event\EventBus;
use core\template\DefaultTemplate;
use core\template\HtmlScriptLoader;
use core\security\AuthorizationCheck;
use core\exception\AuthorizationException;

function esc_html($str) {
    return htmlentities($str, ENT_COMPAT, 'UTF-8');
}

function esc_attr($str) {
    return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

function esc_json_attr($str) {
    return htmlentities(json_encode($str), ENT_QUOTES, 'UTF-8');
}


function infopopup($t) {
    if ($t == null) return '';
    
    $html = '';
    
    $html .= '<span class="info-popup">';
    $html .= '<span class="fa fa-info"></span>';
    $html .= '<div class="info-popup-text">'.$t.'</div>';
    $html .= '</span>';
    
    return $html;
}


function report_user_message($msg) {
    if (isset($_SESSION['user_errors']) == false) {
        $_SESSION['user_message'] = array();
    }
    
    if (is_array($msg)) foreach($msg as $m) {
        $_SESSION['user_message'][] = $m;
    } else {
        $_SESSION['user_message'][] = $msg;
    }
}

function output_user_messages() {
    if (isset($_SESSION['user_message']) && is_array($_SESSION['user_message']) && count($_SESSION['user_message'])) {
        
        foreach($_SESSION['user_message'] as $e) {
            print '<div class="global-message alert alert-success">';
            print '<div>'.esc_html($e).'</div>';
            print '</div>';
        }
        
    }
    
    if (isset($_SESSION['user_message']))
        unset($_SESSION['user_message']);
}

function report_user_error($msg) {
    if (isset($_SESSION['user_errors']) == false) {
        $_SESSION['user_errors'] = array();
    }
    
    if (is_array($msg)) foreach($msg as $m) {
        $_SESSION['user_errors'][] = $m;
    } else {
        $_SESSION['user_errors'][] = $msg;
    }
}

function output_user_errors() {
    if (isset($_SESSION['user_errors']) && is_array($_SESSION['user_errors']) && count($_SESSION['user_errors'])) {
        
        foreach($_SESSION['user_errors'] as $e) {
            print '<div class="alert alert-danger">';
            print '<div>'.esc_html($e).'</div>';
            print '</div>';
        }
        
    }
    
    if (isset($_SESSION['user_errors']))
        unset($_SESSION['user_errors']);
}


function print_htmlScriptLoader_inlineCss() {
    $hsl = ObjectContainer::getInstance()->get(HtmlScriptLoader::class);
    $css = $hsl->getInlineCss();
    if ($css) {
        print "\n<style type=\"text/css\">\n".$css."\n</style>\n";
    }
}

function print_htmlScriptLoader_top() {
    $hsl = ObjectContainer::getInstance()->get(HtmlScriptLoader::class);
    $hsl->printCss('top');
    $hsl->printJavascript('top');
}

function print_htmlScriptLoader_bottom() {
    $hsl = ObjectContainer::getInstance()->get(HtmlScriptLoader::class);
    $hsl->printCss('bottom');
    $hsl->printJavascript('bottom');
}


function include_component($module, $controller, $action, $vars=array()) {
    
    $user = ctx()->getUser();
    if ($user && in_array($user->getUserType(), ['admin', 'user']) == false) {
        $ac = new AuthorizationCheck( $user );
        $ac->setModule($module);
        $ac->setController($controller);
        $ac->setAction($action);
        
        $ac->checkAuthorization();
    }
    
    $oc = \core\ObjectContainer::getInstance();
    $controllerInstance = $oc->getController($module, $controller);
    
    foreach($vars as $key => $val) {
        $func = 'set'.ucfirst($key);
        
        if (method_exists($controllerInstance, $func)) {
            call_user_func_array(array($controllerInstance, $func), array($val));
        } else {
            $controllerInstance->$key = $val;
        }
    }
    
    $controllerInstance->setActionTemplate($action);
    
    if (method_exists($controllerInstance, 'handle_action')) {
        // publish event
        hook_eventbus_publish($controllerInstance, $module, 'include-component');
        
        $controllerInstance->handle_action();
    } else {
        // check if action exists
        if (method_exists($controllerInstance, 'action_'.$action) == false)
            throw new \Exception('Action doesn\'t exist, ' . $controller . '::action_' . $action);

        // publish event
        hook_eventbus_publish($controllerInstance, $module, 'include-component-'.$action);
        
        // call
        $controllerInstance->{'action_'.$action}();
    }
}

function get_component($module, $controller, $action, $vars=array()) {
    ob_start();
    
    include_component($module, $controller, $action, $vars);
    
    return ob_get_clean();
}

function get_template($file, $vars=array()) {
    foreach($vars as $key => $val) {
        $$key = $val;
    }
    
    ob_start();
    include $file;
    
    return ob_get_clean();
}



function include_footer_tabs($moduleName, $actionName, $source) {
    $ftc = generate_tabs($moduleName, $actionName, $source);
    
    $ftc->render();
}

function generate_tabs($moduleName, $actionName, $source) {
    
    $oc = ObjectContainer::getInstance();
    /**
     * @var EventBus $eventBus
     */
    $eventBus = $oc->get(EventBus::class);
    
    $ftc = new TabContainer($source);
    
    $eventBus->publishEvent($ftc, $moduleName, $actionName);
    
    return $ftc;
}

function explode_attributes($keys) {
    $h = '';
    
    foreach($keys as $key => $value) {
        $h .= $key .'="'. esc_attr($value).'" ';
    }
    
    return $h;
}


function apply_html_vars($html, $vars) {
    foreach($vars as $key => $val) {
        $html = str_replace('[['.$key.']]', $val, $html);
    }
    
    return $html;
}



function show_error($msg) {
    $tpl = new DefaultTemplate(ROOT . '/modules/base/templates/decorator/error.php');
    $tpl->setVar('content', '<h1>Fout opgetreden</h1>'.$msg);
    $tpl->showTemplate();
    
    exit;
}


function generate_safe_html($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    
    
    $body = $dom->getElementsByTagName('body');

    if (count($body) > 0) {
        _generate_safe_html_filter( $body[0] );
    } else {
        return '';
    }
    
    return $dom->saveHTML($body[0]);
}

function _generate_safe_html_filter($node) {
    $allowedNodes = array('html', 'body', 'a', 'b', 'i', 'font', 'img', 'table', 'thead', 'tbody', 'tfooter', 'tr', 'td', 'span', 'div', 'p', 'strong', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'h7' ,'h8', '#text', 'br');
    $allowedAttributes = array('href', 'width', 'height', 'id', 'align', 'valign', 'alt', 'title', 'border', 'cellspacing', 'cellpadding', 'colspan');
    $allowedAttributes[] = 'style'; 
    
//     $allowedAttributes[] = 'src';
//     $allowedAttributes[] = 'class';
    
    
    for($x=count($node->childNodes)-1; $x >= 0; $x--) {
        $cn = $node->childNodes[$x];
        
        if (in_array($cn->nodeName, $allowedNodes) == false) {
            $node->removeChild($cn);
            continue;
        }
        
        if ($cn->hasAttributes()) {
            $attrs = $cn->attributes;
            
            for($y=$attrs->count()-1; $y >= 0; $y--) {
                $attributeName = $attrs->item($y)->name;
                
                if (in_array($attributeName, $allowedAttributes) == false) {
                    $cn->removeAttribute( $attributeName );
                }
            }
        }
        
        // always open anchors in new window
        if ($cn->nodeName == 'a') {
            $cn->setAttribute('target', '_blank');
        }
        
        
        if ($cn->hasChildNodes()) {
            _generate_safe_html_filter($cn);
        }
    }
    
}


function valid_rgbhex($hexstr) {
    if (preg_match('/^[0-9a-fA-F]{3}$/', $hexstr) || preg_match('/^[0-9a-fA-F]{6}$/', $hexstr)) {
        return true;
    } else {
        return false;
    }
}

function hex2rgb($hexstr) {
    
    if (strpos($hexstr, '#') === 0)
        $hexstr = substr($hexstr, 1);
    
    if (preg_match('/^[0-9a-fA-F]{3}$/', $hexstr)) {
        $p1 = $hexstr[0].$hexstr[0];
        $p2 = $hexstr[1].$hexstr[1];
        $p3 = $hexstr[2].$hexstr[2];
        
        return array(hexdec($p1), hexdec($p2), hexdec($p3));
    } else if (preg_match('/^[0-9a-fA-F]{6}$/', $hexstr)) {
        $p1 = $hexstr[0].$hexstr[1];
        $p2 = $hexstr[2].$hexstr[3];
        $p3 = $hexstr[4].$hexstr[5];
        
        return array(hexdec($p1), hexdec($p2), hexdec($p3));
    }
    
    return null;
}


function hex_inc_perc($hexstr, $perc=null) {
    $rgb = hex2rgb($hexstr);
    
    if ($rgb == null)
        return null;
    

    $rgb[0] += ((255-$rgb[0]) / 100 * $perc);
    $rgb[1] += ((255-$rgb[1]) / 100 * $perc);
    $rgb[2] += ((255-$rgb[2]) / 100 * $perc);
    
    if ($rgb[0] > 255)
        $rgb[0] = 255;
    if ($rgb[1] > 255)
        $rgb[1] = 255;
    if ($rgb[2] > 255)
        $rgb[2] = 255;
    
    $x = sprintf('#%2x%2x%2x', $rgb[0], $rgb[1], $rgb[2]);
    $x = str_replace(' ', '0', $x);
    return $x;
}


function toolbox_html2pdf_available() {
    if (defined('PATH_WKHTMLTOPDF') && file_exists(PATH_WKHTMLTOPDF)) {
        return PATH_WKHTMLTOPDF;
    }
    
    return which_exec('wkhtmltopdf');
}

function toolbox_html2pdf( $html ) {
    $exec = toolbox_html2pdf_available();
    if (!$exec) {
        return null;
    }
    
    list($return_value, $stdout) = exec_return($exec . ' - -', $html);
    
    // TODO: check $return_value?
//     if ($return_value != 1) {
//         return null;
//     }
    
    return $stdout;
}





