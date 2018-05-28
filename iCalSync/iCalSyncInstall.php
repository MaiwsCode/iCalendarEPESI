<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');

class iCalSyncInstall extends ModuleInstall {

	public function install() {
// Here you can place installation process for the module
		$ret = true;
		//Utils_RecordBrowserCommon::register_processing_callback('crm_calendar',array('iCalSyncCommon','add_action_bar'));
		return $ret; // Return false on success and false on failure
	}

public function uninstall() {
// Here you can place uninstallation process for the module
		$ret = true;
		return $ret; // Return false on success and false on failure
	}

	public function requires($v) {
// Returns list of modules and their versions, that are required to run this module
		return array(); 
	}
	public function version() {
	// Return version name of the module
			return array('1.0'); 
		}

	public function simple_setup() {
// Indicates if this module should be visible on the module list in Main Setup's simple view
		return true; 
	}

}

?>