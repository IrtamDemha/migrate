<?php

declare(strict_types=1);

namespace Drupal\custom_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "checkurl",
 *   handle_multiples = FALSE
 * )
 */
class checkurl extends ProcessPluginBase
{

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if (filter_var($value, FILTER_VALIDATE_URL)) {
      return ($value);
    }

  }


}
