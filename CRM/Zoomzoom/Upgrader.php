<?php
use CRM_Zoomzoom_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Zoomzoom_Upgrader extends CRM_Zoomzoom_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed.
   */
  // public function install() {
  //   $this->executeSqlFile('sql/myinstall.sql');
  // }

  /**
   * Example: Work with entities usually not available during the install step.
   *
   * This method can be used for any post-install tasks. For example, if a step
   * of your installation depends on accessing an entity that is itself
   * created during the installation (e.g., a setting or a managed entity), do
   * so here to avoid order of operation problems.
   */
  public function postInstall() {
    CRM_Civirules_Utils_Upgrader::insertActionsFromJson(E::path('civirules.json'));
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled.
   */
  // public function uninstall() {
  //  $this->executeSqlFile('sql/myuninstall.sql');
  // }

  public function enable() {
    CRM_Civirules_Utils_Upgrader::insertActionsFromJson(E::path('civirules.json'));
  }

  /**
   * Example: Run a simple query when a module is disabled.
   */
  // public function disable() {
  //   CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
  // }

  public function upgrade_10800() {
    $this->ctx->log->info('Installing CiviRules Actions');

    CRM_Civirules_Utils_Upgrader::insertActionsFromJson(E::path('civirules.json'));

    return TRUE;
  }

}
