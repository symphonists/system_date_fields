<?php

	require_once(EXTENSIONS . '/system_date_fields/lib/class.systemdate.php');

	class fieldSystemCreatedDate extends FieldSystemDate {

		public function __construct(){
			parent::__construct();
			$this->_name = __('Date: System Created');
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		protected function getFieldName()
		{
			return 'creation_date_gmt';
		}

	}

