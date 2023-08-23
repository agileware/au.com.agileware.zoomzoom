<?php

use CRM_Zoomzoom_ExtensionUtil as E;

return [
  'zoom_account_id' => [
    'name' => 'zoom_account_id',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'title' => E::ts('Zoom Account ID'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 8]],
  ],
  'zoom_client_key' => [
    'name' => 'zoom_client_key',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'title' => E::ts('Zoom OAuth Client Key'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 9]],
  ],
  'zoom_client_secret' => [
    'name' => 'zoom_client_secret',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'title' => E::ts('Zoom OAuth Client Secret'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => E::ts('Zoom OAuth API credentials to use for this CiviCRM integration. Create a Sever-to-Server OAuth app in the <a href="https://marketplace.zoom.us/develop/create" target="_blank">Zoom Marketplace</a>. For more details read, <a href="https://developers.zoom.us/docs/internal-apps/create/" target="_blank">Create a Server-to-Server OAuth app</a>.'),
  ],
  'zoom_import_meeting' => [
    'name' => 'zoom_import_meeting',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::eventType'
    ],
    'title' => E::ts('Zoom Meeting, Event Type'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => E::ts('Event Type to assign to Events imported a Zoom Meeting'),
  ],
  'zoom_import_webinar' => [
    'name' => 'zoom_import_webinar',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::eventType'
    ],
    'title' => E::ts('Zoom Webinar, Event Type'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => E::ts('Event Type to assign to Events imported a Zoom Webinar'),
  ],
  'zoom_import_status_registration' => [
    'name' => 'zoom_import_status_registration',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => E::ts('Zoom Registration, Status'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => E::ts('Participant Status to be assigned to imported Zoom registrations'),
  ],
  'zoom_import_status_participant' => [
    'name' => 'zoom_import_status_participant',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => E::ts('Zoom Participant, Status'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => E::ts('Participant Status to be assigned to imported Zoom participants'),
  ],
  'zoom_import_status_absentee' => [
    'name' => 'zoom_import_status_absentee',
    'group' => 'zoomzoom',
    'group_name' => 'Zoom Settings',
    'type' => 'String',
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => E::ts('Zoom Absentee, Status'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
    'description' => E::ts('Participant Status to be assigned to imported Zoom absentees'),
  ],
];
