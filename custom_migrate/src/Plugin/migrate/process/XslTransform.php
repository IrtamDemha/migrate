<?php

namespace Drupal\custom_migrate\Plugin\migrate\process;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 *
 * @MigrateProcessPlugin(
 *   id = "xsl_transform"
 * )
 */
class XslTransform extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $xsl_file = isset($this->configuration['xsl']) ? $this->configuration['xsl'] : '';

    $xsl = simplexml_load_file($xsl_file);
    $xslt = new XSLTProcessor;
    $xslt->importStyleSheet($xsl);

    $new_value = $xslt->transformToXML($xml);

    dump($new_value);
    
    return $new_value;
  }
}
