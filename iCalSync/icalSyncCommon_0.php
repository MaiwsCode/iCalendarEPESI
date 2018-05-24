<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');

class iCalSyncCommon extends ModuleCommon {


    
 public static function user_settings() {

	return array(__("Exportuj kalendarz do iCalendar")=> 'settings');
 }

}

