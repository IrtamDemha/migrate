<?php

namespace Drupal\custom_migrate\Commands;

use Drupal\gdomain\GdomainHelper;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\node\Entity\Node;
use Drush\Commands\DrushCommands;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_tools\MigrateExecutable;

/**
 * A drush command file.
 *
 * @package Drupal\drush9_custom_commands\Commands
 */
class EventsGroupsCommands extends DrushCommands {

  /**
   * Migration plugin manager service.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManager
   */
  private $migrationPluginManager;

  /**
   * Groups helper.
   *
   * @var \Drupal\gdomain\GdomainHelper;
   */
  private $gdomainHelper;

  /**
   * MigrateToolsCommands constructor.
   *
   * @param \Drupal\migrate\Plugin\MigrationPluginManager $migrationPluginManager
   *   Migration Plugin Manager service.
   */

  const WEIGHT = 9999;

  public function __construct(GdomainHelper $gdomainHelper, MigrationPluginManager $migrationPluginManager) {
    parent::__construct();
    $this->migrationPluginManager = $migrationPluginManager;
    $this->gdomainHelper = $gdomainHelper;
  }

  /**
   * Perform one or more events migration processes.
   *
   * @command custom_migrate:import_events
   * @aliases mimeg
   * @options arr An option that takes multiple values.
   * @option update
   * @usage custom_migrate:import  --update
   * @usage mimeg  --update
   *
   */
  public function import($options = ['update' => FALSE]) {

    // Update field weight value
    $this->update_weight_events(self::WEIGHT);
    // Events migration ids
    $migrationsIds = [
      'events_images',
      'events_medias',
      'events_nodes',
      'events_nodes_en',
    ];

    $option[] = $options['update'] ? 'update' : NULL;
    //  Get all group where field_openagenda is not null
    $query = \Drupal::entityQuery('group');
    $query->condition('field_openagenda', NULL, '<>');
    // If we are in cli (drush) disable access checks
    if (PHP_SAPI == 'cli') {
      $query->accessCheck(FALSE);
    }
    // Get all ids
    $gids = ($query->execute());
    if ($gids) {
      foreach ($gids as $gid) {
        //Get open agenda url
        $openAgenda = $this->gdomainHelper->getFieldGroupValue($gid, 'field_openagenda');
        if (filter_var($openAgenda, FILTER_VALIDATE_URL
        )) {
          $migrations = $this->preprare_migration($migrationsIds, $openAgenda, $gid);
          foreach ($migrations as $id => $migration) {
            if (!empty($options['update'])) {
              $migration->getIdMap()->prepareUpdate();
            }
            $this->execute_migration($id, $migration, $option);
          }

        }
        else {
          \Drupal::logger('custom_migrate')
            ->error('The Link %url provided for the group id %gid is invalid it needs to be adjusted with the right URL.',
              [
                '%url' => $openAgenda,
                '%gid' => $gid,
              ]);
        }
      }
    }
    else {
      \Drupal::logger('custom_migrate')
        ->error('Please check openAgenda in groups settings');
    }
  }

  /**
   * Executes a single migration.
   *
   * @param $id
   * @param $migration
   * @param $option
   *
   * @throws \Drupal\migrate\MigrateException
   */
  private function execute_migration($id, $migration, $option) {

    \Drupal::logger('custom_migrate')->notice('%id start.', ['%id' => $id]);
    //    Reset migration
    if ($migration->getStatus() != MigrationInterface::STATUS_IDLE) {
      $migration->setStatus(MigrationInterface::STATUS_IDLE);
    }
    $executable = new MigrateExecutable(
      $migration,
      new MigrateMessage(), $option
    );
    //    Execute migration
    $executable->import();

    \Drupal::logger('custom_migrate')
      ->notice('Processed %processed items (%created created, %updated updated, %failed failed, %ignored ignored) - done with %id',
        [
          '%id' => $migration->id(),
          '%processed' => $executable->getProcessedCount(),
          '%created' => $executable->getCreatedCount(),
          '%updated' => $executable->getUpdatedCount(),
          '%failed' => $executable->getFailedCount(),
          '%ignored' => $executable->getIgnoredCount(),

        ]);
  }

  /**
   * Prepare migrations instances
   *
   * @param array $migrationsIds
   * @param $openAgenda
   * @param $gid
   *
   * @return mixed
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  private function preprare_migration(array $migrationsIds, $openAgenda, $gid) {
    foreach ($migrationsIds as $id) {
      $migrations[$id] = $this->migrationPluginManager->createInstance($id, [
        'source' => [
          'urls' => $openAgenda,
          'gid' => $gid,
        ],
      ]);
    }
    return ($migrations);
  }

  /*
 * Update events field weight
 */
  function update_weight_events($value) {
    //get  events with field weight == $value
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'event');
    $query->condition('field_weight', $value, '<>');
    $ids = $query->execute();
    if ($ids) {
      $nodes = Node::loadMultiple($ids);
      if ($nodes) {
        foreach ($nodes as $node) {
          //    set field_weight Value
          $node->field_weight->setValue($value);
          $node->save();
        }
      }
    }
  }

}
