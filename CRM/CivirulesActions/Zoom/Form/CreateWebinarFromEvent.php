<?php
/**
 * Class for CiviRules Create Zoom Event Action Form
 *
 */

class CRM_CivirulesActions_Zoom_Form_CreateWebinarFromEvent extends CRM_CivirulesActions_Form_Form {

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {

    $zoomWebinarFieldTrueFalse = [
      'true' => ts('True'),
      'false' => ts('False'),
    ];

    $zoomWebinarFieldApprovalType = [
      '0' => ts('Automatically approve'),
      '1' => ts('Manually approve'),
      '2' => ts('No registration required'),
    ];

    $zoomWebinarFieldRegistrationType = [
      '1' => ts('Attendees register once and can attend any of the webinar sessions'),
      '2' => ts('Attendees need to register for each session in order to attend'),
      '3' => ts('Attendees register once and can choose one or more sessions to attend'),
    ];

    $zoomWebinarFieldAudio = [
      'both' => ts('Both VOIP and Phone'),
      'telephony' => ts('Phone'),
      'voip' => ts('VOIP'),
    ];

    $zoomWebinarFieldAutoRecording = [
      'local' => ts('Local recording'),
      'cloud' => ts('Cloud recording'),
      'none' => ts('Recording disabled'),
    ];

    $zoomWebinarFieldQaAnswerQuestions = [
      'only' => ts('Attendees are able to view answered questions only'),
      'all' => ts('Attendees are able to view all questions submitted in the Q&A'),
    ];

    $this->add('hidden', 'rule_action_id');

    $this->add('text', 'duration', ts('Webinar duration (minutes). Used for scheduled webinars only'), [], TRUE);

    // Note: Zoom timezone is defined by the system or CiviCRM Event Timezone - not exposed as an option on this form

    // @TODO Implement password validation
    $this->add('text', 'password', ts('Webinar passcode. Passcode may only contain the following characters: [a-z A-Z 0-9 @ - _ * !]. Max of 10 characters.'), [], FALSE);

    $this->add('select', 'host_video', ts('Start video when host joins webinar'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('select', 'panelists_video', ts('Start video when panelists join webinar'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('select', 'practice_session', ts('Enable practice session'), $zoomWebinarFieldTrueFalse, TRUE);

    $this->add('select', 'hd_video', ts('Default to HD video'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('select', 'approval_type', ts('Approval type'), $zoomWebinarFieldApprovalType, TRUE);
    $this->add('select', 'audio', ts('Determine how participants can join the audio portion of the meeting'), $zoomWebinarFieldAudio, TRUE);
    $this->add('select', 'auto_recording', ts('Automatic recording'), $zoomWebinarFieldAutoRecording, TRUE);
    $this->add('text', 'alternative_hosts', ts('Alternative host emails or IDs. Multiple values separated by comma'), [], FALSE);

    $this->add('select', 'close_registration', ts('Close registration after event date'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('select', 'show_share_button', ts('Show social share buttons on the registration page'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('select', 'allow_multiple_devices', ts('Allow attendees to join from multiple devices'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('select', 'on_demand', ts('Make the webinar on-demand'), $zoomWebinarFieldTrueFalse, TRUE);

    /*
     * @TODO This may be problematic depending on the value saved - assuming that Zoom uses ISO Country Codes
     * Zoom expects values from this list https://marketplace.zoom.us/docs/api-reference/other-references/abbreviation-lists#countries
     */
    $this->add('select', 'global_dial_in_countries', ts('List of global dial-in countries'), $this->getCountries(), TRUE,
      [
        'id' => 'country_ids',
        'multiple' => 'multiple',
        'class' => 'crm-select2',
      ]);

    $this->add('text', 'contact_name', ts('Contact name for registration'), [], FALSE);
    $this->add('text', 'contact_email', ts('Contact email for registration'), [], FALSE);

    // @TODO this value must be a number 0 to 20000 maximum
    $this->add('text', 'registrants_restrict_number', ts('Restrict number of registrants for a webinar. Set to 0 for no limit'), [], FALSE);

    $this->add('select', 'post_webinar_survey', ts('Zoom will open a survey page in attendees browsers after leaving the webinar'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('text', 'survey_url', ts('Survey url for post webinar survey'), [], FALSE);

    $this->add('select', 'registrants_email_notification', ts('Send email notifications to registrants about approval, cancellation, denial of the registration'), $zoomWebinarFieldTrueFalse, TRUE);

    $this->add('select', 'meeting_authentication', ts('Only authenticated users can join meeting'), $zoomWebinarFieldTrueFalse, TRUE);

    // Note: Not implemented option for authentication_option - unclear how this would be defined here.

    $this->add('text', 'authentication_domains', ts('Meeting authentication domains. This option, allows you to specify the rule so that Zoom users, whose email address contains a certain domain, can join the Webinar. You can either provide multiple domains, using a comma in between and/or use a wildcard for listing domains'), [], FALSE);

    // Q&A Webinar settings
    $this->add('select', 'qa_enable', ts('Enable Questions & Answers for webinar'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('select', 'qa_allow_anonymous_questions', ts('Allow participants to send anonymous questions'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('select', 'qa_answer_questions', ts('Indicate whether you want attendees to be able to view answered questions only or view all questions'), $zoomWebinarFieldQaAnswerQuestions, TRUE);
    $this->add('select', 'qa_attendees_can_upvote', ts('Attendees can click the thumbs up button to bring popular questions to the top of the Q&A window'), $zoomWebinarFieldTrueFalse, TRUE);
    $this->add('select', 'qa_attendees_can_comment', ts('Attendees can answer questions or leave a comment in the question thread'), $zoomWebinarFieldTrueFalse, TRUE);

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
        'return' => ["id", "name"],
        'options' => ['limit' => 0, 'sort' => "name"],
      ]);
      foreach ($apiCountries['values'] as $apiCountryId => $apiCountry) {
        $countries[$apiCountryId] = $apiCountry['name'];
      }
    } catch (CRM_Core_Exception $ex) {
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
    foreach (CRM_Zoomzoom_Constants::zoomWebinarFields as $setting) {
      if (!empty($data[$setting])) {
        $defaultValues[$setting] = $data[$setting];
      }
    }

    return $defaultValues;
  }


  /**
   * If your form requires special validation, add one or more callbacks here
   */
  public function addRules() {
    $this->addFormRule([__CLASS__, 'survey_url']);
  }

  /**
   * Custom validation callback
   */
  public static function survey_url($values) {
    $errors = [];
    $check_http = stripos($values['survey_url'], 'https');
    if ($values['post_webinar_survey'] == 'true' && $check_http === FALSE) {
      $errors['survey_url'] = ts('Please provide a https URL for the webinar survey OR set the survey page option to false.');
    }
    return empty($errors) ? TRUE : $errors;
  }

  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule condition not found
   * @access public
   */
  public function postProcess() {
    // Save the values
    foreach (CRM_Zoomzoom_Constants::zoomWebinarFields as $setting) {
      $data[$setting] = $this->_submitValues[$setting];
    }

    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();
    parent::postProcess();
  }

}
