<?php

use Civi\Api4\Event;

class CRM_CivirulesConditions_Event_HasZoomMeeting extends CRM_Civirules_Condition {
	/**
	 * @inheritdoc
	 */
	public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
		return $trigger->doesProvideEntity('Event');
	}

	/**
	 * @inheritDoc
	 */
	public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    // Check lock
    if ( CRM_CivirulesActions_Event_ZoomCreateMeetingFromEvent::$lock ) {
      return false;
    }

	$event_id = $triggerData->getEntityData('Event')['id'];

	$count = Event::get(FALSE)
				->selectRowCount()
				->addWhere('id', '=', $event_id )
				->addWhere('zoom.zoom_id', 'IS NOT EMPTY')
				->execute()
				->count();

	return ($count > 0);
	}

	/**
	 * @inheritDoc
	 */
	public function getExtraDataInputUrl( $ruleConditionId ) {
		return false;
	}
}
