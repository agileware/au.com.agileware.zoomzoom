<?php

use Civi\Api4\Event;

class CRM_CivirulesConditions_Event_NoZoomMeeting extends CRM_CivirulesConditions_Event_HasZoomMeeting {
	/**
	 * @inheritdoc
	 */
	public function isConditionValid( CRM_Civirules_TriggerData_TriggerData $triggerData ) {
    // Check lock
    if ( CRM_CivirulesActions_Event_ZoomCreateMeetingFromEvent::$lock ) {
      return false;
    }

		$event_id = $triggerData->getEntityData('Event')['id'];

    $count = Event::get(FALSE)
          ->selectRowCount()
          ->addWhere('id', '=', $event_id )
          ->addWhere('zoom.zoom_id', 'IS EMPTY')
          ->execute()
          ->count();

    return ($count > 0);
	}

}
