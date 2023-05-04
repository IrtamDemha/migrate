<?php

namespace Drupal\custom_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Xml;
use Drupal\migrate\MigrateException;
use Drupal\migrate_plus\DataParserPluginBase;

/**
 * Obtain XML data for migration using the XMLReader pull parser.
 *
 * @DataParser(
 *   id = "xml_transformed",
 *   title = @Translation("XML Transformed via xsl")
 * )
 */
class XmlTransformed extends Xml {

  /**
   * The XSLTProcessor we are encapsulating.
   *
   * @var \XSLTProcessor
   */
  protected $xsltProcessor;
  protected $xslFile;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->xslFile = $configuration['xsl_file'];
    
    $xsl = new \DOMDocument;
    $xsl->load($configuration['xsl_file']);
    $this->xsltProcessor = new \XSLTProcessor();
    $this->xsltProcessor->importStyleSheet($xsl);

  }

  /**
   * {@inheritdoc}
   */
  protected function fetchNextRow() {
    $target_element = NULL;

    // Loop over each node in the XML file, looking for elements at a path
    // matching the input query string (represented in $this->elementsToMatch).
    while ($this->reader->read()) {
      if ($this->reader->nodeType == \XMLReader::ELEMENT) {
        if ($this->prefixedName) {
          $this->currentPath[$this->reader->depth] = $this->reader->name;
          if (in_array($this->reader->name, $this->parentElementsOfInterest)) {
            $this->parentXpathCache[$this->reader->depth][$this->reader->name][] = $this->getSimpleXml();
          }
        }
        else {
          $this->currentPath[$this->reader->depth] = $this->reader->localName;
          if (in_array($this->reader->localName, $this->parentElementsOfInterest)) {
            $this->parentXpathCache[$this->reader->depth][$this->reader->name][] = $this->getSimpleXml();
          }
        }
        if ($this->currentPath == $this->elementsToMatch) {
          // We're positioned to the right element path - build the SimpleXML
          // object to enable proper xpath predicate evaluation.
          $target_element = $this->getSimpleXml();
          if ($target_element !== FALSE) {
            if (empty($this->xpathPredicate) || $this->predicateMatches($target_element)) {
              break;
            }
          }
        }
      }
      elseif ($this->reader->nodeType == \XMLReader::END_ELEMENT) {
        // Remove this element and any deeper ones from the current path.
        foreach ($this->currentPath as $depth => $name) {
          if ($depth >= $this->reader->depth) {
            unset($this->currentPath[$depth]);
          }
        }
        foreach ($this->parentXpathCache as $depth => $elements) {
          if ($depth > $this->reader->depth) {
            unset($this->parentXpathCache[$depth]);
          }
        }
      }
    }

    // If we've found the desired element, populate the currentItem and
    // currentId with its data.
    if ($target_element !== FALSE && !is_null($target_element)) {
      foreach ($this->fieldSelectors() as $field_name => $xpath) {
        $prefix = substr($xpath, 0, 3);
        if (in_array($prefix, ['../', '..\\'])) {
          $name = str_replace($prefix, '', $xpath);
          $up = substr_count($xpath, $prefix);
          $values = $this->getAncestorElements($up, $name);
        }
        else {
          $values = $target_element->xpath($xpath);
        }
        foreach ($values as $value) {
          // If the SimpleXMLElement doesn't render to a string of any sort,
          // and has children then return the whole object for the process
          // plugin or other row manipulation.
          if ($value->children() && !trim((string) $value)) {
            $this->currentItem[$field_name] = $value;
          }
          else {
            $field_info = $this->getFieldDefinition($field_name);
            if(!empty($field_info['xsl_transform'])) {
              $xml_string = (string) $value->asXML();
              $xml = new \DOMDocument;
              $xml->loadXML($xml_string);
              $xml_string_transformed = $this->xsltProcessor->transformToXML($xml);
              $xml_string_transformed = str_replace('<?xml version="1.0"?>', '', $xml_string_transformed);
              $xml_string_transformed = trim($xml_string_transformed);
              $this->currentItem[$field_name][] = (string) $xml_string_transformed;
            } else {
              $this->currentItem[$field_name][] = (string) $value;
            }
          }
        }
      }
      // Reduce single-value results to scalars.
      foreach ($this->currentItem as $field_name => $values) {
        if (count($values) == 1) {
          $this->currentItem[$field_name] = reset($values);
        }
      }
    }
  }

  /**
   * Return 
   *
   * @return string[]
   *   Array of selectors, keyed by field name.
   */
  protected function getFieldDefinition($name) {
    foreach ($this->configuration['fields'] as $field_info) {
      if($field_info['name'] == $name) {
        return $field_info;
      }
    }
    return false;
  }

}
