<?php

namespace core\filter;


use core\container\ObjectHookable;

class FilterChain implements ObjectHookable {
	
	
	protected $filters = array();
	
	protected $currentFilter = 0;
	protected $executeCalled = false;
	
	public function __construct() {
	    
	}
	
	public function removeFilterNo($no) {
	    $newFilters = array();
	    
	    for($x=0; $x < count($this->filters); $x++) {
	        if ($x == $no) {
	            continue;
	        }
	        
	        $newFilters[] = $this->filters[$x];
	    }
	    
	    $this->filters = $newFilters;
	}
	
	public function clearFilters() { $this->filters = array(); }
	
	public function addFilter($filter) {
		$this->filters[] = $filter;
	}
	
	
	public function execute() {
		if ($this->executeCalled)
			throw new \Exception('FilterChain::execute() called twice');
		
		$this->executeCalled = true;
		
		if (count($this->filters) == 0)
			throw new \Exception('No filters');
		
		$this->next();
	}
	
	public function next() {
		if ($this->currentFilter-1 >= 0 && $this->currentFilter-1 < count($this->filters)) {
			hook_eventbus_publish($this->filters[$this->currentFilter-1], 'core', 'filter-executed');
		}
		
		if ($this->currentFilter < count($this->filters)) {
			$filterNo = $this->currentFilter;
			$this->currentFilter++;
			
			hook_eventbus_publish($this->filters[$filterNo], 'core', 'pre-filter-executed');
			
			$this->filters[$filterNo]->doFilter($this);
		}
	}
	
	
}