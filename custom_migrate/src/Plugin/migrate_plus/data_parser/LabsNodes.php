<?php

namespace Drupal\custom_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Obtain JSON data for migration.
 * 
 * @todo to delete not used anymore
 *
 * @DataParser(
 *   id = "labsnodes",
 *   title = @Translation("Labs CUSTOM")
 * )
 */
class LabsNodes extends Json
{
  /**
   * @param string $url
   * @return array|mixed
   */
  protected function getSourceData($url)
  {
    $response = $this->getDataFetcherPlugin()->getResponseContent($url);

    if ($response) {
      $json_decoded = (json_decode($response, true));
      if ($json_decoded) {
        foreach ($json_decoded as $key => $value) {

          if ($value['graduateSchools']) {
            foreach ($value['graduateSchools'] as $graduate) {
              $graduate_school[]['label'] = $graduate;
            }
            $json_decoded[$key]['graduateSchools'] = $graduate_school;
            unset($graduate_school);
          }

          if ($value['docs']) {
            foreach ($value['docs'] as $k => $doc) {
              $ids[]['id_doc'] = $value['officialName'].$key.$k;
            }
            $json_decoded[$key]['paragraph'] = $ids;
            unset($ids);
          }

          if ($value['supervisions']) {
            foreach ($value['supervisions'] as $supervision) {
              $supervisions[]['label'] = $supervision;
            }
            $json_decoded[$key]['supervisions'] = $supervisions;
            unset($supervisions);
          }

        }

        return $json_decoded;
      }
    }
  }


  /**
   * {@inheritdoc}
   */
  protected function openSourceUrl($url) {
    // (Re)open the provided URL.

    $source_data = $this->getSourceData($url);
    if($source_data) {
      $this->iterator = new \ArrayIterator($source_data);
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function fetchNextRow()
  {
    if ($this->iterator) {
      $current = $this->iterator->current();
      if ($current) {
        foreach ($this->fieldSelectors() as $field_name => $selector) {
          $field_data = $current;
          $field_selectors = explode('/', trim($selector, '/'));
          foreach ($field_selectors as $field_selector) {
            if (is_array($field_data) && array_key_exists($field_selector, $field_data)) {
              $field_data = $field_data[$field_selector];
            } else {
              $field_data = '';
            }
          }
          $this->currentItem[$field_name] = $field_data;
        }
        if (!empty($this->configuration['include_raw_data'])) {
          $this->currentItem['raw'] = $current;
        }
        $this->iterator->next();
      }
    }
  }


}
