<?php


namespace admin\filter;

use core\Context;

class AdminRouteFilter {


	protected $fixedRoutes = array();


	public function __construct() {

	}

	public function doFilter($filterChain) {

	    $ctx = Context::getInstance();
	    
        $ctx->setModule('admin');
	    if ($controller = $ctx->getVar('c')) {
	        $ctx->setController( $controller );
	    }
	    
	    if ($action = $ctx->getVar('a')) {
	        $ctx->setAction($action);
	    }
	    
		$filterChain->next();
	}


}
