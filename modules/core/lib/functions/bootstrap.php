<?php


use core\Context;
use core\exception\InvalidStateException;
use admin\model\Customer;
use admin\model\ExceptionLog;
use core\filter\FilterChain;

function bootstrapContext($contextName) {
    
    if (is_standalone_installation()) {
        $customer = new Customer();
        $customer->setContextName('default');
        $customer->setDatabaseName(DEFAULT_DATABASE_NAME);
        $customer->setActive(true);
        $customer->setExperimental(true);
    } else {
        $contextService = new \admin\service\ContextService();
        $customer = $contextService->readCustomerContext( $contextName );
    }
    
    if ($customer == null)
        throw new InvalidStateException('customer not found');
        
    $ctx = Context::getInstance();
    $ctx->setCustomer($customer);
    $ctx->setContextName($contextName);
    
    $autoloadfile = ROOT . '/context/'.$contextName.'/autoload.php';
    
    if (file_exists($autoloadfile)) {
        load_php_file($autoloadfile);
    }
    
}


function bootstrapCli($contextName) {
    bootstrapContext($contextName);
    
    set_exception_handler(function($ex) {
        
        try {
            $ctx = \core\Context::getInstance();
            
            $cn = $ctx->getContextName();
            
            // report error?
            if (function_exists('debug_admin_notification'))
                debug_admin_notification('Error: ' . $cn . ': ' . $ex->getMessage());
            
            // save exception
            $el = new ExceptionLog();
            $el->setContextName($ctx->getContextName());
            if ($ctx->getUser())
                $el->setUserId($ctx->getUser()->getUserId());
            
            // should not be set, because it's a CLI-script, but you never know how it's gonna be used in the future
            if (isset($_SERVER['REQUEST_URI'])) {
                $el->setRequestUri(substr($_SERVER['REQUEST_URI'], 0, 255));
            } else if (isset($_SERVER['SCRIPT_NAME'])) {
                if (realpath($_SERVER['SCRIPT_NAME'])) {
                    $script_name = 'CLI:'.substr(realpath($_SERVER['SCRIPT_NAME']), strlen(ROOT));
                } else {
                    $script_name = 'CLI:'.$_SERVER['SCRIPT_NAME'];
                }
                $el->setRequestUri(substr($script_name, 0, 255));
            }
            $el->setMessage($ex->getMessage());
            
            $stacktrace = '';
            if (is_a($ex, \core\exception\DatabaseException::class)) {
                $stacktrace .= 'Query: '.$ex->getQuery() . "\n\n";
            }
            $stacktrace .= $ex->getFile() . ' ('.$ex->getLine().')' . "\n";
            $stacktrace .= $ex->getTraceAsString();
            $el->setStacktrace($stacktrace);
            $el->setParameters(var_export($_REQUEST, true));
            $el->save();
            
        } catch (\Exception $ex2) {
            // no way to handle
        } catch (\Error $err) {
            // no way to handle
        }
        
       throw $ex;
    });
    
    $fc = new FilterChain();
    $fc->addFilter( new \core\filter\DatabaseFilter() );
    $fc->addFilter( new \core\filter\ModuleEnablerFilter() );
    
    $fc->execute();
    
}


/**
 * is_installation_mode() - generate config-local.php?
 */
function is_installation_mode() {
    if (defined('INSTALLATION_MODE') && INSTALLATION_MODE) {
        return true;
    } else {
        return false;
    }
}


/**
 * is_standalone_installation() - single installation with only one default context?
 * 
 * @return boolean
 */
function is_standalone_installation() {
    if (defined('STANDALONE_INSTALLATION') && STANDALONE_INSTALLATION) {
        return true;
    } else {
        return false;
    }
}


