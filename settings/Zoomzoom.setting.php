<?php

return [
  'zoom_api_key' => [
    'name' => 'zoom_api_key',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'title' => ts('Zoom JWT API Key'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
  ],
  'zoom_api_secret' => [
    'name' => 'zoom_api_secret',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'title' => ts('Zoom JWT API Secret'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => ts('Zoom JWT API credentials to use for this CiviCRM integration. Create a JWT App in the <a href="https://marketplace.zoom.us/develop/create" target="blank">Zoom Marketplace</a>. For more details read, <a href="https://marketplace.zoom.us/docs/guides/auth/jwt" target="blank">Zoom API, JSON Web Tokens (JWT)</a>.'),
  ],
  'zoom_import_meeting' => [
    'name' => 'zoom_import_meeting',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'Select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::eventType'
    ],
    'title' => ts('Zoom Meeting, Event Type'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => ts('Event Type to assign to Events imported a Zoom Meeting'),
  ],
  'zoom_import_webinar' => [
    'name' => 'zoom_import_webinar',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'Select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::eventType'
    ],
    'title' => ts('Zoom Webinar, Event Type'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => ts('Event Type to assign to Events imported a Zoom Webinar'),
  ],
  'zoom_import_status_registration' => [
    'name' => 'zoom_import_status_registration',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'Select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => ts('Zoom Registration, Status'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => ts('Participant Status to be assigned to imported Zoom registrations'),
  ],
  'zoom_import_status_participant' => [
    'name' => 'zoom_import_status_participant',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'Select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => ts('Zoom Participant, Status'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => ts('Participant Status to be assigned to imported Zoom participants'),
  ],
  'zoom_import_status_absentee' => [
    'name' => 'zoom_import_status_absentee',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'Select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => ts('Zoom Absentee, Status'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => ts('Participant Status to be assigned to imported Zoom absentees'),
  ],
];