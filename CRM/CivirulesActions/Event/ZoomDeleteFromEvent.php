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

   // Delete the Zoom
    CRM_Zoomzoom_Zoom::deleteZoom($civicrm_zoom_id);
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
