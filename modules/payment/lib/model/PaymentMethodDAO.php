<?php


namespace payment\model;


class PaymentMethodDAO extends \core\db\DAOObject {

	public function __construct() {
		$this->setResource( 'default' );
		$this->setObjectName( '\\payment\\model\\PaymentMethod' );
	}

   public function read($id) {
       return $this->queryOne("select * from payment__payment_method where payment_method_id = ?", array($id));
   }
   public function delete($id) {
       $this->query("delete from payment__payment_method where payment_method_id = ?", array($id));
   }
   public function readAll() {
       return $this->queryList("select * from payment__payment_method order by sort");
   }
   public function readActive() {
       return $this->queryList("select * from payment__payment_method where active = true order by sort");
   }
   public function readByCode($c) {
       return $this->queryOne("select * from payment__payment_method where code = ?", array($c));
   }

}

