<?php

class CRM_CivirulesActions_Participant_ZoomCancelParticipant extends CRM_Civirules_Action {

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

    // Look up the custom field ID for the Zoom Meeting field
    $zoom_id_field_id = CRM_Core_BAO_CustomField::getCustomFieldID('zoom_id', 'zoom', TRUE);
    $civicrm_zoom_id = $event[$zoom_id_field_id];
    // Check if this Event has a Zoom ID set
    if (empty($civicrm_zoom_id)) {
      return FALSE;
    }

    // Get the related contact
    // @TODO If the contact is invalid then exit
    $contact_id = $triggerData->getContactId();
    $contact = civicrm_api3('Contact', 'getsingle', ['id' => $contact_id]);

    $participant_id = $triggerData->getEntityData('Participant')['participant_id'];

    $participant_record = \Civi\Api4\Participant::get()
      ->selectRowCount()
      ->addSelect('zoom_registrant.registrant_id')
      ->addWhere('id', '=', $participant_id)
      ->setLimit(1)
      ->execute();

    if ($participant_record->rowCount != 0) {
      $registrant_id = $participant_record[0]['zoom_registrant.registrant_id'];
      $registrant_email = $contact['email'];
      CRM_Zoomzoom_Zoom::cancelZoomRegistration($civicrm_zoom_id, $registrant_id, $registrant_email);
    }
  }

  /**
   * Validates whether this action works with the selected trigger.
   *
   * @param CRM_Civirules_Trigger $trigger
   * @param CRM_Civirules_BAO_Rule $rule
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
   * @return bool
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return FALSE;
  }
}
