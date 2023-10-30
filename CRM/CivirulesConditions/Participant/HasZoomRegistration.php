<?php

use Civi\Api4\Participant;

class CRM_CivirulesConditions_Participant_HasZoomRegistration extends CRM_Civirules_Condition {
	/**
	 * @inheritdoc
	 */
	public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
		return $trigger->doesProvideEntity('Participant');
	}

	/**
	 * @inheritDoc
	 */
	public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
		$participant_id = $triggerData->getEntitydata('Participant')['id'];
		$count = Participant::get(FALSE)
		                    ->selectRowCount()
		                    ->addWhere('id', '=', $participant_id )
		                    ->addWhere('zoom_registrant.registrant_id', 'IS NOT EMPTY')
		                    ->execute()
		                    ->count();

		return ($count > 0);
	}

	/**
	 * @inheritDoc
	 */
	public function getExtraDataInputUrl($ruleConditionId) {
		return FALSE;
	}
}