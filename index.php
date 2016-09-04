<?php
// Use "composer require sabre/vobject" to get the required libraries
require_once('./3rdparty/autoload.php');

use Sabre\VObject;

// Configure your data
$configs = include('config.php');
$remoteHost = $configs['host'];
$username = $configs['username'];
$password = $configs['password'];

$calendarName = 'clbrations';

$calendarsArray = array("clbrations","tudes");

// Get ownCloud calendar



$curl = curl_init($remoteHost . '/remote.php/caldav/calendars/'.$username.'/'.$calendarName.'?export');

curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

curl_setopt($curl, CURLOPT_VERBOSE, true);

$ics = curl_exec($curl);

curl_close($curl);



foreach($calendarsArray as &$value){

  echo $value."<br>";

  $curl = curl_init($remoteHost . '/remote.php/caldav/calendars/'.$username.'/'.$value.'?export');

  curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);

  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

  curl_setopt($curl, CURLOPT_VERBOSE, true);

  $value = curl_exec($curl);

  curl_close($curl);

}



$globalVCalendar = new VObject\Component\VCalendar();



foreach($calendarsArray as $value){

      $anCalendar = VObject\Reader::read($value);

      //$nameCalendar = $anCalendar

      foreach($anCalendar->VEVENT as $event) {
var_dump($event);
echo "<br><br>";
        //$globalVCalendar->add('VEVENT', $event);

      }

}





file_put_contents('./stmarc.ics', $globalVCalendar->serialize());



// Parse calendar file



$calendar = VObject\Reader::read($ics);







// Put the resulting ICS to /var/www/public.ics



file_put_contents('./public.ics', $calendar->serialize());





?>
