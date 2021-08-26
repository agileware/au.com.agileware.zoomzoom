<?php

class CRM_CivirulesActions_Event_ZoomDeleteFromEvent extends CRM_Civirules_Action {

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

    // Delete the Zoom and if deleted, then remove Zoom details from Event
    if (CRM_Zoomzoom_Zoom::deleteZoom($civicrm_zoom_id)) {
      \Civi\Api4\Event::update()
        ->addWhere('id', '=', $event['id'])
        ->addValue('zoom.zoom_id', '')
        ->addValue('zoom.password', '')
        ->addValue('zoom.start_url', '')
        ->addValue('zoom.join_url', '')
        ->addValue('zoom.registration_url', '')
        ->addValue('zoom.global_dial_in_numbers', '')
        ->execute();
    }

    // Remove Zoom information from all related Participant records
    $participant_records = \Civi\Api4\Participant::get()
      ->addSelect('id')
      ->addWhere('event_id', '=', $event['id'])
      ->execute();

    foreach ($participant_records as $participant_record) {
      // Remove the Zoom details from the Participant
      // SQL query required to prevent CiviRules recursion due to Participant changed trigger
      CRM_Core_DAO::executeQuery('REPLACE INTO civicrm_value_zoom_registrant (`zoom_id`, `registrant_id`, `join_url`, `entity_id`) VALUES(NULL, NULL, NULL, %1)', [
        '1' => [$participant_record['id'], 'Integer'],
      ]);
    }
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    return '';
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
    return isset($entities['Event']);
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
