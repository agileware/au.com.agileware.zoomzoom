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
  static $civicrm_timezones;
  try {
    $meetings = CRM_Zoomzoom_Zoom::getZooms('meetings', $params['day_offset']);
    $webinars = CRM_Zoomzoom_Zoom::getZooms('webinars', $params['day_offset']);
    $zooms = array_merge($meetings, $webinars);

    foreach ($zooms as $zoom) {
      try {
        // Get the Event Type settings for imported Zooms
        $event_type = Civi::settings()->get('zoom_import_meeting');
        if (substr($zoom['civicrm_zoom_id'], 0, 1) == 'w') {
          $event_type = Civi::settings()->get('zoom_import_webinar');
        }

        // Check if this Event already exists
        $events = \Civi\Api4\Event::get()
          ->addSelect('id')
          ->addWhere('zoom.zoom_id', '=', $zoom['civicrm_zoom_id'])
          ->execute();

        // Event does not exist, then set up the new Event
        if ($events->rowCount == 0) {
          $new_event = \Civi\Api4\Event::create()
            ->addValue('title', $zoom['topic'])
            ->addValue('zoom.zoom_id', $zoom['civicrm_zoom_id'])
            ->addValue('summary', $zoom['topic'])
            ->addValue('event_type_id', $event_type)
            ->addValue('is_public', FALSE);

          // Set other Zoom fields, if available
          if (!empty($zoom['start_url'])) {
            $new_event->addValue('zoom.start_url', $zoom['start_url']);
          }
          if (!empty($zoom['join_url'])) {
            $new_event->addValue('zoom.join_url', $zoom['join_url']);
          }
          if (!empty($zoom['registration_url'])) {
            $new_event->addValue('zoom.registration_url', $zoom['registration_url']);
          }
          if (!empty($zoom['password'])) {
            $new_event->addValue('zoom.password', $zoom['password']);
          }

          // Populate the Zoom dial-in numbers, if available
          if (!empty($zoom['global_dial_in_numbers'])) {
            $zoom_dids = '';
            foreach ($zoom['global_dial_in_numbers'] as $dial_in_number) {
              $zoom_dids .= $dial_in_number['number'] . ' (' . $dial_in_number['country_name'] . ')<br/>';
            }
          }

          // If this CiviCRM site support Event timezones
          if (method_exists('CRM_Utils_Date', 'convertTimeZone')) {
            // Return all CiviCRM timezones
            if (empty($civicrm_timezones)) {
              $civicrm_timezones = civicrm_api3('Event', 'getoptions', [
                'field' => 'event_tz',
              ]);
            }
            // Use CiviCRM site timezone by default
            $zoom_timezone = CRM_Core_Config::singleton()->userSystem->getTimeZoneString();

            // Check if the Zoom defined timezone is supported on this CiviCRM site
            if (array_key_exists($zoom['timezone'], $civicrm_timezones['values'])) {
              $zoom_timezone = $zoom['timezone'];
            }
            // Set the Event timezone
            $new_event->addValue('event_tz', $zoom_timezone);

            // Set the Event start date in the correct timezone
            $start_date = CRM_Utils_Date::convertTimeZone($zoom['start_time'], NULL, $zoom_timezone);
            $new_event->addValue('start_date', $start_date);
          }
          else {
            // Otherwise CiviCRM site has no timezone support, just record the time and people will have to figure it out
            $new_event->addValue('start_date', date('Y-m-d H:i:s', strtotime($zoom['start_time'])));
          }
          $new_event->execute();
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
 * Import Zoom registrations, attendees and absentees for those CiviCRM Events with a Zoom
 * ID. Checks for CiviCRM Events scheduled after today, offset by a number of
 * days.
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
        ->addWhere('start_date', '>=', date('Y-m-d', strtotime($params['day_offset'] . ' days')))
        ->execute();

      foreach ($events as $event) {
        $api = CRM_Zoomzoom_Zoom::getZoomAPIFromCiviCRMZoomId($event['zoom.zoom_id']);
        $zoom_id = CRM_Zoomzoom_Zoom::getZoomIDFromCiviCRMZoomId($event['zoom.zoom_id']);

        // If the event is scheduled for a future date then get registrants
        if (strtotime($event['start_date']) >= strtotime('Now')) {

          // Get registrants future events
          $registrants = CRM_Zoomzoom_Zoom::getRegistrants($api, $zoom_id);
          if (!empty($registrants)) {
            foreach ($registrants as $registrant) {
              // Zoom uses inconsistent field names for registrants and participants
              $registrant_details['registration_date'] = strtotime($registrant['create_time']);
              $registrant_details['first_name'] = $registrant['first_name'];
              $registrant_details['last_name'] = $registrant['last_name'];
              $registrant_details['email'] = $registrant['email'];
              $registrant_details['zoom_id'] = $registrant['id'];
              $registrant_details['zoom_join_url'] = $registrant['join_url'];
              $registrant_details['event'] = $event;
              // @TODO This may actually be 'Pending approval' see CIVIZOOM-8
              $registrant_details['status_id'] = Civi::settings()
                ->get('zoom_import_status_registration');

              CRM_Zoomzoom_Zoom::updateCiviCRMParticipant($registrant_details);
            }
          }
        }
        // Otherwise for a past event, get attendees
        else {
          // Get the past participants
          $participants = CRM_Zoomzoom_Zoom::getPastParticipants($api, $zoom_id);
          if (!empty($participants)) {
            foreach ($participants as $participant) {
              // Zoom uses inconsistent field names for registrants and participants
              $participant_name = explode(' ', trim($participant['name']));
              $participant_details['registration_date'] = strtotime('Now');
              $participant_details['first_name'] = $participant_name[0];;
              $participant_details['last_name'] = $participant_name[1];
              $participant_details['email'] = $participant['user_email'];
              $participant_details['event'] = $event;
              $participant_details['status_id'] = Civi::settings()
                ->get('zoom_import_status_participant');

              CRM_Zoomzoom_Zoom::updateCiviCRMParticipant($participant_details);
            }
          }
          // Get the absentees for webinars
          if ($api == 'webinars') {
            $absentees = CRM_Zoomzoom_Zoom:: getAbsentees($zoom_id);
            if (!empty($absentees)) {
              foreach ($absentees as $absentee) {
                $absentee_details['registration_date'] = strtotime($absentee['create_time']);
                $absentee_details['first_name'] = $absentee['first_name'];
                $absentee_details['last_name'] = $absentee['last_name'];
                $absentee_details['email'] = $absentee['email'];
                $absentee_details['event'] = $event;
                $absentee_details['status_id'] = Civi::settings()
                  ->get('zoom_import_status_absentee');

                CRM_Zoomzoom_Zoom::updateCiviCRMParticipant($absentee_details);
              }
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
