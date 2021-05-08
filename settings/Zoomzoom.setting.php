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
];
