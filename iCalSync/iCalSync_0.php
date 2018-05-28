<?php
defined("_VALID_ACCESS") || die('Direct access forbidden'); // This is a security feature.

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
//konwertuje czas z DB do schematu google
public function google_date_time($date){
    $ymd = $date;
    $ymd = str_replace(" ", "T", $ymd);
    $ymd = $ymd."+02:00";
    return $ymd;
}

public function download_cal_to_iCal($fdata){
    $events_array = array();
    $event = NULL;
    $metings = $this ->set_data($fdata,"_me");
    $phonecalls = $this ->set_data($fdata,"_pc");
    $tasks = $this ->set_data($fdata,"_ts");
 //dodawanie eventow do tablicy
    if($metings != ""){
     
     $results = DB::GetArray("SELECT * FROM crm_meeting_data_1 WHERE f_time > NOW()");
     for($i=0;$i<count($results);$i++){
        $event = new Google_Service_Calendar_Event(array(
        'summary' => $results[$i]["f_title"],
        'description' => $results[$i]["f_description"],
        'id' => "meeting".$results[$i]["id"],
        'start' => array(
        'dateTime' => $this->google_date_time($results[$i]["f_time"]),
       // 'dateTime' => '2018-05-25T17:00:00+02:00',
        'timeZone' => 'Europe/Warsaw',
        ),
        'end' => array(
        'dateTime' => $this->google_date_time($results[$i]["f_time"]),
       // 'dateTime' => '2018-05-25T17:00:00+02:00',
        'timeZone' => 'Europe/Warsaw',
        ),
        ));
      array_push($events_array, $event);    
        }    
    }
    if($phonecalls != ""){
     $results = DB::GetArray("SELECT * FROM phonecall_data_1 WHERE f_date_and_time > NOW()");
     for($i=0;$i<count($results);$i++){
          $event = new Google_Service_Calendar_Event(array(
        'summary' => $results[$i]["f_subject"],
        'description' => 'PHONE:'.$results[$i]["f_other_phone_number"].$results[$i]["f_description"],
        'id' => "phonecall".$results[$i]["id"],
        'start' => array(
        'dateTime' => $this->google_date_time($results[$i]["f_date_and_time"]),
       // 'dateTime' => '2018-05-25T17:00:00+02:00',
        'timeZone' => 'Europe/Warsaw',
        ),
        'end' => array(
        'dateTime' => $this->google_date_time($results[$i]["f_date_and_time"]),
       // 'dateTime' => '2018-05-25T17:00:00+02:00',
        'timeZone' => 'Europe/Warsaw',
        ),
        ));
      array_push($events_array, $event);    
        }
    }
    if($tasks != ""){
     $results = DB::GetArray("SELECT * FROM task_data_1 WHERE f_deadline > NOW()");
     for($i=0;$i<count($results);$i++){

      $event = new Google_Service_Calendar_Event(array(
        'summary' => $results[$i]["f_title"],
        'description' => $results[$i]["f_description"],
        'id' => "task".$results[$i]["id"],
        'start' => array(
        'dateTime' => $this->google_date_time($results[$i]["f_deadline"]),
        //'dateTime' => '2018-05-25T17:00:00+02:00',
        'timeZone' => 'Europe/Warsaw',
        ),
        'end' => array(
        'dateTime' => $this->google_date_time($results[$i]["f_deadline"]),
       // 'dateTime' => '2018-05-25T17:00:00+02:00',
        'timeZone' => 'Europe/Warsaw',
        ),
        ));
      array_push($events_array, $event);    
        }
    }
  /*  $calendar_string = $start."\n".$calendar_string."\n".$end;
    $calendar_string = rtrim(preg_replace("/(^[\r\n]*|[\n]+)[\s\t]*[\n]+/", "\n", $calendar_string));
    $calendarFile = fopen($_SERVER['DOCUMENT_ROOT']."/iCalendar.ics", "w");
    fputs($calendarFile,$calendar_string);*/
  

    //save to google calendar
    //zapis do google calendar
    $client = new Google_Client();
    //sciezka do pliku z Google Console
    $client->setAuthConfig($_SERVER['DOCUMENT_ROOT']."/modules/iCalSync/client_secret.json");
    $client->addScope(Google_Service_Calendar::CALENDAR);
    ////tymczasowy token google auth
    $path = $_SERVER['DOCUMENT_ROOT']."/modules/iCalSync/temp_tokens/temp_token.txt";
    $file = fopen($path,"r");
    $token = fread($file,filesize($path));
    $token_array = unserialize($token);
    fclose($file);
    if (strlen($token)>0) {
      $client->setAccessToken($token_array);
      //usuwany token
      unlink($path);
      $service = new Google_Service_Calendar($client);
      $calendarId = 'primary';
      $counter = 0;
      for($x=0;$x<count($events_array);$x++){
          
      try{
          //sprawdza czy istnieje juz to wydarzenie
      $event = $service->events->get('primary',$events_array[$x]["id"]);  
      
      }
      catch(Exception $e){
          //dodaje wydarzenie
      $event = $service->events->insert($calendarId,$events_array[$x]);  
      $counter++;
      }
      }
      Base_StatusBarCommon::message(__("Zsynchronizowano kalendarze \n Dodano: ".$counter." wydarzeń"));
    } else {
        //wymuszenie logowania
      $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
      Epesi::redirect(filter_var($redirect_uri, FILTER_SANITIZE_URL));

    }
    
    
}
    
}

?>



