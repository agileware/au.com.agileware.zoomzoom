<?php

//https://marketplace.zoom.us/docs/api-reference/zoom-api/

class CRM_Zoomzoom_Zoom {

  /**
   * Initialise a Zoom object for API calls
   *
   * @return ZoomAPIWrapper|null
   */
  static function getZoomObject() {
    static $zoom;

    if (!is_null($zoom)) {
      return $zoom;
    }

    $apiKey = Civi::settings()->get('zoom_api_key');
    $apiSecret = Civi::settings()->get('zoom_api_secret');

    if (empty($apiKey) || empty($apiSecret)) {
      return NULL;
    }

    $extPath = Civi::resources()
      ->getPath(CRM_Zoomzoom_ExtensionUtil::LONG_NAME);
    require_once $extPath . '/packages/ZoomAPIWrapper/ZoomAPIWrapper.php';

    $zoom = new ZoomAPIWrapper($apiKey, $apiSecret);

    return $zoom;
  }

  /**
   * Get the Zoom account owner for the current Zoom JWT
   *
   * @return mixed
   */
  static function getOwner() {
    static $user;

    if (!is_null($user)) {
      return $user;
    }

    $zoom = self::getZoomObject();
    $users = $zoom->doRequest('GET', '/users', [
      'status' => 'active',
      'role_id' => 0,
    ]);

    // Return the first Zoom user with the Owner role
    if (!empty($users['users'])) {
      $user = $users['users'][0];
      return $user;
    }
    else {
      return NULL;
    }
  }


  /**
   * Gets all the Zooms for date range using a day offset from today
   * Zoom API documentation:
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinars
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetings
   *
   * @param string $api use values: meeting, webinar
   * @param int $day_offset
   *
   * @return array|bool
   */
  static function getZooms($api, $day_offset = 0) {
    $date_offset = strtotime($day_offset . ' days');
    $user = self::getOwner();
    $zoom_api = self::getZoomObject();
    $zooms = [];

    $params = [
      'page_size' => 300,
    ];

    if (!empty($user)) {
      do {
        $zooms_list = $zoom_api->doRequest('GET', '/users/{userId}/' . $api, $params, ['userId' => $user['id']]);

        // Set the next results page token is available otherwise, NULL to exit
        $params['next_page_token'] = (empty($zooms_list['next_page_token'])) ? NULL : $zooms_list['next_page_token'];

        if (!empty($zooms_list[$api])) {
          //get full details about each webinar so we determine if registration is enabled
          foreach ($zooms_list[$api] as $key => $zoom_instance) {
            // Prepend the type of Zoom to the ID
            $zooms_list[$api][$key]['civicrm_zoom_id'] = substr($api, 0, 1) . $zoom_instance['id'];

            // If Zoom start time prior to the date offset then remove it
            if (strtotime($zoom_instance['start_time']) < $date_offset) {
              unset($zooms_list[$api][$key]);
            }
          }

          $zooms += $zooms_list[$api];
        }
      } while (!empty($params['next_page_token']));
    }
    return $zooms;
  }

  /**
   * Create Zoom using supplied parameters
   * Zoom API documentation:
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinarcreate
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingcreate
   *
   * @param string $params see Zoom documentation for parameters
   *
   * @return false|mixed
   * @api string $api use either webinar or meeting
   */
  static function createZoom($api, $params) {
    $user = self::getOwner();
    $zoom_api = self::getZoomObject();
    $json = json_encode($params, JSON_NUMERIC_CHECK);

    if (!empty($user)) {
      $response = $zoom_api->doRequest('POST', '/users/{userId}/' . $api, [],
        ['userId' => $user['id']], $json);

      if (!empty($response['id'])) {
        $zoom_details['id'] = $response['id'];
        $zoom_details['start_url'] = $response['start_url'];
        $zoom_details['join_url'] = $response['join_url'];
        $zoom_details['registration_url'] = $response['registration_url'];

        $zoom_details['global_dial_in_numbers'] = '';
        if (!empty($response['settings'])) {
          if (!empty($response['settings']['global_dial_in_numbers'])) {
            foreach ($response['settings']['global_dial_in_numbers'] as $dial_in_number) {
              $zoom_details['global_dial_in_numbers'] .= $dial_in_number['number'] . ' (' . $dial_in_number['country_name'] . ')<br/>';
            }
          }
        }
        $zoom_details['password'] = $response['password'];

        return $zoom_details;
      }
      else {
        // Zoom could not be created for some reason
        CRM_Core_Error::debug_log_message('Unable to create Zoom ' . $api);
        CRM_Core_Error::debug_var('Zoom API Params', $json);
        CRM_Core_Error::debug_var('Zoom API Response Code', $zoom_api->responseCode());
      }
    }

    return FALSE;
  }

