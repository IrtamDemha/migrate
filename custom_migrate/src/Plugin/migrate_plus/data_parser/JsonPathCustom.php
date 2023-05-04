<?php

namespace Drupal\custom_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_source_jsonpath\Plugin\migrate_plus\data_parser\JsonPath;
use JsonPath\JsonObject;

/**
 * Obtain JSON data for migration using JSONPath.
 *
 * @DataParser(
 *   id = "jsonpath_custom",
 *   title = @Translation("JSONPath custom")
 * )
 */
class JsonPathCustom extends JsonPath {

  /**
   * {@inheritdoc}
   */
  protected function getSourceData($url) {
    $source_data = parent::getSourceData($url);
    if(empty($source_data)) {
      $source_data = [];
    }
    return $source_data;
  }

  /**
   * {@inheritdoc}
   */
  protected function fetchNextRow() {
    $current = $this->iterator->current();
    if ($current) {
      foreach ($this->fieldSelectors() as $field_name => $selector) {
        $json = new JsonObject($current, TRUE);
        $field_value = $json->get($selector);

        // BEGIN OVERRIDE => Fix conflict with subbprocess on single value arrays, @todo make configurable
        // // If selector returned single value in array.
        // // Single value required to be a string for being able to use this field
        // // as source primary key.
        // if (is_array($field_value) && 1 == count($field_value)) {
        //   $field_value = reset($field_value);
        // }
        // END OVERRIDE

        $this->currentItem[$field_name] = $field_value;
      }
      if (!empty($this->configuration['include_raw_data'])) {
        $this->currentItem['raw'] = $current;
      }
      $this->iterator->next();
    }
  }

}
