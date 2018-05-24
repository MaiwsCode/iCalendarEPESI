<?php
defined("_VALID_ACCESS") || die('Direct access forbidden'); // This is a security feature.
require_once("zapcallib.php");  
class iCalSync extends Module { // Note, how the class' name reflects module's path.

    

  public function body(){
		// Base_ActionBarCommon::add('print',__('Export Calendar'), Base_BoxCommon::main_module_instance()->create_callback_href(array('Samco_Ical', 'ical')));
        //  $usr = ACL::get_user();
          }

public function settings()
	{
		$form = $this->init_module('Libs/QuickForm');
		$usr = Acl::get_user();
	        $get_hash = DB::Execute("select * FROM crm_calendar_custom_events_handlers");
		if(!$get_hash->EOF)
		{
		 $d = array();
		 $d['_me'] = "0";
		 $d['_pc'] = "0";
		 $d['_ts'] = "0";
		 $form->setDefaults($d); 
		}
			
		//Form Layout
		$form->addElement('header',null, __('Exportuj kalendarz'));
		$form->addElement('checkbox','_me', __('Meetings'));
		$form->addElement('checkbox','_pc', __('Phone Calls'));
		$form->addElement('checkbox','_ts', __('Tasks'));
		//$form->addElement('text','_dir', __('Wpisz nazwę folderu dla danych'));
		$form->addElement('submit', "submitt", "Exportuj");
		Base_ActionBarCommon::add('back', __('Back'), $this->create_main_href("Base_User_Settings"));
                Base_ActionBarCommon::add('save', __('Save'), $form->get_submit_form_href());
		 if($form->getSubmitValue('submited') && $form -> validate()) {
				$this ->download_cal_to_iCal($form -> exportValues());
                   //     Base_StatusBarCommon::message(__('Swoje zapisane pliki znajdziesz w: /home/...'));
                }
                $form->display();	
	}
public function set_data($array,$array_name){
    
    try{
        return $array[$array_name];
        
    }
    catch(Exception $e){
        return "";
    }
    
}

public function download_cal_to_iCal($fdata){
    $metings = $this ->set_data($fdata,"_me");
    $phonecalls = $this ->set_data($fdata,"_pc");
    $tasks = $this ->set_data($fdata,"_ts");
   // $dir_path = $this -> set_data($fdata,"_dir");
    // Base_StatusBarCommon::message(__($metings." ".$phonecalls." ".$tasks));
    $start = "BEGIN:VCALENDAR";
    $end = "END:VCALENDAR";
    $calendar_string = "";
    if($metings != ""){
     
     $results = DB::GetArray("SELECT * FROM crm_meeting_data_1 WHERE f_time > NOW()");
     for($i=0;$i<count($results);$i++){
     $calendar_day = new ZCiCal();
     $event = new ZCiCalNode("VEVENT",$calendar_day->curnode);
     $event->addNode(new ZCiCalDataNode("SUMMARY:" .$results[$i]["f_title"] ));
     $event->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($results[$i]["f_time"])));
     $event->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($results[$i]["f_time"])));
     $uid = date('Y-m-d-H-i-s') . "@EPESI-EXPORT".$i.(rand(0, 1000));
     $event->addNode(new ZCiCalDataNode("UID:" . $uid));
     if($results[$i]["f_description"] != ""){
     $event->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent(
             $results[$i]["f_description"])));
     }
     $event->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
     $toString = $calendar_day->export();
     $toString = str_replace("BEGIN:VCALENDAR", "", $toString);
     $toString = str_replace("END:VCALENDAR", "", $toString);
        $toString = trim($toString,"\n\t");
       // $toString = "\n".$toString;
    // $calendar_string .= $calendar_day->export();
     $calendar_string .= $toString."\n\r";
        }    
    }
    if($phonecalls != ""){
     $results = DB::GetArray("SELECT * FROM phonecall_data_1 WHERE f_date_and_time > NOW()");
     for($i=0;$i<count($results);$i++){
        $calendar_day = new ZCiCal();
        $event = new ZCiCalNode("VEVENT",$calendar_day->curnode);
        $event->addNode(new ZCiCalDataNode("SUMMARY:" .$results[$i]["f_subject"] ));
        $event->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($results[$i]["f_date_and_time"])));
        $event->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($results[$i]["f_date_and_time"])));
        $uid = date('Y-m-d-H-i-s') . "@EPESI-EXPORT".$i.rand(0, 1000);
        $event->addNode(new ZCiCalDataNode("UID:" . $uid));
        if($results[$i]["f_description"] != ""){
        $event->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent(
        "PHONE: ".$results[$i]["f_other_phone_number"].$results[$i]["f_description"])));
        }
        $event->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
        $toString = $calendar_day->export();
        $toString = str_replace("BEGIN:VCALENDAR", "", $toString);
        $toString = str_replace("END:VCALENDAR", "", $toString);
        $toString = trim($toString,"\n\t");
        //$calendar_string .= $calendar_day->export();
        $calendar_string .= $toString."\n\r";
        }
    }
    if($tasks != ""){
     $results = DB::GetArray("SELECT * FROM task_data_1 WHERE f_deadline > NOW()");
     for($i=0;$i<count($results);$i++){
        $calendar_day = new ZCiCal();
        $event = new ZCiCalNode("VEVENT",$calendar_day->curnode);
        $event->addNode(new ZCiCalDataNode("SUMMARY:" .$results[$i]["f_title"] ));
        $event->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($results[$i]["f_deadline"])));
        $event->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($results[$i]["f_deadline"])));
        $uid = date('Y-m-d-H-i-s') . "@EPESI-EXPORT".$i.rand(0, 1000);
        $event->addNode(new ZCiCalDataNode("UID:" . $uid));
        if($results[$i]["f_description"] != ""){
        $event->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent($results[$i]["f_description"])));
        }
        $event->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
        $toString = $calendar_day->export();
        $toString = str_replace("BEGIN:VCALENDAR", "", $toString);
        $toString = str_replace("END:VCALENDAR", "", $toString);
        $toString = trim($toString,"\n\t");
        //$toString = "\n".$toString;
        //$calendar_string .= $calendar_day->export();
        $calendar_string .= $toString."\n\r";

        }
    }
    $calendar_string = $start."\n".$calendar_string."\n".$end;
    //$cal = trim($calendar_string,"\n");
    $calendar_string = rtrim(preg_replace("/(^[\r\n]*|[\n]+)[\s\t]*[\n]+/", "\n", $calendar_string));
    $calendarFile = fopen($_SERVER['DOCUMENT_ROOT']."/iCalendar.ics", "w");
    fputs($calendarFile,$calendar_string);
    Base_StatusBarCommon::message(__("Kalendarz został zapisany w: \n ".$_SERVER['DOCUMENT_ROOT']));
   
    
}

}