  /**
   * Get absentees for a Zoom Webinar
   * Zoom API documentation:
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinarabsentees
   * No Zoom API implementation for Meetings
   *
   * @param string $api options meeting, webinar
   * @param string $zoom_id Zoom ID
   *
   * @return array|mixed
   */
  static function getAbsentees($zoom_id) {
    $user = self::getOwner();
    $zoom_api = self::getZoomObject();
    $absentees = [];

    $params = [
      'page_size' => 300,
    ];

    if (!empty($user)) {
      do {

        /*
        * Important, Zoom Webinars that have ended have a different UUID - because reasons.
        * See API https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/pastwebinars
        * Developer discussion, https://devforum.zoom.us/t/unable-to-retrieve-a-webinar-s-absentees-past-webinars-webinaruuid-absentees/14381/19
         */
        $zoom_webinar = $zoom_api->doRequest('GET', '/past_webinars/' . $zoom_id . '/instances', $params, ['userId' => $user['id']]);

        // Get the UUID for the specific Zoom webinar
        if (!empty($zoom_webinar['webinars'][0]['uuid'])) {
          $zoom_webinaruuid = urlencode($zoom_webinar['webinars'][0]['uuid']);

          $zoom_absentees = $zoom_api->doRequest('GET', '/past_webinars/' . $zoom_webinaruuid . '/absentees', $params, ['userId' => $user['id']]);

          // Set the next results page token is available otherwise, NULL to exit
          $params['next_page_token'] = (empty($zoom_absentees['next_page_token'])) ? NULL : $zoom_absentees['next_page_token'];

          // If absentees exist for this Zoom
          if (!empty($zoom_absentees['registrants'])) {
            foreach ($zoom_absentees['registrants'] as $key => $zoom_absentee) {
              // If the absentees has a first_name and last_name then add them to the list
              // Weirdly, Zoom has been providing absentees which are just email addresses - no idea what they are
              if (!empty($zoom_absentee['first_name']) && !empty($zoom_absentee['last_name'])) {
                $absentees[] = $zoom_absentee;
              }
            }
          }
        }
      } while (!empty($params['next_page_token']));
    }
    return $absentees;
  }

  /**
   * Get past participants for a Zoom meeting
   * Zoom API documentation:
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/pastmeetingparticipants
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/listwebinarparticipants
   *
   * @param string $api options meeting, webinar
   * @param string $zoom_id Zoom ID
   *
   * @return array|mixed
   */
  static function getPastParticipants($api, $zoom_id) {
    $user = self::getOwner();
    $zoom_api = self::getZoomObject();
    $participants = [];

    $params = [
      'page_size' => 300,
    ];

    if (!empty($user)) {
      do {
        $zoom_participants = $zoom_api->doRequest('GET', '/past_' . $api . '/' . $zoom_id . '/participants', $params, ['userId' => $user['id']]);

        // Set the next results page token is available otherwise, NULL to exit
        $params['next_page_token'] = (empty($zoom_participants['next_page_token'])) ? NULL : $zoom_participants['next_page_token'];

        // If participants exist for this Zoom
        if (!empty($zoom_participants['participants'])) {
          foreach ($zoom_participants['participants'] as $key => $zoom_participant) {
            $participants[] = $zoom_participant;
          }
        }
      } while (!empty($params['next_page_token']));
    }
    return $participants;
  }

