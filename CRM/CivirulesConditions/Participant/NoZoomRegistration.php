<?php

class CRM_CivirulesConditions_Participant_NoZoomRegistration extends CRM_CivirulesConditions_Participant_HasZoomRegistration {
	/**
	 * @inheritdoc
	 */
	public function isConditionValid( CRM_Civirules_TriggerData_TriggerData $triggerData ) {
		return !parent::isConditionValid( $triggerData );
	}

}