<?php

class CRM_CivirulesActions_Participant_ZoomDeleteParticipant extends CRM_Civirules_Action {

  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   *
   * @access public
   *
   */
  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $event = $triggerData->getEntityData('Event');

    $civicrm_zoom_id = CRM_Zoomzoom_Zoom::getEventZoomMeetingId($event['id']);

    // Check if this Event has a Zoom ID set
    if (empty($civicrm_zoom_id)) {
      return FALSE;
    }

    $participant_id = $triggerData->getEntityData('Participant')['participant_id'];

    $participant_record = \Civi\Api4\Participant::get()
      ->selectRowCount()
      ->addSelect('zoom_registrant.registrant_id')
      ->addWhere('id', '=', $participant_id)
      ->setLimit(1)
      ->execute();

    if ($participant_record->rowCount != 0) {
      $registrant_id = $participant_record[0]['zoom_registrant.registrant_id'];
      CRM_Zoomzoom_Zoom::deleteZoomRegistration($civicrm_zoom_id, $registrant_id);

      // Remove the Zoom details from the registration
      // SQL query required to prevent CiviRules recursion due to Participant changed trigger
      CRM_Core_DAO::executeQuery('REPLACE INTO civicrm_value_zoom_registrant (`zoom_id`, `registrant_id`, `join_url`, `entity_id`) VALUES(NULL, NULL, NULL, %1)', [
        '1' => [$participant_id, 'Integer'],
      ]);
    }
  }

  /**
   * Validates whether this action works with the selected trigger.
   *
   * @param CRM_Civirules_Trigger $trigger
   * @param CRM_Civirules_BAO_Rule $rule
   *
   * @return bool
   */
  public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
    $entities = $trigger->getProvidedEntities();
    return isset($entities['Participant']);
  }

  /**
   * Method to return the url for additional form processing for action
   * and return false if none is needed
   *
   * @param int $ruleActionId
   *
   * @return bool
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return FALSE;
  }

}