  /**
   * Get Zoom Registrants
   * Zoom API documentation:
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrants
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinarregistrants
   *
   * @param string $api options meeting, webinar
   * @param string $zoom_id Zoom ID
   *
   * @return array|mixed
   */
  static function getRegistrants($api, $zoom_id) {
    $user = self::getOwner();
    $zoom_api = self::getZoomObject();
    $registrants = [];

    $params = [
      'page_size' => 300,
    ];

    if (!empty($user)) {

      do {
        $zoom_participants = $zoom_api->doRequest('GET', '/' . $api . '/' . $zoom_id . '/registrants', $params, ['userId' => $user['id']]);

        // Set the next results page token is available otherwise, NULL to exit
        $params['next_page_token'] = (empty($zoom_participants['next_page_token'])) ? NULL : $zoom_participants['next_page_token'];

        // If registrations are enabled for the Zoom then get the registrants
        if (!empty($zoom_participants['registrants'])) {
          foreach ($zoom_participants['registrants'] as $key => $zoom_participant) {
            $registrants[] = $zoom_participant;
          }
        }
      } while (!empty($params['next_page_token']));
    }
    return $registrants;
  }

  /**
   * Updates a CiviCRM Participant based on the registration details from Zoom.
   * Participant is created if does not exist.
   *
   * @param array $registration_details options meeting, webinar
   *
   * @return array|mixed
   */
  static function updateCiviCRMParticipant($registration_details) {

    try {
      $email = trim(strtolower($registration_details['email']));

      // @TODO this should really use an unsupervised dedupe rule
      $emails = \Civi\Api4\Email::get()
        ->selectRowCount()
        ->addSelect('contact_id')
        ->addWhere('email', '=', $email)
        ->setLimit(1)
        ->execute();

      // Contact does not exist, create it now
      if ($emails->rowCount == 0) {
        // Create the contact now
        $new_contact = \Civi\Api4\Contact::create()
          ->addValue('contact_type', 'Individual')
          ->addValue('first_name', $registration_details['first_name'])
          ->addValue('last_name', $registration_details['last_name'])
          ->addChain('add_email', \Civi\Api4\Email::create()
            ->addValue('contact_id', '$id')
            ->addValue('email', $email)
            ->addValue('is_primary', TRUE)
          )
          ->execute();
        $contact_id = $new_contact[0]['id'];
      }
      else {
        // Contact exists, set the Contact ID
        $contact_id = $emails[0]['contact_id'];
      }

      // Check if this contact is already registered on this event
      $existing_participant = \Civi\Api4\Participant::get()
        ->selectRowCount()
        ->addSelect('id')
        ->addWhere('contact_id', '=', $contact_id)
        ->addWhere('event_id', '=', $registration_details['event']['id'])
        ->setLimit(1)
        ->execute();

      // Contact already is registered for this event, update their status to Attended and record Zoom registration details
      if ($existing_participant->rowCount == 1) {
        $results = \Civi\Api4\Participant::update()
          ->addWhere('id', '=', $existing_participant[0]['id'])
          ->addValue('status_id', $registration_details['status_id'])
          ->addValue('zoom_registrant.registrant_id', $registration_details['zoom_id'])
          ->addValue('zoom_registrant.join_url', $registration_details['zoom_join_url'])
          ->execute();

        return TRUE;
      }
      else {
        // Contact is not registered for this event, register now and set status
        // Set the Registration Date to the Event Start Date otherwise will look weird having a Registration Date AFTER Event Start Date
        $register_date = (strtotime($registration_details['event']['start_date']) < $registration_details['registration_date'] ? $registration_details['event']['start_date'] : date('Y-m-d H:i:s', $registration_details['registration_date']));

        $results = \Civi\Api4\Participant::create()
          ->addValue('contact_id', $contact_id)
          ->addValue('event_id', $registration_details['event']['id'])
          ->addValue('status_id', $registration_details['status_id'])
          ->addValue('register_date', $register_date)
          ->addValue('zoom_registrant.registrant_id', $registration_details['zoom_id'])
          ->addValue('zoom_registrant.join_url', $registration_details['zoom_join_url'])
          ->execute();

        return TRUE;
      }
    } catch (API_Exception $e) {
      $errorMessage = $e->getMessage();
      CRM_Core_Error::debug_var('Zoomzoom::updateCiviCRMParticipant', $errorMessage);
      CRM_Core_Error::debug_var('Zoomzoom::updateCiviCRMParticipant', $registration_details);
      return FALSE;
    }
  }

