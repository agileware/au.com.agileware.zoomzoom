<?php

//https://marketplace.zoom.us/docs/api-reference/zoom-api/

class CRM_Zoomzoom_Zoom {

  /**
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
      return FALSE;
    }
  }


  /**
   * @param bool $current
   * @param string $zoom_type options meeting, webinar
   *
   * @return array|mixed
   */
  static function getZooms($zoom_type, $day_offset) {
    $date_offset = strtotime('- ' . $day_offset . ' days');

    // Only permit meeting and webinar zoom types
    if ($zoom_type !== 'meeting' && $zoom_type != 'webinar') {
      return FALSE;
    }

    // Define plural for the zoom type
    $zoom_type_plural = $zoom_type . 's';

    $user = self::getOwner();
    $zoom_api = self::getZoomObject();
    $zooms = [];

    // @TODO Implement a pager for the results
    $params = [
      'page_size' => 300,
      // @TODO This does not work apparently
    ];

    if (!empty($user)) {
      $zooms_list = $zoom_api->doRequest('GET', '/users/{userId}/' . $zoom_type_plural, $params, ['userId' => $user['id']]);

      if (!empty($zooms_list[$zoom_type_plural])) {
        //get full details about each webinar so we determine if registration is enabled
        foreach ($zooms_list[$zoom_type_plural] as $key => $zoom_instance) {
          // Prepend the type of Zoom to the ID
          $zooms_list[$zoom_type_plural][$key]['civicrm_zoom_id'] = substr($zoom_type, 0, 1) . $zoom_instance['id'];

          // If Zoom start time prior to the date offset then remove it
          if (strtotime($zoom_instance['start_time']) < $date_offset) {
            unset($zooms_list[$zoom_type_plural][$key]);
          }
        }

        $zooms += $zooms_list[$zoom_type_plural];
      }
    }

    return $zooms;
  }

