<?php


namespace filesync\model;


class StoreFileDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\filesync\\model\\StoreFile' );
	}
	
	
	public function read($id) {
	    return $this->queryOne('select * from filesync__store_file where store_file_id = ?', array($id));
	}
	
	public function readByPath($storeId, $path) {
	    $sql = "select sf.*, sfr.md5sum
                from filesync__store_file sf
                left join filesync__store_file_rev sfr on (sfr.store_file_id = sf.store_file_id and sfr.rev = sf.rev) 
                where store_id = ? and path = ? ";
        
	    return $this->queryOne($sql, array($storeId, $path));
	}
	
	public function delete($id) {
	    $this->query('delete from filesync__store_file where store_file_id = ?', array($id));
	}

	public function search($opts) {
	    $sql = "select filesync__store_file.*, "
                . "filesync__store_file_meta.company_id, "
                . "filesync__store_file_meta.subject, "
                . "filesync__store_file_meta.long_description, "
                . "filesync__store_file_meta.document_date, "
                . "ifnull(filesync__store_file_meta.public, 0) public, "
                . "customer__company.company_name, "
                . "customer__person.firstname, "
                . "customer__person.insert_lastname, "
                . "customer__person.lastname, "
                . "sfr.filesize, "
                . "sfr.encrypted, "
                . "sfr.lastmodified, "
                . "filesync__store.store_name "
                . "from filesync__store_file "
                . "left join filesync__store on (filesync__store.store_id = filesync__store_file.store_id) "
                . "left join filesync__store_file_meta on (filesync__store_file.store_file_id = filesync__store_file_meta.store_file_id) "
                . "left join filesync__store_file_rev sfr on (filesync__store_file.store_file_id = sfr.store_file_id and filesync__store_file.rev = sfr.rev) "
                . "left join customer__company on (customer__company.company_id = filesync__store_file_meta.company_id) "
                . "left join customer__person on (customer__person.person_id = filesync__store_file_meta.person_id) ";
                
        $where = array();
        $params = array();
        
        
        if (isset($opts['storeId']) && $opts['storeId']) {
            $where[] = " filesync__store_file.store_id = ? ";
            $params[] = $opts['storeId'];
        }
        
        if (isset($opts['storeIds']) && is_array($opts['storeIds']) && count($opts['storeIds'])) {
            $storeIds = array();
            foreach($opts['storeIds'] as $id) {
                $storeIds[] = (int)$id;
            }
            
            $where[] = " filesync__store_file.store_id IN ( " . implode(', ', $storeIds) . ") ";
        }
        
        if (isset($opts['archiveOnly']) && $opts['archiveOnly']) {
            $where[] = " filesync__store.store_type = 'archive' ";
        }
        
        
        if (isset($opts['path']) && $opts['path']) {
            $where[] = 'path LIKE ? ';//COLLATE utf8mb4_general_ci';
            $params[] = '%'.trim($opts['path']).'%';
        }
        
        if (isset($opts['companyId']) && $opts['companyId']) {
            $where[] = " filesync__store_file_meta.company_id = ? ";
            $params[] = $opts['companyId'];
        }
        
        if (isset($opts['company_name']) && $opts['company_name']) {
            $where[] = " company_name LIKE ? ";
            $params[] = '%'.$opts['company_name'].'%';
        }
        
        if (isset($opts['subject']) && $opts['subject']) {
            $where[] = " subject LIKE ? ";
            $params[] = '%'.$opts['subject'].'%';
        }
        
        if (isset($opts['public']) && $opts['public'] !== '') {
            $where[] = " ifnull(public, 0) LIKE ? ";
            $params[] = $opts['public'] ? 1 : 0;
        }
        
        if (count($where)) {
            $sql .= " WHERE (".implode(" ) AND (", $where) . ") ";
        }
        
        
        $sql .= "order by filesync__store_file_meta.document_date desc, filesync__store_file.store_file_id desc";
        
        
        return $this->queryCursor($sql, $params);
	}
	
	
	public function readByStore($storeId) {
	    $sql = "select sf.*, sfr.md5sum, sfr.filesize, sfr.lastmodified "
	        . "from filesync__store_file sf "
            . "left join filesync__store_file_rev sfr using (store_file_id, rev) "
            . "where store_id = ? "
            . "order by path ";
	    
        return $this->queryList($sql, array($storeId));
	}
	
	public function markDeleted($storeId, $path) {
	    $this->query('update filesync__store_file set deleted = true where store_id = ? and path = ?', array($storeId, $path));
	}
	
	
	public function setRevision($storeFileId, $rev) {
	    $this->query('update filesync__store_file set rev = ? where store_file_id = ?', array($rev, $storeFileId));
	}

	public function readFilesByCustomer($companyId=null, $personId=null) {
	    if (!$companyId && !$personId)
	        return array();
	        
	        $sql = "select sf.*, sfr.filesize, sfm.document_date, sfm.subject, sfm.public
                from filesync__store_file sf
                left join filesync__store s using (store_id)
                left join filesync__store_file_rev sfr using (store_file_id)
                join filesync__store_file_meta sfm on (sf.store_file_id = sfm.store_file_id)
                where (s.store_type = 'archive' OR s.store_type = 'share')
                    and sf.deleted = false
                    and sfr.rev = sf.rev ";
	        
	        $params = array();
	        
	        if ($companyId) {
	            $sql .= ' and sfm.company_id = ? ';
	            $params[] = $companyId;
	        }
	        if ($personId) {
	            $sql .= ' and sfm.person_id = ? ';
	            $params[] = $personId;
	        }
	        
	        $sql .= ' order by ifnull(sfm.document_date, sf.created) desc, sf.store_file_id desc';
	        
	        return $this->queryList($sql, $params);
	}
	
	public function readArchiveFiles($companyId=null, $personId=null) {
	    if (!$companyId && !$personId)
	        return array();
	    
	    $sql = "select sf.*, sfr.filesize, sfm.document_date, sfm.subject
                from filesync__store_file sf
                left join filesync__store s using (store_id)
                left join filesync__store_file_rev sfr using (store_file_id)
                join filesync__store_file_meta sfm on (sf.store_file_id = sfm.store_file_id)
                where s.store_type = 'archive' and sf.deleted = false
                and sfr.rev = sf.rev ";
	    
	    $params = array();
	    
	    if ($companyId) {
	        $sql .= ' and sfm.company_id = ? ';
	        $params[] = $companyId;
	    }
	    if ($personId) {
	        $sql .= ' and sfm.person_id = ? ';
	        $params[] = $personId;
	    }
	    
	    $sql .= ' order by sfm.document_date desc, sf.store_file_id desc';
	    
	    return $this->queryList($sql, $params);
	}
	
	
	public function autocomplete($storeId, $q) {
	    $sql = "SELECT DISTINCT substring(path, 1, length(path) - LOCATE('/', reverse(path))+1) path
                FROM filesync__store_file sf
                join filesync__store s on (s.store_id = sf.store_id)
                where s.store_type = 'share'
                    and s.store_id = ?
                    and sf.deleted = false
                    and LOWER(substring(path, 1, length(path) - LOCATE('/', reverse(path))+1)) LIKE ?
                ORDER BY path
                LIMIT 20";
        
	    $res = $this->query($sql, array((int)$storeId, '%'.strtolower($q).'%'));
	    
	    $paths = array();
	    while ( $r = mysqli_fetch_array($res) ) {
	        $paths[] = $r[0];
	    }
	    
	    return $paths;
	}
	
	
}

