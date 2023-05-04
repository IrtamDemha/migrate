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
 *   id = "filter_url"
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
class FiltreUrl extends ProcessPluginBase
{
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property)
  {

           return(_filter_url($value,'NULL'));
      }
}

