<?php

require_once 'zoomzoom.civix.php';
// phpcs:disable
use CRM_Zoomzoom_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function zoomzoom_civicrm_config(&$config) {
  _zoomzoom_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function zoomzoom_civicrm_xmlMenu(&$files) {
  _zoomzoom_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function zoomzoom_civicrm_install() {
  _zoomzoom_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function zoomzoom_civicrm_postInstall() {
  _zoomzoom_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function zoomzoom_civicrm_uninstall() {
  _zoomzoom_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function zoomzoom_civicrm_enable() {
  _zoomzoom_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function zoomzoom_civicrm_disable() {
  _zoomzoom_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function zoomzoom_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  // @TODO This is executing everytime, refactor to be an upgrade
  CRM_Civirules_Utils_Upgrader::insertActionsFromJson(__DIR__ . '/civirules.json');

  return _zoomzoom_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function zoomzoom_civicrm_managed(&$entities) {
  $entities[] = [
    'module' => 'au.com.agileware.zoomzoom',
    'name' => 'ZoomZoom_ImportZooms',
    'entity' => 'Job',
    'update' => 'clean',
    'params' => [
      'version' => 3,
      'run_frequency' => 'Daily',
      'name' => 'Import Zoom Webinars and Meetings',
      'description' => 'Import Zoom Webinars and Zoom Meetings',
      'api_entity' => 'Zoomzoom',
      'api_action' => 'importzooms',
      'parameters' => "day_offset=30",
      'is_active' => '0',
    ],
  ];

  $entities[] = [
    'module' => 'au.com.agileware.zoomzoom',
    'name' => 'ZoomZoom_ImportAttendees',
    'entity' => 'Job',
    'update' => 'clean',
    'params' => [
      'version' => 3,
      'run_frequency' => 'Daily',
      'name' => 'Import Zoom Registrations and Attendees',
      'description' => 'Import Zoom registrations and attendees for those CiviCRM Events with a Zoom ID',
      'api_entity' => 'Zoomzoom',
      'api_action' => 'importattendees',
      'parameters' => "day_offset=30",
      'is_active' => '0',
    ],
  ];

  _zoomzoom_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function zoomzoom_civicrm_caseTypes(&$caseTypes) {
  _zoomzoom_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function zoomzoom_civicrm_angularModules(&$angularModules) {
  _zoomzoom_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function zoomzoom_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _zoomzoom_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function zoomzoom_civicrm_entityTypes(&$entityTypes) {
  _zoomzoom_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_themes().
 */
function zoomzoom_civicrm_themes(&$themes) {
  _zoomzoom_civix_civicrm_themes($themes);
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */

function zoomzoom_civicrm_navigationMenu(&$menu) {
  _zoomzoom_civix_insert_navigation_menu($menu, 'Administer', [
    'label' => E::ts('Zoom Settings'),
    'name' => 'zoomzoom_settings',
    'url' => 'civicrm/admin/setting/zoomzoom',
    'permission' => 'access CiviEvent',
    'operator' => 'OR',
    'separator' => 0,
  ]);
  _zoomzoom_civix_navigationMenu($menu);
}
