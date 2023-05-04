<?php

namespace Drupal\custom_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\paragraphs\Entity\Paragraph;


/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "upper_point"
 * )
 *
 * Callback _filter_url function:
 *
 * @code
 * field_text:
 *   plugin: custom_callback
 *   source:
 *     - value 1
 * @endcode
 *
 */
class UpperPoint extends ProcessPluginBase
{
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property)
  {


    if ($value[strlen($value) - 1] != '.') {
      $value = $value . '.';
    }

    return (ucfirst($value));
  }
}

