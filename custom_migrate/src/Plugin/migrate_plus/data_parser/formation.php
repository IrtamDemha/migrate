<?php

namespace Drupal\custom_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "formation",
 *   title = @Translation("formation")
 * )
 */
class formation extends Json
{

  protected function getSourceData($url)
  {
    $response = $this->getDataFetcherPlugin()->getResponseContent($url);

    if ($response) {
      $json_decoded = (json_decode($response, true));

      foreach ($json_decoded as $key => $value) {
        $array[] = $value['elemFormJson'];

      }

      $mentions = [];
      foreach ( $array as $key => $value)
      {
        foreach ($value['mentions'] as  $item)
        {
          $mentions[$item['codRof']]['libelle'] = $item['libelle']['libelleParLangue']['1']['libelle'];
          $mentions[$item['codRof']]['libelle_en'] = $item['libelle']['libelleParLangue']['0']['libelle'];
          $mentions[$item['codRof']]['master'][] = $json_decoded[$key]['elemFormJson']['codRof'];
          $mentions[$item['codRof']]['id'] = $item['codRof'];
          $mentions[$item['codRof']]['creation'] = $item['dateCreation'];
          $mentions[$item['codRof']]['school']= $item['school']['libSchool'];
          $mentions[$item['codRof']]['responsables'] = $item['responsables'];
          $mentions[$item['codRof']]['lang'] = $this->configuration['language'];


        }

      }

      return $mentions;
    }
  }
}