  /**
   *
   * @api string $api
   * @param string $params
   *
   * @return false|mixed
   */
  static function createZoom($api, $params) {
    $user = self::getOwner();
    $zoom_api = self::getZoomObject();
    $json = json_encode($params);

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
      } else {
        // Zoom could not be created for some reason
        CRM_Core_Error::debug_log_message('Unable to create Zoom ' . $api);
        CRM_Core_Error::debug_var('Zoom API Params', $json);
        CRM_Core_Error::debug_var('Zoom API Response Code', $zoom_api->responseCode());
        // CRM_Core_Error::debug_var('Zoom API Response Message', $response['message']);
      }
    }

    return FALSE;
  }

  /**
   * @param bool $current
   * @param string $zoom_type options meeting, webinar
   *
   * @return array|mixed
   */
  static function getPastParticipants($zoom_type, $zoom_id) {
    // https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/pastmeetingparticipants
    // https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/listwebinarparticipants

    // Only permit meeting and webinar zoom types
    if ($zoom_type !== 'meeting' && $zoom_type != 'webinar') {
      return FALSE;
    }

    // Define plural for the zoom type
    $zoom_type_plural = $zoom_type . 's';

    $user = self::getOwner();
    $zoom_api = self::getZoomObject();
    $participants = [];

    // @TODO Implement a pager for the results
    $params = [
      'page_size' => 300,
    ];

    if (!empty($user)) {
      $zoom_participants = $zoom_api->doRequest('GET', '/past_' . $zoom_type_plural . '/' . $zoom_id . '/participants', $params, ['userId' => $user['id']]);

      // If participants exist for this Zoom
      if (!empty($zoom_participants['participants'])) {
        foreach ($zoom_participants['participants'] as $key => $zoom_participant) {
          $participants[] = $zoom_participant;
        }
      }
    }

    return $participants;
  }

  /**
   * @param bool $current
   * @param string $zoom_type options meeting, webinar
   *
   * @return array|mixed
   */
  static function getRegistrants($zoom_type, $zoom_id) {
    // https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrants
    // https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinarregistrants

    // Only permit meeting and webinar zoom types
    if ($zoom_type !== 'meeting' && $zoom_type != 'webinar') {
      return FALSE;
    }

    // Define plural for the zoom type
    $zoom_type_plural = $zoom_type . 's';

    $user = self::getOwner();
    $zoom_api = self::getZoomObject();
    $registrants = [];

    // @TODO Implement a pager for the results
    $params = [
      'page_size' => 300,
    ];

    if (!empty($user)) {
      $zoom_participants = $zoom_api->doRequest('GET', '/' . $zoom_type_plural . '/' . $zoom_id . '/registrants', $params, ['userId' => $user['id']]);

      // If registrations are enabled for the Zoom then get the registrants
      if (!empty($zoom_participants['registrants'])) {
        foreach ($zoom_participants['registrants'] as $key => $zoom_participant) {
          $registrants[] = $zoom_participant;
        }
      }
    }

    return $registrants;
  }

  /**
   * @param bool $current
   * @param string $zoom_type options meeting, webinar
   *
   * @return array|mixed
   */
  static function updateCiviCRMParticipant($registration_details) {
    $emails = \Civi\Api4\Email::get()
      ->selectRowCount()
      ->addSelect('contact_id')
      ->addWhere('email', '=', $registration_details['email'])
      ->setLimit(1)
      ->execute();

    // Contact does not exist, create it now
    if ($emails->rowCount == 0) {
      // Create the contact now
      // @TODO try / catch
      $new_contact = \Civi\Api4\Contact::create()
        ->addValue('contact_type', 'Individual')
        ->addValue('first_name', $registration_details['first_name'])
        ->addValue('last_name', $registration_details['last_name'])
        ->addChain('add_email', \Civi\Api4\Email::create()
          ->addValue('contact_id', '$id')
          ->addValue('email', $registration_details['email'])
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
        ->addValue('status_id:name', $registration_details['status'])
        ->addValue('zoom_registrant.registrant_id', $registration_details['zoom_id'])
        ->addValue('zoom_registrant.join_url', $registration_details['zoom_join_url'])
        ->execute();
    }
    else {
      // Contact is not registered for this event, register now and set status
      // Set the Registration Date to the Event Start Date otherwise will look weird having a Registration Date AFTER Event Start Date
      $register_date = (strtotime($registration_details['event']['start_date']) < $registration_details['registration_date'] ? $registration_details['event']['start_date'] : date('Y-m-d H:i:s', $registration_details['registration_date']));

      $results = \Civi\Api4\Participant::create()
        ->addValue('contact_id', $contact_id)
        ->addValue('event_id', $registration_details['event']['id'])
        ->addValue('status_id:name', $registration_details['status'])
        ->addValue('register_date', $register_date)
        ->addValue('zoom_registrant.registrant_id', $registration_details['zoom_id'])
        ->addValue('zoom_registrant.join_url', $registration_details['zoom_join_url'])
        ->execute();
    }
  }

  /**
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
    } catch (CiviCRM_API3_Exception $e) {
    }

    return $result ?? NULL;
  }

  /**
   * @param $civicrm_zoom_id
   * @param $params
   *
   * @return false|mixed
   */
  static function createZoomRegistration($civicrm_zoom_id, $participant_id, $params) {
    $zoom_api = self::getZoomObject();
    $json = json_encode($params);

    // @TODO This should really be a helper function
    $api = 'meetings';
    if (substr($civicrm_zoom_id, 0, 1) == 'w') {
      $api = 'webinars';
    }

    $zoom_id = substr($civicrm_zoom_id, 1);

    $response = $zoom_api->doRequest('POST', "/{$api}/{zoomId}/registrants", [],
      ['zoomId' => $zoom_id], $json);

    // If Zoom accepted the registration, as indicated by no error code in the response
    if (!empty($response['registrant_id'])) {
      // Record the Zoom details for the registration
      \Civi\Api4\Participant::update()
        ->addWhere('id', '=', $participant_id)
        ->addValue('zoom_registrant.registrant_id', $response['registrant_id'])
        ->addValue('zoom_registrant.join_url', $response['join_url'])
        ->execute();
    }

    return $zoom_api->responseCode();
  }

  /**
   * @param $civicrm_zoom_id
   * @param $registrant_id
   * @param $registrant_email
   *
   * @return false|mixed
   */
  static function cancelZoomRegistration($civicrm_zoom_id, $registrant_id, $registrant_email) {
    $zoom_api = self::getZoomObject();

    // @TODO This should really be a helper function
    $api = 'meetings';
    if (substr($civicrm_zoom_id, 0, 1) == 'w') {
      $api = 'webinars';
    }

    $zoom_id = substr($civicrm_zoom_id, 1);

    $params = [
      'action' => 'cancel',
      'registrants' => [
        [
          'id' => $registrant_id,
          'email' => $registrant_email,
        ],
      ],
    ];
    $json = json_encode($params);

    $zoom_api->doRequest('PUT', "/{$api}/{zoomId}/registrants/status", [],
      ['zoomId' => $zoom_id], $json);

    return $zoom_api->responseCode();
  }

  /**
   * @param $civicrm_zoom_id
   * @param $params
   *
   * @return false|mixed
   */
  static function updateZoom($civicrm_zoom_id, $params) {
    $zoom_api = self::getZoomObject();
    $json = json_encode($params);

    // @TODO This should really be a helper function
    $api = 'meetings';
    if (substr($civicrm_zoom_id, 0, 1) == 'w') {
      $api = 'webinars';
    }

    $zoom_id = substr($civicrm_zoom_id, 1);

    $zoom_api->doRequest('PATCH', "/{$api}/{zoomId}", [],
      ['zoomId' => $zoom_id], $json);

      if ( $zoom_api->responseCode() != '204') {
        // Zoom could not be updated for some reason
        CRM_Core_Error::debug_log_message('Unable to update Zoom ' . $api);
        CRM_Core_Error::debug_var('Zoom API Params', $json);
        CRM_Core_Error::debug_var('Zoom API Response Code', $zoom_api->responseCode());
        //CRM_Core_Error::debug_var('Zoom API Response Message', $response['message']);
        return FALSE;
      } else {
        return TRUE;
      }
  }

  /**
   * @param $civicrm_zoom_id
   *
   * @return false|mixed
   */
  static function deleteZoom($civicrm_zoom_id) {
    $zoom_api = self::getZoomObject();

    // @TODO This should really be a helper function
    $api = 'meetings';
    if (substr($civicrm_zoom_id, 0, 1) == 'w') {
      $api = 'webinars';
    }

    $zoom_id = substr($civicrm_zoom_id, 1);

    $params = [
          'cancel_webinar_reminder' => 'false',
    ];
    $json = json_encode($params);

    $zoom_api->doRequest('DELETE', "/{$api}/{zoomId}", [],
      ['zoomId' => $zoom_id], $json);

    if ( $zoom_api->responseCode()!= '200' || $zoom_api->responseCode()!= '204') {
      // Zoom could not be deleted for some reason
      CRM_Core_Error::debug_log_message('Unable to delete Zoom ' . $api);
      CRM_Core_Error::debug_var('Zoom API Params', $json);
      CRM_Core_Error::debug_var('Zoom API Response Code', $zoom_api->responseCode());
      //CRM_Core_Error::debug_var('Zoom API Response Message', $zoom_api->responseCode());
      return FALSE;
    } else {
      return TRUE;
    }
  }
}


