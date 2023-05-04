<?php

namespace Drupal\custom_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "sub_person",
 *   handle_multiples = TRUE
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
class SubResponsible extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $persons = [];
    foreach ($value as $key => $person) {
      if (($person['role']['id'] == '13') && ($person ['personne']['actif'])) {
        $hash = md5($person['personne']['email'] . $person['personne']['prenom'] . $person['personne']['nom']); 
        $persons[$key]['name'] = $person['personne']['prenom'].' '.$person['personne']['nom'];
        $persons[$key]['email'] = $person['personne']['email'];
        $persons[$key]['id'] = $person['personne']['id'] . '-' . $hash;
      }
    }
    return ($persons);
  }

}
