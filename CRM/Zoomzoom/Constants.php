<?php


/**
 * Zoom Zoom Constants
 *
 * @package CiviCRM
 */
class CRM_Zoomzoom_Constants {

  /*
  * Zoom Meeting, registration_type, 2 = Scheduled Meeting
  */

  const zoomMeetingType = 2;

  const zoomMeetingFields = [
    'duration',
    'schedule_for',
    'password',
    'host_video',
    'participant_video',
    'join_before_host',
    'mute_upon_entry',
    'watermark',
    'use_pmi',
    'approval_type',
    'audio',
    'auto_recording',
    'alternative_hosts',
    'close_registration',
    'waiting_room',
    'global_dial_in_countries',
    'contact_name',
    'contact_email',
    'registrants_email_notification',
    'meeting_authentication',
    'authentication_domains',
    'show_share_button',
    'allow_multiple_devices',
    'alternative_hosts_email_notification',
  ];

  /*
   * Zoom Webinar, registration_type, 5 = Webinar (non-recurring)
   * Hard-coding this option means that registration_type option is also not available
   */
  const zoomWebinarType = 5;

  const zoomWebinarFields = [
    'duration',
    'password',
    'host_video',
    'panelists_video',
    'practice_session',
    'hd_video',
    'approval_type',
    'audio',
    'auto_recording',
    'alternative_hosts',
    'close_registration',
    'show_share_button',
    'allow_multiple_devices',
    'on_demand',
    'global_dial_in_countries',
    'contact_name',
    'contact_email',
    'registrants_restrict_number',
    'post_webinar_survey',
    'survey_url',
    'registrants_email_notification',
    'meeting_authentication',
    'authentication_domains',
    'qa_enable',
    'qa_allow_anonymous_questions',
    'qa_answer_questions',
    'qa_attendees_can_upvote',
    'qa_attendees_can_comment',
  ];

}
