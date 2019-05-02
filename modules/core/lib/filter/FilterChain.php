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
		
		$this->next();
	}
	
	public function next() {
		if ($this->currentFilter-1 >= 0 && $this->currentFilter-1 < count($this->filters)) {
			hook_eventbus_publish($this->filters[$this->currentFilter-1], 'core', 'filter-executed');
		}
		
		if ($this->currentFilter < count($this->filters)) {
			$filterNo = $this->currentFilter;
			$this->currentFilter++;
			$this->filters[$filterNo]->doFilter($this);
		}
	}
	
	
}