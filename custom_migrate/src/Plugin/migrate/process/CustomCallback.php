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
 *   id = "custom_callback"
 * )
 *
 * Callback any php function:
 *
 * @code
 * field_text:
 *   plugin: custom_callback
 *   source:
 *     - param 1
 *     - param 2
 *     - param 3
 *     - param 4
 * @endcode
 *
 */
class CustomCallback extends ProcessPluginBase
{
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property)
  {

           return(call_user_func_array($this->configuration['callable'],$value));
      }
}

