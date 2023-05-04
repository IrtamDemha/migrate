<?php

namespace Drupal\custom_migrate\Plugin\migrate\destination;

use Drupal\migrate\Plugin\migrate\destination\NullDestination;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;

/**
 * Provides null destination plugin.
 *
 * @MigrateDestination(
 *   id = "nowhere",
 * )
 */
class Nowhere extends NullDestination {
  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->supportsRollback = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    // The no-op always succeeds. Returning TRUE here prevents a 'failed'
    // being thrown. However, it also gives no indication of progress.
    return TRUE;
  }
}
