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
				$ret1 = Symphony::Database()->query("
					ALTER TABLE `tbl_fields_systemcreateddate`
					ADD COLUMN `show_time` enum('yes','no') NOT NULL DEFAULT 'no',
					ADD COLUMN `use_timeago` enum('yes','no') NOT NULL DEFAULT 'no'
				");
				$ret2 = Symphony::Database()->query("
					ALTER TABLE `tbl_fields_systemmodifieddate`
					ADD COLUMN `show_time` enum('yes','no') NOT NULL DEFAULT 'no',
					ADD COLUMN `use_timeago` enum('yes','no') NOT NULL DEFAULT 'no'
				");
				$ret = $ret1 && $ret2;
			}

			return $ret;
		}

		public function uninstall()
		{
			$ret1 = Symphony::Database()->query("DROP TABLE `tbl_fields_systemcreateddate`");
			$ret2 = Symphony::Database()->query("DROP TABLE `tbl_fields_systemmodifieddate`");
			return $ret1 && $ret2;
		}

		public function install()
		{
			$ret1 = Symphony::Database()->query("
				CREATE TABLE `tbl_fields_systemcreateddate` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					PRIMARY KEY  (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
			");
			$ret2 = Symphony::Database()->query("
				CREATE TABLE `tbl_fields_systemmodifieddate` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					PRIMARY KEY  (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
			");
			return $ret1 && $ret2;
		}
	}
