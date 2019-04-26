<?php

namespace core\filter;

use core\Context;


class DispatchFilter {
	
	public function __construct() {
		
	}
	
	
	public function doFilter($filterChain) {
		$ctx = Context::getInstance();
		
		
		self::dispatch($ctx->getController());
		
		$filterChain->next();
	}
	
	public static function dispatch() {
	    $ctx = Context::getInstance();
	    
	    
	    include_component($ctx->getModule(), $ctx->getController(), $ctx->getAction(), array('showDecorator' => true));
		
	}
	
}

