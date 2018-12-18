<?php

	Class extension_System_Date_Fields extends Extension
	{

		public function getSubscribedDelegates()
		{
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitialiseAdminPageHead',
					'callback' => 'appendAssets'
				),
			);
		}

		/**
		 *
		 * Appends js/css files references into the head, if needed
		 * @param array $context
		 */
		public function appendAssets(array $context)
		{
			// store de callback array localy
			$c = Administration::instance()->getPageCallback();

			// publish page
			if(isset($c['context']['section_handle'])) {

				Administration::instance()->Page->addStylesheetToHead(
					URL . '/extensions/system_date_fields/assets/system_date_fields.publish.css',
					'screen',
					104,
					false
				);

				Administration::instance()->Page->addScriptToHead(
					URL . '/extensions/system_date_fields/assets/system_date_fields.publish.js',
					105,
					false
				);

				return;
			}
		}

		public function update($previousVersion = false)
		{
			$ret = true;

			if ($ret && version_compare($previousVersion,'1.1.0') == -1) {
				$ret1 = Symphonmy::Database()
					->alter('tbl_fields_systemcreateddate')
					->add([
						'show_time' => [
							'type' => 'enum',
							'values' => ['yes','no'],
							'default' => 'no',
						],
						'use_timeago' => [
							'type' => 'enum',
							'values' => ['yes','no'],
							'default' => 'no',
						],
					])
					->execute()
					->success();

				$ret2 = Symphonmy::Database()
					->alter('tbl_fields_systemmodifieddate')
					->add([
						'show_time' => [
							'type' => 'enum',
							'values' => ['yes','no'],
							'default' => 'no',
						],
						'use_timeago' => [
							'type' => 'enum',
							'values' => ['yes','no'],
							'default' => 'no',
						],
					])
					->execute()
					->success();

				$ret = $ret1 && $ret2;
			}

			return $ret;
		}

		public function uninstall()
		{
			$ret1 = Symphony::Database()
				->drop('tbl_fields_systemcreateddate')
				->ifExists()
				->execute()
				->success();

			$ret2 = Symphony::Database()
				->drop('tbl_fields_systemmodifieddate')
				->ifExists()
				->execute()
				->success();

			return $ret1 && $ret2;
		}

		public function install()
		{
			$ret1 = Symphony::Database()
				->create('tbl_fields_systemcreateddate')
				->ifNotExists()
				->fields([
					'id' => [
						'type' => 'int(11)',
						'auto' => true,
					],
					'field_id' => 'int(11)',
					'show_time' => [
						'type' => 'enum',
						'values' => ['yes','no'],
						'default' => 'no',
					],
					'use_timeago' => [
						'type' => 'enum',
						'values' => ['yes','no'],
						'default' => 'no',
					],
				])
				->keys([
					'id' => 'primary',
					'field_id' => 'key',
				])
				->execute()
				->success();

			$ret2 = Symphony::Database()
				->create('tbl_fields_systemmodifieddate')
				->ifNotExists()
				->fields([
					'id' => [
						'type' => 'int(11)',
						'auto' => true,
					],
					'field_id' => 'int(11)',
					'show_time' => [
						'type' => 'enum',
						'values' => ['yes','no'],
						'default' => 'no',
					],
					'use_timeago' => [
						'type' => 'enum',
						'values' => ['yes','no'],
						'default' => 'no',
					],
				])
				->keys([
					'id' => 'primary',
					'field_id' => 'key',
				])
				->execute()
				->success();

			return $ret1 && $ret2;
		}
	}
