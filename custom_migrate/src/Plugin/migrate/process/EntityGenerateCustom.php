<?php

namespace Drupal\custom_migrate\Plugin\migrate\process;

use Drupal\migrate_plus\Plugin\migrate\process\EntityGenerate;

/**
 * This plugin generates entities within the process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "entity_generate_custom"
 * )
 *
 * @see EntityLookup
 * 
 */
class EntityGenerateCustom extends EntityGenerate {
  /**
   * Fabricate an entity.
   *
   * This is intended to be extended by implementing classes to provide for more
   * dynamic default values, rather than just static ones.
   *
   * @param mixed $value
   *   Primary value to use in creation of the entity.
   *
   * @return array
   *   Entity value array.
   */
  protected function entity($value) {
    $entity_values = parent::entity($value);

    if (isset($this->configuration['values']) && is_array($this->configuration['values'])) {
      foreach ($this->configuration['values'] as $key => $property) {
        if(!empty($property)) {
          $entity_values[$key] = $this->row->get($property);
        }
      }
    }

    if (isset($this->configuration['value_keys']) && is_array($this->configuration['value_keys'])) {
        foreach ($this->configuration['value_keys'] as $key) {
            $entity_values[$key] = $value;
        }
    }
    
    return $entity_values;
  }
}
