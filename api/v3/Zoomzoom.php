<?php

use _ExtensionUtil as E;

/**
 * zoomzoom.importzooms specification
 *
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_zoomzoom_importzooms_spec(&$spec) {
  $spec['day_offset'] = [
    'title' => 'Zoom day offset',
    'description' => 'Import Zooms scheduled after today, offset by a number of days.',
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
}

/**
 * zoomzoom.importzooms implementation
 *
 * Import Zoom Webinars and Zoom Meetings.
 * Checks for Zooms scheduled after today, offset by a number of days.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return civicrm_api3_create_success|civicrm_api3_create_error
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function civicrm_api3_zoomzoom_importzooms($params) {
  try {
    $meetings = CRM_Zoomzoom_Zoom::getZooms('meeting', $params['day_offset']);
    $webinars = CRM_Zoomzoom_Zoom::getZooms('webinar', $params['day_offset']);
    $zooms = array_merge($meetings, $webinars);

    foreach ($zooms as $zoom) {
      try {

        /*
         * Note to potential translators or people using this extension in a different language
         * Use of the $event_type here may be a problem if the name (not label) has been translated
         */

        $event_type = 'Meeting';
        if (substr($zoom['civicrm_zoom_id'], 0, 1) == 'w') {
          $event_type = 'Webinar';
        }

        // Check if this Event already exists
        $events = \Civi\Api4\Event::get()
          ->addSelect('id')
          ->addWhere('zoom.zoom_id', '=', $zoom['civicrm_zoom_id'])
          ->execute();

        // Event does not exist, create it now
        if ($events->rowCount == 0) {
          $results = \Civi\Api4\Event::create()
            ->addValue('title', $zoom['topic'])
            ->addValue('zoom.zoom_id', $zoom['civicrm_zoom_id'])
            ->addValue('start_date', date('Y-m-d H:i:s', strtotime($zoom['start_time'])))
            ->addValue('summary', $zoom['topic'])
            ->addValue('event_type_id:name', $event_type)
            ->addValue('is_public', FALSE)
            ->execute();
        }
      } catch (Exception $e) {
        continue;
      }
    }
    return civicrm_api3_create_success(TRUE, $params, 'Civizoom', 'importzooms');

  } catch (API_Exception $e) {
    $errorMessage = $e->getMessage();
    CRM_Core_Error::debug_var('Zoomzoom::importzooms', $errorMessage);
    CRM_Core_Error::debug_var('Zoomzoom::importzooms', $params);
    return civicrm_api3_create_error($errorMessage);
  }
}

/**
 * zoomzoom.importattendees specification
 *
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_zoomzoom_importattendees_spec(&$spec) {
  $spec['day_offset'] = [
    'title' => 'CiviCRM event day offset',
    'description' => 'Check CiviCRM events scheduled since the day offset.',
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
}

/**
 * zoomzoom.importattendees implementation
 *
 * Import Zoom registrations and attendees for those CiviCRM Events with a Zoom ID.
 * Checks for CiviCRM Events scheduled after today, offset by a number of days.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return civicrm_api3_create_success|civicrm_api3_create_error
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function civicrm_api3_zoomzoom_importattendees($params) {
  try {
    // Get past Events which have a Zoom ID assigned
    try {
      $events = \Civi\Api4\Event::get()
        ->addSelect('id')
        ->addSelect('zoom.zoom_id')
        ->addSelect('start_date')
        ->addWhere('zoom.zoom_id', 'IS NOT EMPTY')
        ->addWhere('start_date', '>=', date('Y-m-d', strtotime('- ' . $params['day_offset'] . ' days')))
        ->execute();

      foreach ($events as $event) {
        $api = CRM_Zoomzoom_Zoom::getZoomAPIFromCiviCRMZoomId($event['zoom.zoom_id']);
        $zoom_id = CRM_Zoomzoom_Zoom::getZoomIDFromCiviCRMZoomId($event['zoom.zoom_id']);

        // If the event is scheduled for a future date then get registrants
        if (strtotime($event['start_date']) >= strtotime('Now')) {

          // Get registrants future events
          $participants = CRM_Zoomzoom_Zoom::getRegistrants($api, $zoom_id);

          // Verify the participants exist as contacts and added to the event
          if (!empty($participants)) {
            foreach ($participants as $participant) {
              // Zoom uses inconsistent field names for registrants and participants
              $registration_details['registration_date'] = strtotime($participant['create_time']);
              $registration_details['first_name'] = $participant['first_name'];
              $registration_details['last_name'] = $participant['last_name'];
              $registration_details['email'] = $participant['email'];
              $registration_details['zoom_id'] = $participant['id'];
              $registration_details['zoom_join_url'] = $participant['join_url'];
              // @TODO This may actually be 'Pending approval' see CIVIZOOM-8
              $registration_details['status'] = 'Registered';
              $registration_details['event'] = $event;

              CRM_Zoomzoom_Zoom::updateCiviCRMParticipant($registration_details);
            }
          }
        }
        // Otherwise for a past event, get attendees
        else {
          // Get the past participants
          $participants = CRM_Zoomzoom_Zoom::getPastParticipants($api, $zoom_id);
          // Verify the participants exist as contacts and added to the event
          if (!empty($participants)) {
            foreach ($participants as $participant) {
              // Zoom uses inconsistent field names for registrants and participants
              $participant_name = explode(' ', trim($participant['name']));
              $registration_details['registration_date'] = strtotime('Now');
              $registration_details['first_name'] = $participant_name[0];;
              $registration_details['last_name'] = $participant_name[1];
              $registration_details['email'] = trim(strtolower($participant['user_email']));
              $registration_details['status'] = 'Attended';
              $registration_details['event'] = $event;

              CRM_Zoomzoom_Zoom::updateCiviCRMParticipant($registration_details);
            }
          }
        }
      }
    } catch (Exception $e) {
      $errorMessage = $e->getMessage();
      CRM_Core_Error::debug_var('Zoomzoom::importattendees', $errorMessage);
      CRM_Core_Error::debug_var('Zoomzoom::importattendees', $params);
    }
    return civicrm_api3_create_success(TRUE, $params, 'Zoomzoom', 'importattendees');

  } catch (API_Exception $e) {
    $errorMessage = $e->getMessage();
    CRM_Core_Error::debug_var('Zoomzoom::importattendees', $errorMessage);
    CRM_Core_Error::debug_var('Zoomzoom::importattendees', $params);
    return civicrm_api3_create_error($errorMessage);
  }
}
