<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>

<?php

// Use "composer require sabre/vobject" to get the required libraries
require_once('../3rdparty/autoload.php');

use Sabre\VObject;

// Configure your data
$configs = include('config.php');
$remoteHost = $configs['host'];
$username = $configs['username'];
$password = $configs['password'];
$calendarsArrayOfUrl = $configs['calendars'];

$arrayOfics = array();

$arrayOfEvent = array();
for ($i = 1; $i <= 31; $i++) {
    $arrayOfEvent[$i] = array();
}



foreach($calendarsArrayOfUrl as $value){
  $curl = curl_init($remoteHost . $value . '?export&start=1475280000&end=1477872000&expand=1');
  curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  $result = curl_exec($curl);
  if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200){
    $arrayOfics[explode("/", $value)[5]] = $result;
  }
  curl_close($curl);
}


$globalVCalendar = new VObject\Component\VCalendar();

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

      file_put_contents('./print-'.$key.'.ics', $anCalendar->serialize());

    } catch (ParseException $e){
      //echo $e;
    }

}

foreach($globalVCalendar->VEVENT as $event) {
  $dateBegin = $event->DTSTART->getDateTime();
  $dateBegin->setTimezone(new \DateTimeZone("America/New_York"));
  $arrayOfEvent[intval($dateBegin->format('d'))][] = $dateBegin->format('H:i').' '.$event->SUMMARY;
}


$latex = "";

foreach($arrayOfEvent as $day) {
  $latex = $latex."\day{}{" ;
  if (count($day)){
    $latex = $latex . join(' \\\\ ', $day);
    $latex = $latex ."}\n";
  }else {
    $latex = $latex ."\\vspace{2.5cm}}\n";
  }
}

echo "<pre>".$latex."</pre>";

file_put_contents('./print-all.ics', $globalVCalendar->serialize());

?>

</body>
</html>
