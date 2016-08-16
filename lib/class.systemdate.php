<?php

	abstract class FieldSystemDate extends Field
	{

	/*-------------------------------------------------------------------------
		Setup:
	-------------------------------------------------------------------------*/

		public function isSortable()
		{
			return true;
		}

		public function createTable()
		{
			return true;
		}

		public function entryDataCleanup($entry_id, $data = null)
		{
			return true;
		}

		public function mustBeUnique()
		{
			return true;
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		protected abstract function getFieldName();

		private function dateFromEntryID($entry_id)
		{
			return Symphony::Database()->fetchRow(0, sprintf("
				SELECT %s
				FROM `tbl_entries` 
				WHERE `id` = %d 
				LIMIT 1
			", $this->getFieldName(), $entry_id));
		}

		private function getDateFormat()
		{
			return $this->get('show_time') == 'yes' ?
				__SYM_DATETIME_FORMAT__ :
				__SYM_DATE_FORMAT__;
		}

		private function formatDate($date)
		{
			return DateTimeObj::get($this->getDateFormat(), $date->format('U'));
		}

		private function parseDate($row)
		{
			$fieldname = $this->getFieldName();
			if (!empty($row) && isset($row[$fieldname])) {
				$value = $row[$fieldname] . ' Etc/UTC';
			}
			else {
				$value = DateTimeObj::getGMT('Y-m-d H:i:s') . ' Etc/UTC';
			}
			$date = DateTimeObj::parse($value);
			return $date;
		}

	/*-------------------------------------------------------------------------
		Settings:
	-------------------------------------------------------------------------*/

		public function displaySettingsPanel(XMLElement &$wrapper, $errors = null)
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
			
			$row = $this->dateFromEntryID($entry_id);
			$date = $this->parseDate($row);
			$value = $this->formatDate($date);
			$time = new XMLElement('time', $value, array('class' => 'field-value-readonly'));
			$label->setValue($this->get('label'));
			$label->appendChild($time);

			if ($this->get('use_timeago') == 'yes') {
				$label->setAttribute('class', 'js-systemdate-timeago');
				$time->setAttributeArray(array(
					'utc' => $date->format('U'),
					'datetime' => $date->format(DateTime::ISO8601),
					'title' => $time->getValue(),
				));
			}
		}

		public function checkPostFieldData($data, &$message, $entry_id = null)
		{
			return self::__OK__;
		}

		public function processRawFieldData($data, &$status, &$message = null, $simulate = false, $entry_id = null)
		{
			$status = self::__OK__;
 			return NULL;
		}

		public function commit()
		{
			if (!parent::commit()) {
				return false;
			}

			$id = $this->get('id');

			if ($id === false) {
				return false;
			}

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
				$row = $this->dateFromEntryID($entry_id);
				if (!$link) {
					$link = new XMLElement('span');
				}
				$date = $this->parseDate($row);
				$link->setAttribute('class', 'js-systemdate-timeago');
				$time = new XMLElement('time', $this->formatDate($date));
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
			$row = $this->dateFromEntryID($entry_id);
			$date = $this->parseDate($row);
			return $this->formatDate($date);
		}

	/*-------------------------------------------------------------------------
		Filtering:
	-------------------------------------------------------------------------*/

		public function buildSortingSQL(&$joins, &$where, &$sort, $order = 'ASC')
		{
			$fieldname = $this->getFieldName();
			$sort = 'ORDER BY ' . (in_array(strtolower($order), array('random', 'rand')) ? 'RAND()' : "`e`.`$fieldname` $order");
		}

	}

