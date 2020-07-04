<?php


use core\controller\BaseController;
use core\db\DatabaseHandler;
use core\db\DBObject;
use core\forms\lists\ListResponse;

class usercustomerController extends BaseController {
    
    
    
    public function action_select2() {
        
        $sql = "select user_id id, username name, 'user' as type
                from base__user
                where username LIKE ? OR concat(firstname, lastname) LIKE ?
                union
                select company_id id, company_name name, 'company' as type
                from customer__company
                where company_name LIKE ?
                union
                select person_id id, concat(lastname, ' ', insert_lastname, ', ', firstname) name, 'person' as type
                from customer__person
                where concat(lastname, ' ', insert_lastname, firstname, ' ', insert_lastname, ' ', lastname) LIKE ?
                ";
        
        $q = '%'.get_var('name').'%';
        $params = array();
        $params[] = $q;
        $params[] = $q;
        $params[] = $q;
        $params[] = $q;
        
        /** @var \core\db\connection\MysqlConnection $mcon */
        $mcon = DatabaseHandler::getConnection('default');
        $cursor = $mcon->queryCursor(DBObject::class, $sql, $params);
        
        $lr = ListResponse::fillByCursor(0, 20, $cursor, ['id', 'usercustomer_id', 'name', 'type']);
        
        $arr = array();
        
        if (isset($_REQUEST['name']) == false || trim($_REQUEST['name']) == '') {
            $arr[] = array(
                'id' => '0',
                'text' => t('Make your choice')
            );
        }
        foreach($lr->getObjects() as $customer) {
            $arr[] = array(
                'id' => $customer['type'] . '-' . $customer['id'],
                'text' => $customer['name']
            );
        }
        
        
        $result = array();
        $result['results'] = $arr;
        
        $this->json($result);
    }
    
}
