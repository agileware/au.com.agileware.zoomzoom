<?php
/**
 * Class for CiviRules Create Zoom Event Action Form
 *
 */

class CRM_CivirulesActions_Zoom_Form_CreateMeetingFromEvent extends CRM_CivirulesActions_Form_Form {

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {

    $zoomMeetingFieldTrueFalse = [
      'true'=>ts('True'),
      'false'=>ts('False'),
    ];

    $zoomMeetingJoinBeforeHost = [
      '0'=>ts('Allow participant to join anytime'),
      '5'=>ts('Allow participant to join 5 minutes before meeting start time'),
      '10'=>ts('Allow participant to join 10 minutes before meeting start time'),
    ];

    $zoomMeetingFieldApprovalType = [
      '0' =>ts('Automatically approve'),
      '1'  =>ts('Manually approve'),
      '2'  =>ts('No registration required'),
    ];

    $zoomMeetingFieldAudio = [
      'both' =>ts('Both VOIP and Phone'),
      'telephony' =>ts('Phone'),
      'voip' =>ts('VOIP'),
    ];

    $zoomMeetingFieldAutoRecording = [
      'local' =>ts('Local recording'),
      'cloud' =>ts('Cloud recording'),
      'none' =>ts('Recording disabled'),
    ];

    $this->add('hidden', 'rule_action_id');

    $this->add('text', 'duration', ts('Webinar duration (minutes). Used for scheduled webinars only'), [], TRUE);

    // Note: Zoom timezone is defined by the system or CiviCRM Event Timezone - not exposed as an option on this form

    $this->add('text', 'schedule_for', ts('If you would like to schedule this meeting for someone else in your account, provide the Zoom user id or email address of the user here'), [], FALSE);

    // @TODO Implement password validation
    $this->add('text', 'password', ts('Webinar passcode. Passcode may only contain the following characters: [a-z A-Z 0-9 @ - _ * !]. Max of 10 characters.'), [], FALSE);

    $this->add('select', 'host_video', ts('Start video when host joins webinar'), $zoomMeetingFieldTrueFalse, TRUE);
    $this->add('select', 'participant_video', ts('Start video when participants join the meeting'),$zoomMeetingFieldTrueFalse, TRUE);

    $this->add('select', 'join_before_host', ts('Allow participants to join the meeting before the host starts the meeting'), $zoomMeetingFieldTrueFalse, TRUE);
    // @TODO This field should be conditionally shown in join_before_host is TRUE
    $this->add('select', 'jbh_time', ts('Indicate time limits within which a participant may join a meeting before a host. Only applicable if participants can join before the host'), $zoomMeetingJoinBeforeHost, TRUE);

    $this->add('select', 'mute_upon_entry', ts('Mute participants upon entry'), $zoomMeetingFieldTrueFalse, TRUE);
    $this->add('select', 'watermark', ts('Add watermark when viewing a shared screen'), $zoomMeetingFieldTrueFalse, TRUE);

    $this->add('select', 'approval_type', ts('Approval type'),$zoomMeetingFieldApprovalType, TRUE);
    $this->add('select', 'audio', ts('Determine how participants can join the audio portion of the meeting'), $zoomMeetingFieldAudio, TRUE);
    $this->add('select', 'auto_recording', ts('Automatic recording'), $zoomMeetingFieldAutoRecording, TRUE);
    $this->add('text', 'alternative_hosts', ts('Alternative host emails or IDs. Multiple values separated by comma'), [], FALSE);
    $this->add('select', 'close_registration', ts('Close registration after event date'),$zoomMeetingFieldTrueFalse, TRUE);

    $this->add('select', 'waiting_room', ts('Enable waiting room. If this value is enable, it will override the join before host option'),$zoomMeetingFieldTrueFalse, TRUE);

    // @TODO This may be problematic depending on the value saved
    // Zoom expects values from this list https://marketplace.zoom.us/docs/api-reference/other-references/abbreviation-lists#countries
    $this->add('select', 'global_dial_in_countries', ts('List of global dial-in countries'), $this->getCountries(), TRUE,
      [
        'id' => 'country_ids',
        'multiple' => 'multiple',
        'class' => 'crm-select2',
      ]);

    $this->add('text', 'contact_name', ts('Contact name for registration'), [], FALSE);
    $this->add('text', 'contact_email', ts('Contact email for registration'), [], FALSE);

    $this->add('select', 'registrants_email_notification', ts('Send email notifications to registrants about approval, cancellation, denial of the registration'), $zoomMeetingFieldTrueFalse, TRUE);

    $this->add('select', 'meeting_authentication', ts('Only authenticated users can join meeting'), $zoomMeetingFieldTrueFalse, TRUE);

    // Note: Not implemented option for authentication_option - unclear how this would be defined here.

    $this->add('text', 'authentication_domains', ts('Meeting authentication domains. This option, allows you to specify the rule so that Zoom users, whose email address contains a certain domain, can join the Webinar. You can either provide multiple domains, using a comma in between and/or use a wildcard for listing domains'), [], FALSE);

    $this->add('select', 'show_share_button', ts('Show social share buttons on the registration page'),$zoomMeetingFieldTrueFalse, TRUE);
    $this->add('select', 'allow_multiple_devices', ts('Allow attendees to join from multiple devices'),$zoomMeetingFieldTrueFalse, TRUE);
    $this->add('select', 'alternative_hosts_email_notification', ts('Flag to determine whether to send email notifications to alternative hosts'),$zoomMeetingFieldTrueFalse, TRUE);

    $this->addButtons([
      ['type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,],
      ['type' => 'cancel', 'name' => ts('Cancel')],
    ]);
  }

  /**
   * Method to get the country list
   *
   * @return array
   */
  private function getCountries() {
    $countries = [];
    try {
      $apiCountries = civicrm_api3('Country', 'get', [
        'return' => ["iso_code", "name"],
        'options' => ['limit' => 0, 'sort' => "name"],
      ]);
      foreach ($apiCountries['values'] as $apiCountryId => $apiCountry) {
        $countries[$apiCountryId] = $apiCountry['name'];
      }
    } catch (CiviCRM_API3_Exception $ex) {
    }
    return $countries;
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $defaultValues = parent::setDefaultValues();
    $data = unserialize($this->ruleAction->action_params);

    // Load the values
    foreach (CRM_Zoomzoom_Constants::zoomMeetingFields as $setting) {
      if (!empty($data[$setting])) {
        $defaultValues[$setting] = $data[$setting];
      }
    }

    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule condition not found
   * @access public
   */
  public function postProcess() {
    // Save the values
    foreach (CRM_Zoomzoom_Constants::zoomMeetingFields as $setting) {
      $data[$setting] = $this->_submitValues[$setting];
    }

    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();
    parent::postProcess();
  }

}
