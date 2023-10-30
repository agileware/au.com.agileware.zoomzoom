<?php

class CRM_CivirulesConditions_Event_NoZoomMeeting extends CRM_CivirulesConditions_Event_HasZoomMeeting {
	/**
	 * @inheritdoc
	 */
	public function isConditionValid( CRM_Civirules_TriggerData_TriggerData $triggerData ) {
		return !parent::isConditionValid( $triggerData );
	}

}