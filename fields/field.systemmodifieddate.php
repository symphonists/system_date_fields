<?php

	require_once(EXTENSIONS . '/system_date_fields/lib/class.systemdate.php');

	class fieldSystemModifiedDate extends FieldSystemDate {

		public function __construct(){
			parent::__construct();
			$this->_name = __('Date: System Modified');
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		protected function getFieldName()
		{
			return 'modification_date_gmt';
		}

	}

