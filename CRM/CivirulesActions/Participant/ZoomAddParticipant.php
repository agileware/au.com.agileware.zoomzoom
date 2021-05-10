<?php

class CRM_CivirulesActions_Participant_ZoomAddParticipant extends CRM_Civirules_Action {

  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   *
   * @access public
   *
   */
  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $contact_id = $triggerData->getContactId();
    $event = $triggerData->getEntityData('Event');

    $civicrm_zoom_id = CRM_Zoomzoom_Zoom::getEventZoomMeetingId($event['id']);

    // Check if this Event has a Zoom ID set
    if (empty($civicrm_zoom_id)) {
      return FALSE;
    }

    // Get the related contact
    $contact = civicrm_api3('Contact', 'getsingle', ['id' => $contact_id]);

    // @TODO If the contact is invalid then exit

    $participant_id = $triggerData->getEntityData('Participant')['participant_id'];

    $params = [
      'email' => $contact['email'],
      'first_name' => $contact['first_name'],
      'last_name' => $contact['last_name'],
      'address' => $contact['street_address'],
      'city' => $contact['city'],
      'country' => $contact['country'],
      'state' => $contact['state_province'],
      'zip' => $contact['postal_code'] ?? NULL,
      'phone' => $contact['phone'],
      'org' => $contact['current_employer'],
      'job_title' => $contact['job_title'],
      'custom_questions' => [
        [
          'title' => 'participant_id',
          'value' => $participant_id,
        ],
        [
          'title' => 'contact_id',
          'value' => $contact_id,
        ],
      ],
    ];

    CRM_Zoomzoom_Zoom::createZoomRegistration($civicrm_zoom_id, $participant_id, $params);
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
