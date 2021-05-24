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
    'title' => ts('Default Event Type for imported Zoom Meeting'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
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
    'title' => ts('Default Event Type for imported Zoom Webinar'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['zoomzoom' => ['weight' => 10]],
  ],
];