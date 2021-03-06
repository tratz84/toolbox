<?php


namespace core\forms\lists;

class IndexTable {
    
    protected $itVariable = 'it';
    
    protected $containerId = null;
    protected $connectorUrl;
    protected $rowClick = null;
    
    protected $opts = array();
    
    protected $columns = array();
    protected $columnPrio = 10;
    
    // call 'it.load();' ?
    protected $renderLoad = true;
    
    protected $enableColumnSelection = false;
    
    
    public function __construct() {
        $this->autoSetItVariable();
        
        $this->codegen();
    }
    
    /**
     * extra class=""-name in <table>-tag
     */
    public function setTableClass($cl) { $this->setOpt('tableClass', $cl); }
    
    /**
     * extra variables used loading IndexTable
     * used for standard filtering
     */ 
    public function setDefaultSearchOpt($key, $val) {
        if (isset($this->opts['defaultSearchOpts']) == false) {
            $this->opts['defaultSearchOpts'] = array();
        }
        
        $this->opts['defaultSearchOpts'][$key] = $val;
    }
    
    
    protected function autoSetItVariable() {
        $n = get_class($this);
        
        // ucfirst to include part of namespace
        $n = ucfirst($n);
        
        // remove all non-uppercases + 'IndexTable'-string
        $n = preg_replace('/([^A-Z]|IndexTable)/', '', $n);
        
        $n = strtolower($n);
        
        $this->setItVariable('it_'.$n);
    }
    
    
    public function setItVariable( $n ) { $this->itVariable = $n; }
    public function getItVariable( ) { return $this->itVariable; }
    
    public function setContainerId( $containerId ) {
        if (strpos($containerId, '#') !== 0)
            $containerId = '#' . $containerId;
        $this->containerId = $containerId;
    }
    public function getContainerId( ) { return $this->containerId; }
    
    public function setConnectorUrl( $connectorUrl ) { $this->connectorUrl = $connectorUrl; }
    public function getConnectorUrl( ) { return $this->connectorUrl; }
    
    public function setOpt($key, $val) { $this->opts[$key] = $val; }
    public function getOpt($k, $defaultValue = null) { return isset($this->opts[$k]) ? $this->opts[$k] : $defaultValue; }
    public function getOpts() { return $this->opts; }
    
    public function setRowClick( $js ) { $this->rowClick = $js; }
    public function getRowClick( ) { return $this->rowClick; }
    
    public function setRenderLoad( $bln ) { $this->renderLoad = $bln?true:false; }
    public function getRenderLoad( ) { return $this->renderLoad; }
    
    public function setColumn($columnName, $props) {
        if (isset($props['prio']) == false) {
            $props['prio'] = $this->columnPrio;
            $this->columnPrio += 10;
        }
        
        $this->columns[ $columnName ] = $props;
    }
    public function removeColumn( $columnName ) { unset( $this->columns[ $columnName ] ); }
    public function getColumn( $columnName ) {
        if (isset($this->columns[ $columnName ])) {
            return $this->columns[ $columnName ];
        }
        else {
            return null;
        }
    }
    public function setColumnProperty($columnName, $propName, $val) {
        $this->columns[ $columnName ][ $propName ] = $val;
    }
    
    public function enableColumnSelection() { $this->enableColumnSelection = true; }
    public function disableColumnSelection() { $this->enableColumnSelection = false; }
    
    
    protected function codegen() {
        
    }
    
    public function renderHtml() {
        
        if ($this->containerId == null) {
            $this->containerId = '#default-table';
        }
        
        $html = '';
        if ($this->enableColumnSelection) {
            $idColSelect = substr($this->getContainerId(), 1)."-column-selection";
            
            $html .= "<div id=\"".$idColSelect."\"></div>";
            $html .= '<hr/>';
            $this->setOpt('columnSelection', '#'.$idColSelect);
        }
        
        $html .= '<div id="'.substr($this->getContainerId(), 1).'"></div>';
        
        $html .= "\n\n";
        $html .= "<script type=\"text/javascript\">\n";
        $html .= $this->render();
        $html .= "</script>\n";
        
        return $html;
    }
    
    public function render() {
        $js = '';
        
        $js .= 'var '.$this->getItVariable().' = new IndexTable('.json_encode($this->getContainerId()).', '.json_encode($this->opts).');' . PHP_EOL;
        $js .= PHP_EOL;
        $js .= $this->getItVariable() . '.setConnectorUrl( '.json_encode($this->getConnectorUrl()).' );' . PHP_EOL;
        $js .= PHP_EOL;
        if ($this->rowClick) {
            $js .= $this->getItVariable() . '.setRowClick( '.$this->getRowClick().' );' . PHP_EOL;
        }
        $js .= PHP_EOL;
        
        if ($this->enableColumnSelection) {
            $js .= $this->getItVariable() . ".createColumnSelection({forcePopup: true});" . PHP_EOL;
        }
        $js .= PHP_EOL;
        
        $colKeys = array_keys( $this->columns );
        usort($colKeys, function($k1, $k2) {
            return $this->columns[$k1]['prio'] - $this->columns[$k2]['prio'];
        });
        
        $columnState = null;
        if (isset($this->opts['tableName'])) {
            $columnState = getJsState('indextable-enabled-columns-'.$this->opts['tableName']);
        }
        
        foreach($this->columns as $colName => $props) {
            if (isset($props['fieldName']) == false) {
                $props['fieldName'] = $colName;
            }
            
            // hidden columns
            if ($columnState && isset($columnState[$props['fieldName']]) && is_false($columnState[$props['fieldName']])) {
                $props['hidden'] = true;
            }
            
            $js .= $this->getItVariable() . '.addColumn({' . PHP_EOL . '  ';
            $x=0;
            foreach($props as $key => $val) {
                if ($x > 0) {
                    $js .= ', ';
                }
                $js .= json_encode($key) . ': ';
                if (in_array($key, ['render'])) {
                    $js .= $val;
                }
                else {
                    $js .= json_encode($val);
                }
                $js .= PHP_EOL;
                
                $x++;
            }
            $js .= '});'.PHP_EOL;
            $js .= PHP_EOL;
        }
        
        if ($this->renderLoad) {
            $js .= $this->getItVariable().'.load();'.PHP_EOL;
            $js .= PHP_EOL;
        }
        
        return $js;
    }
    
}

