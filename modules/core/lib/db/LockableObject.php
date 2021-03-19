<?php


namespace core\db;

interface LockableObject {
    
    public function getLockKey();
    
}

