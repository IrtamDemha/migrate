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
 *   id = "vouchers"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field_text:
 *   plugin: transform_value
 *   source: text
 * @endcode
 *
 */
class Vouchers extends ProcessPluginBase
{
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property)
  {


    $elfpj = $value[1];
    foreach ($elfpj as $key => $pj) {
      $ids[$key]['pieceJustificative']['id'] = $value[0].$pj['pieceJustificative']['id'] ;

    }

//    dump($ids);
    return ($ids);
  }
}

