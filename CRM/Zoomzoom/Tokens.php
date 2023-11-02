<?php

use Civi\Api4\Event;
use Civi\Token\Event\TokenRegisterEvent;
use Civi\Token\Event\TokenValueEvent;
use Civi\Token\TokenRow;
use CRM_Zoomzoom_ExtensionUtil as E;

class CRM_Zoomzoom_Tokens {
	const TOKEN = 'zoom';
	public static function register(TokenRegisterEvent $e) {
		$context = $e->getTokenProcessor()->context;
		if(!is_array($context['schema'] ?? NULL))
			return;

		// Register Zoom tokens for event
		if (in_array('eventId', $context['schema'])) {
			$entity = $e->entity(self::TOKEN);
			$entity->register('zoom_id', E::ts('Zoom ID'));
			$entity->register('join_url', E::ts('Zoom Join URL'));
			$entity->register('global_dial_in_numbers', E::ts('Zoom Dial-in Numbers'));
		}

		// Register Zoom tokens for participant
		if (in_array('participantId', $context['schema'])) {
			$entity = $e->entity( self::TOKEN );
			$entity->register('registrant_id', E::ts('Zoom Registrant ID'));
		}
	}

	public static function evaluate(TokenValueEvent $e) {
		foreach($e->getRows() as $row) {
			self::evaluateRow($row);
		}
	}

	protected static function evaluateRow(TokenRow $row) {
		try {
			if ($row->context['eventId']) {
				$row->format( 'text/html' );
				$event = Event::get( FALSE )
				              ->addWhere('id', '=', $row->context['eventId'])
				              ->addSelect('zoom.zoom_id', 'zoom.join_url', 'zoom.global_dial_in_numbers')
				              ->execute()
				              ->first();
				$row->tokens(self::TOKEN, 'zoom_id', $event['zoom.zoom_id']);
				$row->tokens(self::TOKEN, 'join_url', $event['zoom.zoom_url']);
				$row->tokens(self::TOKEN, 'global_dial_in_numbers', $event['zoom.global_dial_in_numbers']);
			}
			if ($row->context['participantId']) {
				$participant = \Civi\Api4\Participant::get(FALSE)
					->addWhere('id', '=', $row->context['participantId'])
					->addSelect('zoom_registrant.registrant_id', 'zoom_registrant.join_url')
					->execute()
					->first();
				$row->tokens(self::TOKEN, 'registrant_id', $participant['zoom_registrant.registrant_id']);
				$row->tokens(self::TOKEN, 'join_url', $participant['zoom_registrant.join_url']);
			}
		}
		catch(CRM_Core_Exception $e) {
			// ...
		}
	}
}