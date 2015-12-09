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

		private function getDateFormat()
		{
			return $this->get('show_time') == 'yes' ?
				__SYM_DATETIME_FORMAT__ :
				__SYM_DATE_FORMAT__;
		}

		private function formatDate($date)
		{
			return DateTimeObj::get($this->getDateFormat(), $date . ' +00:00');
		}

	/*-------------------------------------------------------------------------
		Settings:
	-------------------------------------------------------------------------*/

		public function displaySettingsPanel(&$wrapper, $errors = null)
		{
			parent::displaySettingsPanel($wrapper, $errors);
			$fieldset = new XMLElement('fieldset');
			$row = new XMLElement('div', null, array('class' => 'two columns'));
			$this->appendShowColumnCheckbox($row);
			$this->appendShowTimeColumnCheckbox($row);
			$this->appendUseTimeAgoColumnCheckbox($row);
			$fieldset->appendChild($row);
			$wrapper->appendChild($fieldset);
		}

		protected function appendShowTimeColumnCheckbox(&$wrapper)
		{
			$this->createCheckboxSetting($wrapper, 'show_time', __('Display time'));
		}

		protected function appendUseTimeAgoColumnCheckbox(&$wrapper)
		{
			$this->createCheckboxSetting($wrapper, 'use_timeago', __('Use time ago'));
		}

	/*-------------------------------------------------------------------------
		Input:
	-------------------------------------------------------------------------*/

		public function displayPublishPanel(XMLElement &$wrapper, $data = null, $flagWithError = null, $fieldnamePrefix = null, $fieldnamePostfix = null, $entry_id = null)
		{
			$label = new XMLElement('label');
			$wrapper->appendChild($label);
			
			$row = static::dateFromEntryID($entry_id);
			$value = $this->formatDate($row['modification_date_gmt']);
			$time = new XMLElement('time', $value);
			$label->setValue($this->get('label'));
			$label->appendChild($time);

			if ($this->get('use_timeago') == 'yes') {
				$date = DateTimeObj::parse($row['modification_date_gmt'] . ' +00:00');
				$label->setAttribute('class', 'js-systemdate-timeago');
				$time->setAttributeArray(array(
					'utc' => $date->format('U'),
					'datetime' => $date->format(DateTime::ISO8601),
					'title' => $time->getValue(),
				));
			}
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

			$show_time = $this->get('show_time');
			$use_timeago = $this->get('use_timeago');

			$fields = array();
			$fields['show_time'] = empty($show_time) ? 'no' : $show_time;
			$fields['use_timeago'] = empty($use_timeago) ? 'no' : $use_timeago;

			return FieldManager::saveSettings($id, $fields);
		}

	/*-------------------------------------------------------------------------
		Output:
	-------------------------------------------------------------------------*/

		public function prepareTableValue($data, XMLElement $link = null, $entry_id = null)
		{
			if ($this->get('use_timeago') == 'yes') {
				$row = static::dateFromEntryID($entry_id);
				if (!$link) {
					$link = new XMLElement('span');
				}
				$date = DateTimeObj::parse($row['modification_date_gmt'] . ' +00:00');
				$link->setAttribute('class', 'js-systemdate-timeago');
				$time = new XMLElement('time', $this->formatDate($row['modification_date_gmt']));
				$time->setAttributeArray(array(
					'utc' => $date->format('U'),
					'datetime' => $date->format(DateTime::ISO8601),
					'title' => $time->getValue(),
				));
				$link->appendChild($time);
				return $link->generate();
			}
			return parent::prepareTableValue($data, $link, $entry_id);
		}

		public function prepareTextValue($data, $entry_id = null)
		{
			$row = static::dateFromEntryID($entry_id);
			return $this->formatDate($row['modification_date_gmt']);
		}

	/*-------------------------------------------------------------------------
		Filtering:
	-------------------------------------------------------------------------*/

		public function buildSortingSQL(&$joins, &$where, &$sort, $order='ASC'){
			$sort = 'ORDER BY ' . (in_array(strtolower($order), array('random', 'rand')) ? 'RAND()' : "`e`.`modification_date_gmt` $order");
		}

	}

