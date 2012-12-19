<?php

	Class extension_System_Date_Fields extends Extension{

		public function uninstall(){
			Symphony::Database()->query("DROP TABLE `tbl_fields_systemcreateddate`");
			Symphony::Database()->query("DROP TABLE `tbl_fields_systemmodifieddate`");
		}

		public function install(){
			Symphony::Database()->query("
				CREATE TABLE `tbl_fields_systemcreateddate` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					PRIMARY KEY  (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
			");
			Symphony::Database()->query("
				CREATE TABLE `tbl_fields_systemmodifieddate` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					PRIMARY KEY  (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
			");
		}
	}
