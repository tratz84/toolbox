<?php


namespace core\forms\lists;

class IndexTable {
    
    protected $itVariable = 'it';
    
    protected $containerId;
    protected $connectorUrl;
    protected $rowClick = null;
    
    protected $opts = array();
    
    protected $columns = array();
    protected $columnPrio = 10;
    
    // call 'it.load();' ?
    protected $renderLoad = true;
    
    public function __construct() {
        $this->autoSetItVariable();
        
        $this->codegen();
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
    
    public function setContainerId( $containerId ) { $this->containerId = $containerId; }
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
    
    protected function codegen() {
        
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
        
        $colKeys = array_keys( $this->columns );
        usort($colKeys, function($k1, $k2) {
            return $this->columns[$k1]['prio'] - $this->columns[$k2]['prio'];
        });
        
        foreach($this->columns as $colName => $props) {
            if (isset($props['fieldName']) == false) {
                $props['fieldName'] = $colName;
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

