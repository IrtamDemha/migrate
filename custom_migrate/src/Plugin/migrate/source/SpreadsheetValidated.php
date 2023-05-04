<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;

use Drupal\migrate_spreadsheet\Plugin\migrate\source\Spreadsheet;

/**
 * Provides a source plugin that migrate from spreadsheet files.
 *
 * This source plugin uses the PhpOffice/PhpSpreadsheet library to read
 * spreadsheet files.
 *
 * @MigrateSource(
 *   id = "spreadsheet_validated"
 * )
 */
class SpreadsheetValidated extends Spreadsheet {

  /**
   * Checks whether the iterator is currently valid.
   *
   * Implementation of \Iterator::valid() - called at the top of the loop,
   * returning TRUE to process the loop and FALSE to terminate it.
   */
  public function valid() {
    // Checks if the source keys are not empty
    foreach($this->currentSourceIds as $k => $v) {
      if(empty($v)) {
        return false;
      }
    }
    return parent::valid();
  }

}
