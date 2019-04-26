<?php


namespace core\filter;

use core\Context;

class RouteFilter {


	protected $fixedRoutes = array();


	public function __construct() {

	}

	public function doFilter($filterChain) {

	    $ctx = Context::getInstance();
	    
	    if ($module = $ctx->getVar('m')) {
	        $ctx->setModule($module);
	    }
	    
	    if ($controller = $ctx->getVar('c')) {
	        $ctx->setController( $controller );
	    }
	    
	    if ($action = $ctx->getVar('a')) {
	        $ctx->setAction($action);
	    }
	    
		$filterChain->next();
	}


}
