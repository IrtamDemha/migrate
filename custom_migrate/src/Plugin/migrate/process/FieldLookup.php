<?php

namespace Drupal\custom_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "fieldlookup"
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
class FieldLookup extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // As the migrate goes on, store the items in a drupal static array
    $nid = $value[0];
    $referenced_nid = $value[1];
    $field_name = $this->configuration['field'];
    $drupal_static_fast = &drupal_static(__FUNCTION__ . "_{$nid}_{$field_name}");
    $target_ids = [];

    if ($nid && $this->configuration['type'] == 'node') {
      if (isset($drupal_static_fast[$nid])) {
        $target_ids = $drupal_static_fast[$nid];
      }
      if(!in_array($referenced_nid, $target_ids)) {
        $target_ids[] = $referenced_nid;
      }
      $drupal_static_fast[$nid] = $target_ids;
    }

    if(!empty($target_ids)) {
      $return = [];
      foreach($target_ids as $id) {
        $return[] = ['target_id' => $id];
      }
      return $return;
    }

    return [];

  }

  /**
   * {@inheritdoc}
   */
  public function transform_old($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property)
  {

     $drupal_static_fast = &drupal_static(__FUNCTION__);;

    if ($value[0]) {

      $exist = False;
      if (($this->configuration['type'] == 'node') && ($filed = $this->configuration['field'])) {
        $lenght = 0;

        if (!isset($drupal_static_fast[$value[0]])) {

          if ($node = \Drupal\node\Entity\Node::load($value[0])) {
            $target_id = $node->$filed->getValue();

          }
        } else {
      
          $target_id = $drupal_static_fast[$value[0]];
          $lenght = sizeof($target_id);
          foreach ($target_id as $id) {
            if ($id['target_id'] == $value['1'])
              $exist = True;
          }

        }

        if (!$exist) {
          $target_id[$lenght]['target_id'] = $value['1'];
        }
        $drupal_static_fast[$value[0]] = $target_id;

      }
    }

    return ($target_id);

  }
}