<?php

	Class fieldSystemModifiedDate extends Field {

		public function __construct(){
			parent::__construct();
			$this->_name = __('Date: System Modified');
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

		public function entryDataCleanup($entry_id, $data=NULL){
			return true;
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		private static function dateFromEntryID($entry_id){
			return Symphony::Database()->fetchRow(0, sprintf("
				SELECT modification_date_gmt
				FROM `tbl_entries` 
				WHERE `id` = %d 
				LIMIT 1
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

		public function displayPublishPanel(XMLElement &$wrapper, $data = null, $flagWithError = null, $fieldnamePrefix = null, $fieldnamePostfix = null, $entry_id = null)
		{
			// Append assets
			if(class_exists('Administration')) {
				Administration::instance()->Page->addStylesheetToHead(URL . '/extensions/system_date_fields/assets/system_date_fields.publish.css', 'screen', 104, false);
			}

			$label = new XMLElement('label');
			$wrapper->appendChild($label);
			
			$row = static::dateFromEntryID($entry_id);
			$value = DateTimeObj::get(__SYM_DATE_FORMAT__, $row['modification_date_gmt'] . ' +00:00');
			
			$label->setValue($this->get('label') . ': ' . $value);
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

			Symphony::Database()->query("
				DELETE FROM `tbl_fields_systemmodifieddate`
				WHERE `field_id` = '$id' 
				LIMIT 1
			");
			Symphony::Database()->insert($fields, 'tbl_fields_systemmodifieddate');
		}

	/*-------------------------------------------------------------------------
		Output:
	-------------------------------------------------------------------------*/

		public function fetchIncludableElements() {}

		public function prepareTextValue($data, $entry_id = null)
		{
			$row = static::dateFromEntryID($entry_id);
			return DateTimeObj::get(__SYM_DATE_FORMAT__, $row['modification_date_gmt'] . ' +00:00');
		}

	/*-------------------------------------------------------------------------
		Filtering:
	-------------------------------------------------------------------------*/

		public function buildSortingSQL(&$joins, &$where, &$sort, $order='ASC'){
			$sort = 'ORDER BY ' . (in_array(strtolower($order), array('random', 'rand')) ? 'RAND()' : "`e`.`modification_date_gmt` $order");
		}

	}

