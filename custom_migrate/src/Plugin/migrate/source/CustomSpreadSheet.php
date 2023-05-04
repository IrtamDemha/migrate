<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;

use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\MigrationInterface;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Drupal\migrate_spreadsheet\Plugin\migrate\source\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;

/**
 * Source plugin for retrieving data via URLs.
 *
 * @MigrateSource(
 *   id = "customspreadSheet"
 * )
 */
class CustomSpreadSheet extends Spreadsheet {

  /**
   * Loads the worksheet.
   *
   * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
   *   The source worksheet.
   *
   * @throws \Drupal\migrate\MigrateException
   *   When it's impossible to load the file or the worksheet does not exist.
   */
  protected function loadWorksheet(): Worksheet {
    $config = $this->getConfiguration();

    if (empty($config['file']) || !file_exists($config['file'])) {
      if(!empty($config['directory'])) {
        $directory = $config['directory'];
        $pattern = '/.*\.(xlsx|xls)$/';
        if (is_dir($directory)) {
          $files = \Drupal::service('file_system')->scanDirectory($directory, $pattern);
        }
        $config['file'] = reset($files)->uri ;
      }
    }

    // Check that the file exists.
    if (!file_exists($config['file'])) {
      throw new MigrateException("Either the file defined in config[file] ('{$config['file']}') doesn't exist. Or there is no xls or xlsx in the config[directory]");
    }

    // Check that a non-empty worksheet has been passed.
    if (empty($config['worksheet'])) {
      throw new MigrateException('No worksheet was passed.');
    }

    // Load the workbook.
    try {
      $file_path = $this->fileSystem->realpath($config['file']);

      // Identify the type of the input file.
      $type = IOFactory::identify($file_path);

      // Create a new Reader of the file type.
      /** @var \PhpOffice\PhpSpreadsheet\Reader\BaseReader $reader */
      $reader = IOFactory::createReader($type);
      if(filesize ($file_path) > 5030000) {
        Settings::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_COMPACT | LIBXML_PARSEHUGE);
      }

      // Advise the Reader that we only want to load cell data.
      $reader->setReadDataOnly(TRUE);

      // Advise the Reader of which worksheet we want to load.
      $reader->setLoadSheetsOnly($config['worksheet']);

      /** @var \PhpOffice\PhpSpreadsheet\Spreadsheet $workbook */
      $workbook = $reader->load($file_path);

      return $workbook->getSheet(0);
    }
    catch (\Exception $e) {
      $class = get_class($e);
      throw new MigrateException("Got '$class', message '{$e->getMessage()}'.");
    }
  }

  /**
   * Checks whether the iterator is currently valid.
   *
   * Implementation of \Iterator::valid() - called at the top of the loop,
   * returning TRUE to process the loop and FALSE to terminate it.
   */
  public function valid() {
    // Checks if the source keys are not empty
    if(!empty($this->currentSourceIds)) {
      foreach($this->currentSourceIds as $k => $v) {
        if(empty($v)) {
          return false;
        }
      }
    }
    return parent::valid();
  }

}
