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

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function zoomzoom_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function zoomzoom_civicrm_navigationMenu(&$menu) {
//  _zoomzoom_civix_insert_navigation_menu($menu, 'Mailings', array(
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ));
//  _zoomzoom_civix_navigationMenu($menu);
//}

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

/*
function zoomzoom_civicrm_tokens(&$tokens) {
  $tokens['zoom'][] = ['zoom.zoom_id' => ts('Zoom Meeting ID')];
  $tokens['zoom'][] = ['zoom.password' => ts('Zoom Password')];
  $tokens['zoom'][] = ['zoom.start_url' => ts('Zoom Start URL')];
  $tokens['zoom'][] = ['zoom.join_url' => ts('Zoom Join URL')];
  $tokens['zoom'][] = ['zoom.registration_url' => ts('Zoom Registration URL')];
  $tokens['zoom'][] = ['zoom.global_dial_in_numbers' => ts('Zoom Dial-in Numbers')];

  $tokens['zoom_registrant'][] = ['zoom_registrant.registrant_id' => ts('Zoom Registrant ID')];
  $tokens['zoom_registrant'][] = ['zoom_registrant.join_url' => ts('Zoom Registrant Join URL')];
}

function zoomzoom_civicrm_tokenvalues(&$values, $cids, $job = NULL, $tokens = [], $context = NULL) {
  if (!empty($tokens['zoom'])) {
    foreach ($values as $id => $value) {
      $values[$id]['zoom.zoom_id'] = 'Banana';
      $values[$id]['zoom.password'] = 'Banana';
      $values[$id]['zoom.start_url'] = 'Banana';
      $values[$id]['zoom.join_url'] = 'Banana';
      $values[$id]['zoom.registration_url'] = 'Banana';
      $values[$id]['zoom.registration_url'] = 'Banana';
      $values[$id]['zoom.global_dial_in_numbers'] = 'Banana';
    }
  }
  if (!empty($tokens['zoom_registrant'])) {
    foreach ($values as $id => $value) {
      $values[$id]['zoom_registrant.registrant_id'] = 'Banana';
      $values[$id]['zoom_registrant.join_url'] = 'Banana';
    }
  }
} */

/**
 * Implements hook_civicrm_tokens().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tokens
 */
function zoomzoom_XXXcivicrm_tokens(&$tokens) {
  $customTokens = getAllEventCustomFieldTokens();
  foreach ($customTokens as $customToken) {
    $tokens['event'][$customToken['key']] = $customToken['label'];
  }
}

/**
 * Find membership custom fields token.
 *
 * @return array
 * @throws CiviCRM_API3_Exception
 */
function getAllEventCustomFieldTokens() {
  $tokens = array();

  $membershipCustomFieldsGroups = civicrm_api3('CustomGroup', 'get', [
    'sequential' => 1,
    'return' => ['id', 'title'],
    'extends' => "Event",
  ]);

  $membershipCustomFieldsGroups = $membershipCustomFieldsGroups['values'];

  foreach ($membershipCustomFieldsGroups as $membershipCustomFieldsGroup) {
    $customFields = civicrm_api3('CustomField', 'get', [
      'sequential'      => "1",
      'custom_group_id' => $membershipCustomFieldsGroup['id'],
    ]);
    $customFields = $customFields['values'];

    foreach ($customFields as $customField) {
      $tokens[] = array(
        'custom_field_id' => 'custom_' . $customField['id'],
        'key'             => 'event.custom_' . $customField['id'],
        'label'           => $customField['label'] . ' :: ' . $membershipCustomFieldsGroup['title'],
      );
    }
  }

  return $tokens;
}

/**
 * Implements hook_civicrm_tokenValues().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tokenValues
 */
function zoomzoom_civicrm_XXXtokenValues(&$values, $cids, $job = NULL, $tokens = array(), $context = NULL) {
// Unfortunately, cannot target CRM_Activity_BAO_Activity context as we don't know what Participant or Event this context relates too

  if ($context == 'CRM_Core_BAO_ActionSchedule') {
    $customTokens = getAllEventCustomFieldTokens();
    if (is_array($cids)) {
      foreach ($cids as $cid) {
        foreach ($customTokens as $customToken) {
          // $values[$cid][$customToken['key']] = "[" . $customToken['key'] . "]";
          $values[$cid][$customToken['key']] = 'Banana';
        }
      }
    }
    else {
      foreach ($customTokens as $customToken) {
        /// $values[$customToken['key']] = "[" . $customToken['key'] . "]";
        $values[$customToken['key']] = 'Banana';
      }
    }
  }
}

/**
 * Implements hook_civicrm_alterMailParams().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterMailParams
 */
function zoomzoom_civicrm_XXXalterMailParams(&$params, $context) {
  if (isset($params['token_params'])) {
    $tokenParams = $params['token_params'];
    // Replace membership custom field values
    if ($tokenParams['entityTable'] == 'civicrm_event') {
      $membershipId = $tokenParams['id'];
      $customTokens = getAllEventCustomFieldTokens();
      $customFieldIds = array_column($customTokens, 'custom_field_id');

      $customFieldValues = civicrm_api3('Event', 'get', [
        'sequential' => 1,
        'return'     => $customFieldIds,
        'id'         => $membershipId,
      ]);
      $customFieldValues = $customFieldValues['values'];
      if (count($customFieldValues) > 0) {
        $customFieldValues = $customFieldValues[0];
      }
      $tokenValues = array();

      foreach ($customTokens as $customToken) {
        $key = $customToken['key'];
        $fieldId = $customToken['custom_field_id'];

        if (isset($customFieldValues[$fieldId])) {
          $tokenValues['[' . $key . ']'] = $customFieldValues[$fieldId];
        }
      }

      foreach ($tokenValues as $tokenKey => $tokenValue) {
        $params['html'] = str_replace($tokenKey, $tokenValue, $params['html']);
        $params['subject'] = str_replace($tokenKey, $tokenValue, $params['subject']);
        $params['text'] = str_replace($tokenKey, $tokenValue, $params['text']);
      }
    }
  }
}