  /**
   * Get the Zoom Meeting ID custom field from an Event
   *
   * @param $eventId
   *
   * @return array|null
   */
  static function getEventZoomMeetingId($eventId) {
    try {
      $zoom_id_field_id = CRM_Core_BAO_CustomField::getCustomFieldID('zoom_id', 'zoom', TRUE);
      $result = civicrm_api3('Event', 'getvalue', [
        'return' => $zoom_id_field_id,
        'id' => $eventId,
      ]);
      return $result ?? NULL;

    } catch (CiviCRM_API3_Exception $e) {
      $errorMessage = $e->getMessage();
      CRM_Core_Error::debug_var('Zoomzoom::getEventZoomMeetingId', $errorMessage);
      CRM_Core_Error::debug_var('Zoomzoom::getEventZoomMeetingId', $eventId);
      return NULL;
    }
  }

  /**
   * Helper function to return the Zoom Web API based on the CiviCRM Zoom ID
   * eg. m1234567 will return meetings; w1234567 will return webinars
   *
   * @param string $civicrm_zoom_id Zoom ID in format of m1234567 or
   *   w1234567
   *
   * @return array single, plural
   */
  static function getZoomAPIFromCiviCRMZoomId($civicrm_zoom_id) {
    $api = 'meetings';
    if (substr(strtolower($civicrm_zoom_id), 0, 1) == 'w') {
      $api = 'webinars';
    }
    return $api;
  }

  /**
   * Helper function to return the Zoom ID based on the CiviCRM Zoom ID
   * eg. m1234567 will return 1234567
   *
   * @param string $civicrm_zoom_id Zoom ID in format of m1234567 or
   *   w1234567
   *
   * @return string
   */
  static function getZoomIDFromCiviCRMZoomId($civicrm_zoom_id) {
    return substr($civicrm_zoom_id, 1);
  }

  /**
   * Creates a registration for a Zoom
   * Zoom API documentation:
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinarregistrantcreate
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrantcreate
   *
   * @param string $civicrm_zoom_id Zoom ID in format of m1234567 or
   *   w1234567
   * @param string $participant_id the CiviCRM Participant ID
   * @param $params array values for the Zoom registration
   *
   * @return false|mixed
   */
  static function createZoomRegistration($civicrm_zoom_id, $participant_id, $params) {
    $zoom_api = self::getZoomObject();
    $json = json_encode($params, JSON_NUMERIC_CHECK);

    $api = CRM_Zoomzoom_Zoom::getZoomAPIFromCiviCRMZoomId($civicrm_zoom_id);
    $zoom_id = CRM_Zoomzoom_Zoom::getZoomIDFromCiviCRMZoomId($civicrm_zoom_id);

    $response = $zoom_api->doRequest('POST', '/' . $api . '/{zoomId}/registrants', [],
      ['zoomId' => $zoom_id], $json);

    // If Zoom accepted the registration, as indicated by no error code in the response
    if (!empty($response['registrant_id'])) {
      try {
        // Record the Zoom details for the registration
        // SQL query required to prevent CiviRules recursion due to Participant changed trigger
        CRM_Core_DAO::executeQuery('UPDATE civicrm_value_zoom_registrant SET `registrant_id` = %1, `join_url` = %2 WHERE civicrm_value_zoom_registrant.entity_id = %3', [
          '1' => [$response['registrant_id'], 'String'],
          '2' => [$response['join_url'], 'String'],
          '3' => [$participant_id, 'Integer'],
        ]);
      } catch (API_Exception $e) {
        $errorMessage = $e->getMessage();
        CRM_Core_Error::debug_var('Zoomzoom::createZoomRegistration', $errorMessage);
        CRM_Core_Error::debug_var('Zoomzoom::createZoomRegistration', $response);
        CRM_Core_Error::debug_var('Zoomzoom::createZoomRegistration', $zoom_api->responseCode());
        return FALSE;
      }
    }

    return $zoom_api->responseCode();
  }

