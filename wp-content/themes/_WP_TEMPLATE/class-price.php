<?php
class classPrice {
   function __construct() {
       
   }
   
   public function getEuroVal($val) {
		$val = (int)$val;
		$eur = (int)($val/40);
		return number_format($eur, 0, '.', ' ').' €';
   }
}
?>