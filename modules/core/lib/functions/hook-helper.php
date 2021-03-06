<?php



use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use core\event\EventBus;
use core\event\PeopleEvent;
use core\template\HtmlScriptLoader;
use core\event\VariableFilter;
use core\container\ObjectHookCall;

function hook_create_object($className, $callback) {
    $eb = ObjectContainer::getInstance()->get( EventBus::class );
    
    $eb->subscribe('core', 'create-'.$className, new CallbackPeopleEventListener(function(PeopleEvent $evt) use ($callback) {
        $callback( $evt->getSource() );
    }));
    
}

/**
 * hook_object() - proxy pattern for ObjectHookable-objects. Called after actual function is called, in current transaction
 * 
 * @param $className - classname of object
 * @param $function - function name
 * @param $callback - callback function to execute, signature: function clbck($obj, $result, $functionArguments) { ... }
 */
function hook_object($className, $function, $callback) {
    $eb = ObjectContainer::getInstance()->get( EventBus::class );
    
    $eb->subscribe('core', 'post-call-'.$className.'::'.$function, new CallbackPeopleEventListener(function(PeopleEvent $evt) use ($callback) {
        /**
         * @var ObjectHookCall $ohc
         */
        $ohc = $evt->getSource();
        
        $callback( $ohc );
    }));
}



function hook_eventbus_subscribe($moduleName, $actionName, $callback) {
    $oc = ObjectContainer::getInstance();
    $eb = $oc->get(\core\event\EventBus::class);
    
    $eb->subscribe($moduleName, $actionName, new CallbackPeopleEventListener(function($evt) use ($callback) {
        $src = $evt->getSource();
        
        $callback( $src );
    }));
}

function hook_eventbus_publish($source, $moduleName, $actionName, $message=null) {
    $oc = ObjectContainer::getInstance();
    $eb = $oc->get(\core\event\EventBus::class);
    
    return $eb->publishEvent($source, $moduleName, $actionName, $message);
}



function hook_register_css($groupName, $cssFile, $opts = array()) {
    $oc = ObjectContainer::getInstance();
    $hsl = $oc->get(HtmlScriptLoader::class);
    
    $hsl->registerCss($groupName, $cssFile, $opts);
}

function hook_register_javascript($groupName, $jsFile, $opts = array()) {
    $oc = ObjectContainer::getInstance();
    $hsl = $oc->get(HtmlScriptLoader::class);
    
    $hsl->registerJavascript($groupName, $jsFile, $opts);
}

function hook_htmlscriptloader_enableGroup($groupName) {
    $oc = ObjectContainer::getInstance();
    $hsl = $oc->get(HtmlScriptLoader::class);
    
    $hsl->enableGroup( $groupName );
}

function hook_add_inline_css( $cssText ) {
    $oc = ObjectContainer::getInstance();
    $hsl = $oc->get(HtmlScriptLoader::class);
    
    $hsl->addInlineCss( $cssText );
}

function hook_loader($folder, $opts=array()) {
    $cnt=0;
    
    $dh = opendir($folder);
    if (!$dh) {
        return $cnt;
    }
    
    $files = array();
    while($f = readdir($dh)) {
        if (strrpos($f, '.php') === strlen($f)-4 && preg_match('/^\\d+-/', $f)) {
            $files[] = $f;
        }
    }
    closedir($dh);
    
    
    usort($files, function($o1, $o2) {
        $n1 = (int)substr($o1, 0, strpos($o1, '-'));
        $n2 = (int)substr($o2, 0, strpos($o2, '-'));
        
        return $n1-$n2;
    });
    
    foreach($files as $f) {
        load_php_file($folder . '/' . $f);
    }
}

/**
 * @param unknown $className
 * @return $className
 */
function object_container_get($className) {
    return ObjectContainer::getInstance()->get($className);
}

// TODO: parameter support, class can contain constructor..
function object_container_create($className) {
    return ObjectContainer::getInstance()->create($className);
}


function add_filter($filterName, $callback, $prio=10) {
    $vf = object_container_get(VariableFilter::class);
    
    return $vf->addFilter($filterName, $callback, $prio);
}

/**
 * $filterName - parameter = name of filter
 * $variable   - variable to filter
 * 
 * @return mixed
 */
function apply_filter($filterName, $value, $opts=array()) {
    $vf = object_container_get(VariableFilter::class);
    
    return $vf->applyFilter($filterName, $value, $opts);
}


