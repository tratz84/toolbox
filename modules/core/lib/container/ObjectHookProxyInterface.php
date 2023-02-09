<?php

namespace core\container;


interface ObjectHookProxyInterface {
    
    public function proxy_addFilter( $func_filter );
    
    public function proxy_executeFunction();
    
}
