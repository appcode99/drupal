<?php
 
namespace Drupal\event_timer;

use \Datetime;
use \DateTimeZone;

class EventTimerService {
	
	private function validateDate($date, $format = 'Y-m-d\TH:i:s') {
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) === $date;
	}
	
	private function isItToday($nowDate, $eventDate) {
		return $nowDate->format('d') == $eventDate->format('d') ? true : false;
	}
	
	public function getValue($dateTime) {
		$return = array('success' => 0, 'val' => null);

		if( $this->validateDate($dateTime) ) {
			$nowDate = new DateTime();
			$nowDate->settime(0,0);
			$eventDate = new DateTime(str_replace('T', ' ', $dateTime), new DateTimeZone(DATETIME_STORAGE_TIMEZONE)); // storage timezone UTC
			$eventDate->setTimezone(new DateTimeZone(drupal_get_user_timezone())); // user local timezone
			$eventDate->settime(0,0);
			$interval = $nowDate->diff($eventDate);
			
			if( $interval = $nowDate->diff($eventDate) ) {
				$return['success'] = 1;
				switch($interval->format('%R')) {
					case('+'):
						$return['val'] = $this->isItToday($nowDate, $eventDate) ? 0 : $interval->days;
						break;
					case('-'):
						$return['val'] = $this->isItToday($nowDate, $eventDate) ? 0 : -1;
						break;
				}
				return $return;
			}
			else
				return $return;
		}
		else
			return $return;
	
	}
	
}