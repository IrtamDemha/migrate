<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;



use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\migrate\source\Url;

/**
 * Provides a source plugin that migrate from spreadsheet files.
 *
 * This source plugin uses the PhpOffice/PhpSpreadsheet library to read
 * spreadsheet files.
 *
 * @MigrateSource(
 *   id = "custom_url"
 * )
 */
class CustomUrl extends Url {



  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    $labStart = (isset($configuration['labStart'])) ? $configuration['labStart'] : 1;
    $configuration['urls'] = $this->getUrls($configuration['urls'],$configuration['labcount'],$configuration['lablength'], $labStart);

    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

  }

  /**
   * @param $url
   * @param $labcount
   * @param $lablenght
   */
  protected function getUrls($url,$labcount,$lablenght, $labStart = 1)
  {
    if ($url){
      for ($i = $labStart; $i < $lablenght; $i=$i+$labcount)
      {
        $uri = $url[0] . '?labStart=' . $i . '&labcount=' . $labcount;
        $urls[] = $uri;
      }
    }
    return($urls);
  }


}
