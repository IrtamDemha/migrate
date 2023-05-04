<?php

namespace Drupal\custom_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "jsonlpj",
 *   title = @Translation("JSON CUSTOM")
 * )
 */
class JsonLpj extends Json
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
          $elfpjs[$values['id'] . $item['pieceJustificative']['id']]['temObligatoire'] = $item ['temObligatoire'] ?: '0';
          $elfpjs[$values['id'] . $item['pieceJustificative']['id']]['id_elfPj'] = $values['id'] . $item['pieceJustificative']['id'];
          
          foreach ($item['pieceJustificative']['libelle']['libelleParLangue'] as $libelle) {
            $codLang = strtolower($libelle['codLangueAppli']['codLangue']);
            $elfpjs[$values['id'] . $item['pieceJustificative']['id']]['libelle_' . $codLang] = $libelle['libelle'];
          }

          // Gererate array[codlang]= libelle_lang
          if ($item['libelleCommentaire']) {
            foreach ($item['libelleCommentaire']['libelleParLangue'] as $comment) {
              $comments[$values['id'] . $item['pieceJustificative']['id']][strtolower($comment['codLangueAppli']['codLangue'])] = $comment['libelle'];
            }
          }

          $elfpjs[$values['id'] . $item['pieceJustificative']['id']]['comment_fr'] = $comments[$values['id'] . $item['pieceJustificative']['id']]['fr'] ?: $comments[$values['id'] . $item['pieceJustificative']['id']]['en'];
          $elfpjs[$values['id'] . $item['pieceJustificative']['id']]['comment_en'] = $comments[$values['id'] . $item['pieceJustificative']['id']]['en'] ?: $comments[$values['id'] . $item['pieceJustificative']['id']]['fr'];

        }
      }

      return $elfpjs;
    }
  }
}
