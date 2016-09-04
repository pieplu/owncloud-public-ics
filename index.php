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
$arrayOfics = array();
// Get ownCloud calendar

foreach($calendarsArray as $value){
  echo $value."<br>";
  $curl = curl_init($remoteHost . '/remote.php/caldav/calendars/'.$username.'/'.$value.'?export');
  curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_VERBOSE, true);
  $arrayOfics[$value] = curl_exec($curl);
  curl_close($curl);
}


$globalVCalendar = new VObject\Component\VCalendar();

foreach($arrayOfics as $key => $value){
    echo $value."<br><br>";
      $anCalendar = VObject\Reader::read($value);

      foreach($anCalendar->VEVENT as $event) {
       echo (string)$event->SUMMARY."<br>";
        $globalVCalendar->add($event);
      }
      file_put_contents('./'.$key.'.ics', $anCalendar->serialize());
}

file_put_contents('./all.ics', $globalVCalendar->serialize());



// Parse calendar file
// $calendar = VObject\Reader::read($ics);

// Put the resulting ICS to .public.ics
// file_put_contents('./public.ics', $calendar->serialize());

?>
