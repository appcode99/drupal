<?php

/**
 * Provides Block
 * @Block(
 *  id = "event_timer_block",
 *  admin_label = @Translation("Event Timer block"),
 *  category = @Translation("Event Timer")
 * )
 */
 
namespace Drupal\event_timer\Plugin\Block;

use Drupal;
use Drupal\Core\Block\BlockBase;

class EventTimerBlock extends BlockBase  {
	
	private function getMsgFromDate() {		
		$node = Drupal::routeMatch()->getParameter('node');
		if( $node instanceof \Drupal\node\NodeInterface ) {
			$dateArr = $node->field_event_date->getValue();
			$service = Drupal::service('event_timer_service');
			$return = $service->getValue($dateArr[0]['value']);
			
			if( $return['success'] ) {
				switch( $return['val'] ) {
					case(-1):
						return 'This event already passed';
						break;
					case(0):
						return 'This event is happening today';
						break;
					default:
						$wordDay = $return['val'] == 1 ? 'day' : 'days';
						return $return['val'] .' '. $wordDay .' left until event starts';
						break;
				}
			}
			else
				return 'Error';
		}
		else
			return 'Error getting node parameters :(';
	}
	

	public function build() {
		Drupal::service('page_cache_kill_switch')->trigger(); // disable cache for anonymous users..
		return array(
			'#markup' => $this->getMsgFromDate(),
			'#cache' => [
				'max-age' => 0
			]
		);
	}
	
}