<?php

namespace Drupal\custom_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "jsoncustom",
 *   title = @Translation("JSON CUSTOM")
 * )
 */
class JsonCustom extends Json
{

  protected function getSourceData($url)
  {
    $response = $this->getDataFetcherPlugin()->getResponseContent($url);

    if ($response) {
      $json_decoded = (json_decode($response, true));

      foreach ($json_decoded as $key => $value) {
        $array[] = $value['elemFormJson'];

      }

      $elfpjs = [];
      foreach ($array as $key => $values) {
        foreach ($values['elfPj'] as $item) {
          $elfpjs[$item['pieceJustificative']['id'].$values['id']]['temObligatoire'] = $item ['temObligatoire'] ?: '0';
          $elfpjs[$item['pieceJustificative']['id'].$values['id']]['libelle'] = $item['pieceJustificative']['libelle']['libelleParLangue']['1']['libelle'] ?: $item['pieceJustificative']['libelle']['libelleParLangue']['0']['libelle'];
          $elfpjs[$item['pieceJustificative']['id'].$values['id']]['libelle_id'] = $item['pieceJustificative']['libelle']['id']?: NULL;
          $elfpjs[$item['pieceJustificative']['id'].$values['id']]['id_elfPj'] = $item['pieceJustificative']['id'].$values['id'];
          $elfpjs[$item['pieceJustificative']['id'].$values['id']]['comment'] = $item['libelleCommentaire']['libelleParLangue']['1']['libelle']  ?: $item['libelleCommentaire']['libelleParLangue']['0']['libelle'];

        }
      }

      return $elfpjs;
    }
  }
}
