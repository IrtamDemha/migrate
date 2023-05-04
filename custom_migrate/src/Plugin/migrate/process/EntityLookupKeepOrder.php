<?php

namespace Drupal\custom_migrate\Plugin\migrate\process;

use Drupal\migrate_plus\Plugin\migrate\process\EntityLookup;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * This plugin looks for existing entities and keeps order.
 * 
 * @see Drupal\migrate_plus\Plugin\migrate\process\EntityLookup
 *
 * @codingStandardsIgnoreEnd
 *
 * @MigrateProcessPlugin(
 *   id = "entity_lookup_keep_order",
 *   handle_multiples = TRUE
 * )
 */
class EntityLookupKeepOrder extends EntityLookup {

  /**
   * Checks for the existence of some value.
   *
   * @param mixed $value
   *   The value to query.
   *
   * @return mixed|null
   *   Entity id if the queried entity exists. Otherwise NULL.
   */
  protected function query($value) {
    // Entity queries typically are case-insensitive. Therefore, we need to
    // handle case sensitive filtering as a post-query step. By default, it
    // filters case insensitive. Change to true if that is not the desired
    // outcome.
    $ignoreCase = !empty($this->configuration['ignore_case']) ?: FALSE;

    $multiple = is_array($value);

    $query = $this->entityTypeManager->getStorage($this->lookupEntityType)
      ->getQuery()
      ->accessCheck($this->accessCheck)
      ->condition($this->lookupValueKey, $value, $multiple ? 'IN' : NULL);

    if ($this->lookupBundleKey) {
      $query->condition($this->lookupBundleKey, $this->lookupBundle);
    }
    
    $results = $query->execute();
    if (empty($results)) {
      return NULL;
    }

    // By default do a case-sensitive comparison.
    if (!$ignoreCase) {
      // Returns the entity's identifier.
      foreach ($results as $k => $identifier) {
        $entity = $this->entityTypeManager->getStorage($this->lookupEntityType)->load($identifier);
        $result_value = $entity instanceof ConfigEntityInterface ? $entity->get($this->lookupValueKey) : $entity->get($this->lookupValueKey)->value;
        if (($multiple && !in_array($result_value, $value, TRUE)) || (!$multiple && $result_value !== $value)) {
          unset($results[$k]);
        }
      }
    }

    if ($multiple && !empty($this->destinationProperty)) {
      array_walk($results, function (&$value) {
        $value = [$this->destinationProperty => $value];
      });
    }

    return $multiple ? array_values($results) : reset($results);
  }

}
