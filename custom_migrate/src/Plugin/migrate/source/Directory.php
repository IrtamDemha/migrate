<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\migrate\source\Url;

/**
 * Source plugin for retrieving data via URLs.
 *
 * @MigrateSource(
 *   id = "directory"
 * )
 */
class Directory extends Url {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    $directory = $configuration['directory'];
    $pattern = ($configuration['pattern']) ?: '/.*/';
    if (is_dir($directory)) {
      $files = \Drupal::service('file_system')->scanDirectory($directory, $pattern);
    }
    
    if(empty($files) && !empty($configuration['zip_init'])) {
      $real_directory = \Drupal::service('file_system')->realpath($directory);
      exec("cd {$real_directory} && curl {$configuration['zip_init']} -o archive.tgz && tar -zxf archive.tgz");
      if (is_dir($directory)) {
        $files = \Drupal::service('file_system')->scanDirectory($directory, $pattern);
      }
    }

    if(!empty($files)) {
      $configuration['urls'] = array_keys($files);
    }

    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->sourceUrls = $configuration['urls'];
  }

}
