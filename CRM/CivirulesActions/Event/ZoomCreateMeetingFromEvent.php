<?php

class CRM_CivirulesActions_Event_ZoomCreateMeetingFromEvent extends CRM_Civirules_Action {

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
    $actionParams = $this->getActionParameters();

    $params['topic'] = $event['title'];
    $params['type'] = CRM_Zoomzoom_Constants::zoomMeetingType;

    // Check if CiviCRM Event Timezone is available for this Event, if so use it
    if (!empty($event['event_tz'])) {
      $params['start_time'] = CRM_Utils_Date::customFormat(CRM_Utils_Date::convertTimeZone($event['event_start_date'], $event['event_tz'], NULL, 'YmdHis'), '%Y-%m-%dT%H:%M:00');
      $params['timezone'] = $event['event_tz'];
    }
    else {
      // Otherwise, use the CiviCRM default timezone for the Event
      $params['start_time'] = CRM_Utils_Date::customFormat($event['event_start_date'], '%Y-%m-%dT%H:%M:00');
      $params['timezone'] = CRM_Core_Config::singleton()->userSystem->getTimeZoneString();
    }

    $params['duration'] = $actionParams['duration'];
    unset($actionParams['duration']);

    $params['schedule_for'] = $actionParams['schedule_for'];
    unset($actionParams['schedule_for']);

    $params['password'] = $actionParams['password'];
    unset($actionParams['password']);

    $params['agenda'] = $event['summary'];

    // Convert the CiviCRM Country Code to ISO Country Code
    $global_dial_in_countries = [];
    if (!empty($actionParams['global_dial_in_countries'])) {
      foreach ($actionParams['global_dial_in_countries'] as $global_dial_in_country) {
        $global_dial_in_countries[] = CRM_Core_PseudoConstant::countryIsoCode($global_dial_in_country);
      }
      $actionParams['global_dial_in_countries'] = $global_dial_in_countries;
    }
    else {
      // Unlikely ever to meet this condition, but just in case
      unset($actionParams['global_dial_in_countries']);
    }

    $params['settings'] = $actionParams;

    // Allow participant to join anytime
    $params['settings']['jbh_time'] = '0';

    // Create the Zoom Meeting
    $zoom_details = CRM_Zoomzoom_Zoom::createZoom('meetings', $params);

    // Update Event with new Zoom details
    if (!empty($zoom_details)) {

      \Civi\Api4\Event::update()
        ->addWhere('id', '=', $event['id'])
        ->addValue('zoom.zoom_id', 'm' . $zoom_details['id'])
        ->addValue('zoom.password', $zoom_details['password'])
        ->addValue('zoom.start_url', $zoom_details['start_url'])
        ->addValue('zoom.join_url', $zoom_details['join_url'])
        ->addValue('zoom.registration_url', $zoom_details['registration_url'])
        // Writing HTML does not work in APIv4 read on...
        // ->addValue('zoom.global_dial_in_numbers', $zoom_details['global_dial_in_numbers'])
        ->execute();

      /* Two API calls required because APIv4 encodes HTML characters without exception which is a bit annoying
      https://lab.civicrm.org/dev/core/-/issues/1328
      Other interesting history:
      https://chat.civicrm.org/civicrm/channels/documentation/mjcjfn5w4jy8jnjkx5zqjsnkty
      https://github.com/civicrm/civicrm-core/blob/6bb0783dbe4a900c405254691e2ab1e79a7b60f1/CRM/Utils/API/HTMLInputCoder.php#L119
      https://github.com/civicrm/civicrm-core/blob/5.36.1/Civi/Api4/Generic/Traits/CustomValueActionTrait.php#L54-L57
      https://github.com/civicrm/civicrm-core/blob/5.36.1/Civi/Api4/Utils/FormattingUtil.php#L43-L75
      */

      // Look up the custom field IDs
      $zoom_global_dial_in_numbers_field_id = CRM_Core_BAO_CustomField::getCustomFieldID('global_dial_in_numbers', 'zoom', TRUE);

      $result = civicrm_api3('Event', 'create', [
        'id' =>  $event['id'],
        $zoom_global_dial_in_numbers_field_id  => $zoom_details['global_dial_in_numbers'],
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
    return CRM_Utils_System::url('civicrm/civirule/form/action/zoom/createmeetingfromevent', 'rule_action_id='.$ruleActionId);
  }
}
