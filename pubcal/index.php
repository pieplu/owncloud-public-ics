<?php

// Use "composer require sabre/vobject" to get the required libraries
require_once('./3rdparty/autoload.php');

use Sabre\VObject;

// Configure your data
$configs = include('config.php');
$remoteHost = $configs['host'];
$username = $configs['username'];
$password = $configs['password'];

$calendarsArray = array("clbrations","tudes");
$calendarsArrayFromServer = array();
$calendarsArrayOfUrl = array();
$arrayOfics = array();

// Get ownCloud calendars
$curlDav = curl_init($remoteHost . '/remote.php/caldav/calendars/'.$username.'/');
curl_setopt($curlDav, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($curlDav, CURLOPT_CUSTOMREQUEST, 'PROPFIND');
curl_setopt($curlDav, CURLOPT_RETURNTRANSFER, TRUE);
$DAVCal = curl_exec($curlDav);
curl_close($curlDav);

preg_match_all("/(\/remote\.php\/caldav\/calendars\/\w+\/\w+\/)/", $DAVCal, $calendarsArrayFromServer);

$calendarsArrayOfUrl = $calendarsArrayFromServer[0];


foreach($calendarsArrayOfUrl as $value){
  $curl = curl_init($remoteHost . $value . '?export');
  curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_VERBOSE, true);
  $result = curl_exec($curl);
  if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200){
    $arrayOfics[explode("/", $value)[5]] = $result;
  }
  curl_close($curl);
}


$globalVCalendar = new VObject\Component\VCalendar();

foreach($arrayOfics as $key => $value){
    echo $key."<br><br>";
    try {
      $anCalendar = VObject\Reader::read($value);

      if((boolean)$anCalendar->VEVENT){
        foreach($anCalendar->VEVENT as $event) {
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