  /**
   * Deletes an existing Zoom registration
   * Zoom API documentation:
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/deletewebinarregistrant
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrantdelete
   *
   * @param string $civicrm_zoom_id Zoom ID in format of m1234567 or
   *   w1234567
   * @param string $registrant_id the Zoom Registrant ID
   *
   * @return false|mixed
   */
  static function deleteZoomRegistration($civicrm_zoom_id, $registrant_id) {
    $zoom_api = self::getZoomObject();

    $api = CRM_Zoomzoom_Zoom::getZoomAPIFromCiviCRMZoomId($civicrm_zoom_id);
    $zoom_id = CRM_Zoomzoom_Zoom::getZoomIDFromCiviCRMZoomId($civicrm_zoom_id);

    $zoom_api->doRequest('DELETE', '/' . $api . '/{zoomId}/registrants/{registrantId}', [],
      ['zoomId' => $zoom_id, 'registrantId' => $registrant_id]);

    return $zoom_api->responseCode();
  }

  /**
   * Updates an existing Zoom date and details
   * Zoom API documentation:
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingupdate
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinarupdate
   *
   * @param $civicrm_zoom_id Zoom ID in format of m1234567 or w1234567
   * @param $params array values for the Zoom
   *
   * @return true|false
   */
  static function updateZoom($civicrm_zoom_id, $params) {
    $zoom_api = self::getZoomObject();
    $json = json_encode($params,JSON_NUMERIC_CHECK);

    $api = CRM_Zoomzoom_Zoom::getZoomAPIFromCiviCRMZoomId($civicrm_zoom_id);
    $zoom_id = CRM_Zoomzoom_Zoom::getZoomIDFromCiviCRMZoomId($civicrm_zoom_id);

    $zoom_api->doRequest('PATCH', '/' . $api . '/{zoomId}', [],
      ['zoomId' => $zoom_id], $json);

    if ($zoom_api->responseCode() != '204') {
      // Zoom could not be updated for some reason
      CRM_Core_Error::debug_log_message('Unable to update Zoom ' . $api);
      CRM_Core_Error::debug_var('Zoom API Params', $json);
      CRM_Core_Error::debug_var('Zoom API Response Code', $zoom_api->responseCode());
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Deletes a Zoom
   * Zoom API documentation:
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingdelete
   * https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinardelete
   *
   * @param $civicrm_zoom_id Zoom ID in format of m1234567 or w1234567
   *
   * @return true|false
   */
  static function deleteZoom($civicrm_zoom_id) {
    $zoom_api = self::getZoomObject();

    $api = CRM_Zoomzoom_Zoom::getZoomAPIFromCiviCRMZoomId($civicrm_zoom_id);
    $zoom_id = CRM_Zoomzoom_Zoom::getZoomIDFromCiviCRMZoomId($civicrm_zoom_id);

    $params = [
      'cancel_webinar_reminder' => 'false',
    ];
    $json = json_encode($params);

    $zoom_api->doRequest('DELETE', '/' . $api . '/{zoomId}', [],
      ['zoomId' => $zoom_id], $json);

    if ($zoom_api->responseCode() != '200' && $zoom_api->responseCode() != '204') {
      // Zoom could not be deleted for some reason
      CRM_Core_Error::debug_log_message('Unable to delete Zoom ' . $api);
      CRM_Core_Error::debug_var('Zoom API Params', $json);
      CRM_Core_Error::debug_var('Zoom API Response Code', $zoom_api->responseCode());
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

}

