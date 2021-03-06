<?php

// Use "composer require sabre/vobject" to get the required libraries
require_once('../3rdparty/autoload.php');

use Sabre\VObject;

// Configure your data
$configs = include('config.php');
$remoteHost = $configs['host'];
$username = $configs['username'];
$password = $configs['password'];
$nameGlobalCalendar = $configs['globalname'];
$calendarsArrayOfUrl = $configs['calendars'];

$arrayOfics = array();



foreach($calendarsArrayOfUrl as $value){
  $curl = curl_init($remoteHost . $value . '?export');
  curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  $result = curl_exec($curl);
  if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200){
    $arrayOfics[explode("/", $value)[5]] = $result;
  }
  curl_close($curl);
}


$globalVCalendar = new VObject\Component\VCalendar();
$globalVCalendar->add('X-WR-CALNAME', $nameGlobalCalendar);

foreach($arrayOfics as $key => $value){
    //echo $key."<br><br>";
    try {
      $anCalendar = VObject\Reader::read($value);
      $anCalendarName = (string)$anCalendar->{'X-WR-CALNAME'};
      if((boolean)$anCalendar->VEVENT){
        foreach($anCalendar->VEVENT as $event) {
          $event->CATEGORIES = $anCalendarName;
          $globalVCalendar->add($event);
        }
      }

      file_put_contents('./'.$key.'.ics', $anCalendar->serialize());

    } catch (ParseException $e){
      //echo $e;
    }

}

file_put_contents('./all.ics', $globalVCalendar->serialize());


?>
