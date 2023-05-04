<?php

namespace Drupal\custom_migrate\Plugin\migrate\process;

use Drupal\migrate\Plugin\migrate\process\Substr;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Returns a substring of the input value.
 *
 * The substr process plugin returns the portion of the input value specified by
 * the start and length parameters. This is a wrapper around mb_substr().
 *
 * Available configuration keys:
 * - start: (optional) The returned string will start this many characters after
 *   the beginning of the string, defaults to 0.
 * - length: (optional) The maximum number of characters in the returned
 *   string, defaults to NULL.
 * - elipsis: (optional) The elipsis defaults to '…'
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "substr_and_elipsis"
 * )
 */
class SubstrAndElipsis extends Substr {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $new_value = parent::transform($value, $migrate_executable, $row, $destination_property);
    if($value != $new_value) {
      $elipsis = isset($this->configuration['elipsis']) ? $this->configuration['elipsis'] : '…';
      $new_value = $new_value . $elipsis;
    }
    return $new_value;
  }

}
