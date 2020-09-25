<?php


namespace core\filter;

use core\Context;
use core\exception\ContextNotFoundException;

class SessionFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        // determine current contextName
        
        if (is_standalone_installation()) {
            $contextName = 'default';
        } else {
            $uri = '/'.     substr($_SERVER['REQUEST_URI'], strlen(BASE_HREF));
            
            $matches = array();
            if (preg_match('/^\\/([a-zA-Z0-9_-]+)?\\/.*/', $uri, $matches) == false || count($matches) != 2) {
                throw new ContextNotFoundException('context not found');
            }
            $contextName = $matches[1];
        }
        
        bootstrapContext( $contextName );
        
        $ctx = Context::getInstance();
        
        // start session for path
        if (is_standalone_installation()) {
            $sessionPath = BASE_HREF;
        } else {
            $sessionPath = BASE_HREF.$ctx->getContextName().'/';
        }
        session_set_cookie_params(0, $sessionPath);
        
        if (get_var('c') && (strpos(get_var('c'), 'api/') === 0 || strpos(get_var('c'), 'api/') === 0)) {
            // api-calls are stateless
            
        } else {
            $r = session_start();
            
            if ($r == false) {
                // creating session failed? this can happen on existing sessions that can't
                // access the session-files anymore on the server. Can happen when the Apache-config
                // changes and assigns other user-permissions to the php-pool or change the 
                // AssignUserID-value in the <VirtualHosting>-settings
                print get_template(module_file('core', 'templates/exception/handledError.php'), 
                    [
                        'message' => t('Error creating session')
                    ]);
                
                // session_destroy(); <= this doesn't work in this case, because there's no access to the session-file
                // unset PHPSESSID-cookie
                setcookie(session_name(), null, 0, $sessionPath);
                
                exit;
            }
        }
        
        $filterChain->next();
    }
    
}