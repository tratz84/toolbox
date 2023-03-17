<?php


namespace base\model;


class UserGroup extends base\UserGroupBase {
    
    protected $children = array();
    

	public function __construct($id=null) {
		parent::__construct( $id );
		
	}
	
	
	public function getChildren() {
	    // TODO: sort?
	    return $this->children;
	}
	public function setChildren($c) { $this->children = $c; }
	public function addChild( $c ) { $this->children[] = $c; }
	
	public function hasChildren() {
	    return count($this->children) > 0 ? true : false;
	}
	
	
}

