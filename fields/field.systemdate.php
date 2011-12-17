<?php

	Class fieldSystemDate extends Field {

		public function __construct(&$parent){
			parent::__construct($parent);
			$this->_name = __('System Date');
		}

	/*-------------------------------------------------------------------------
		Setup:
	-------------------------------------------------------------------------*/

		public function isSortable(){
			return true;
		}

		public function createTable(){
			return true;
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		private static function __dateFromEntryID($entry_id){
			return Symphony::Database()->fetchRow(0, sprintf("
				SELECT creation_date_gmt FROM `tbl_entries` WHERE `id` = %d LIMIT 1
			", $entry_id));
		}

	/*-------------------------------------------------------------------------
		Settings:
	-------------------------------------------------------------------------*/

		public function displaySettingsPanel(&$wrapper, $errors = null) {
			parent::displaySettingsPanel($wrapper, $errors);
			$this->appendShowColumnCheckbox($wrapper);
		}

	/*-------------------------------------------------------------------------
		Input:
	-------------------------------------------------------------------------*/

		public function displayPublishPanel(&$wrapper, $data = null, $error = null, $prefix = null, $postfix = null) {
			return;
		}

		public function checkPostFieldData($data, &$message, $entry_id=NULL){
			return self::__OK__;
		}

		public function processRawFieldData($data, &$status, $simulate=false, $entry_id=NULL){
			$status = self::__OK__;
 			return NULL;
		}

		public function commit(){
			if(!parent::commit()) return false;

			$id = $this->get('id');

			if($id === false) return false;

			$fields = array();
			$fields['field_id'] = $id;

			Symphony::Database()->query("DELETE FROM `tbl_fields_".$this->handle()."` WHERE `field_id` = '$id' LIMIT 1");
			Symphony::Database()->insert($fields, 'tbl_fields_' . $this->handle());
		}

	/*-------------------------------------------------------------------------
		Output:
	-------------------------------------------------------------------------*/

		public function prepareTableValue($data, XMLElement $link=NULL, $entry_id=NULL) {
			$row = self::__dateFromEntryID($entry_id);

			$value = DateTimeObj::get(__SYM_DATE_FORMAT__, strtotime($row['creation_date_gmt'] . ' +00:00'));

			return parent::prepareTableValue(array('value' => $value), $link);
		}

	/*-------------------------------------------------------------------------
		Filtering:
	-------------------------------------------------------------------------*/

		public function buildSortingSQL(&$joins, &$where, &$sort, $order='ASC'){
			$sort = 'ORDER BY ' . (in_array(strtolower($order), array('random', 'rand')) ? 'RAND()' : "`e`.`creation_date_gmt` $order");
		}

	}

