<?php

use Civi\Api4\Event;
use Civi\Api4\Participant;
use Civi\Token\Event\TokenRegisterEvent;
use Civi\Token\Event\TokenValueEvent;
use Civi\Token\TokenRow;
use CRM_Zoomzoom_ExtensionUtil as E;

class CRM_Zoomzoom_Tokens {
	const TOKEN = 'zoom';

	/**
	 * @param \Civi\Token\Event\TokenRegisterEvent $entity
	 * @param string $field Machine name for the token
	 * @param string $label the translated token label
	 *
	 * @return string
	 */
	protected static function registerCtx(TokenRegisterEvent $entity, string $field, string $label){
		$entity->register($field, $label . ' :: ' . E::ts('Zoom (Portable)') );
	}

	public static function register(TokenRegisterEvent $e) {
		$context = $e->getTokenProcessor()->context;
		if(!is_array($context['schema'] ?? NULL))
			return;

		// Register Zoom tokens for event
		if (in_array('eventId', $context['schema'])) {
			$entity = $e->entity(self::TOKEN);
			self::registerCtx($entity, 'zoom_id', E::ts('Zoom ID'));
			self::registerCtx($entity, 'join_url', E::ts('Zoom Join URL'));
			self::registerCtx($entity, 'global_dial_in_numbers', E::ts('Zoom Dial-in Numbers'));
		}

		// Register Zoom tokens for participant
		if (in_array('participantId', $context['schema'])) {
			$entity = $e->entity( self::TOKEN );
			self::registerCtx($entity, 'registrant_id', E::ts('Zoom Registrant ID'));
		}
	}

	public static function evaluate(TokenValueEvent $e) {
		foreach($e->getRows() as $row) {
			self::evaluateRow($row);
		}
	}

	protected static function evaluateRow(TokenRow $row) {
		if (empty($row->context['eventId']) && empty($row->context['participantId'])) {
			return;
		}
		$row->format( 'text/html' );
		try {
			if (!empty($row->context['eventId'])) {
				$event = Event::get( FALSE )
				              ->addWhere('id', '=', $row->context['eventId'])
				              ->addSelect('zoom.zoom_id', 'zoom.join_url', 'zoom.global_dial_in_numbers')
				              ->execute()
				              ->first();
				$row->tokens(self::TOKEN, 'zoom_id', $event['zoom.zoom_id'] ?? '');
				$row->tokens(self::TOKEN, 'join_url', $event['zoom.join_url'] ?? '' );
				$row->tokens(self::TOKEN, 'global_dial_in_numbers', $event['zoom.global_dial_in_numbers'] ?? '');
			}
			if (!empty($row->context['participantId'])) {
				$participant = Participant::get(FALSE)
					->addWhere('id', '=', $row->context['participantId'])
					->addSelect('zoom_registrant.registrant_id', 'zoom_registrant.join_url')
					->execute()
					->first();
				$row->tokens(self::TOKEN, 'registrant_id', $participant['zoom_registrant.registrant_id'] ?? '');
				$row->tokens(self::TOKEN, 'join_url', $participant['zoom_registrant.join_url'] ?? '');
			}
		}
		catch(CRM_Core_Exception $e) {
			// ...
		}
	}
}
