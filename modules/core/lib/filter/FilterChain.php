<?php

namespace core\filter;


class FilterChain {
	
	
	protected $filters = array();
	
	protected $currentFilter = 0;
	protected $executeCalled = false;
	
	public function __construct() {
	    
	}
	
	public function addFilter($filter) {
		$this->filters[] = $filter;
	}
	
	
	public function execute() {
		if ($this->executeCalled)
			throw new \Exception('FilterChain::execute() called twice');
		
		$this->executeCalled = true;
		
		if (count($this->filters) == 0)
			throw new \Exception('No filters');
		
		$this->filters[$this->currentFilter++]->doFilter($this);
	}
	
	public function next() {
		if ($this->currentFilter < count($this->filters))
			$this->filters[$this->currentFilter++]->doFilter($this);
	}
	
	
}