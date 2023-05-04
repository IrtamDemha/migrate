<?php

namespace Drupal\custom_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "person",
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
class Person extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $persons = [];
    foreach ($value as $key => $person) {
      if ($person['utilisateur']['actif']) {
        $hash = md5($person['utilisateur']['email'] . $person['utilisateur']['prenom'] . $person['utilisateur']['nom']); 
        $persons[$key]['name'] = $person['utilisateur']['prenom'] . ' ' . $person['utilisateur']['nom'];
        $persons[$key]['email'] = $person['utilisateur']['email'];
        $persons[$key]['id'] = $person['utilisateur']['id'] . '-' . $hash;
      }
    }
    return ($persons);
  }
}

