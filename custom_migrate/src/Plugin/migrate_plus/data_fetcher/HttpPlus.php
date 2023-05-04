<?php

namespace Drupal\custom_migrate\Plugin\migrate_plus\data_fetcher;

use Drupal\migrate\MigrateException;
use Drupal\migrate_plus\Plugin\migrate_plus\data_fetcher\Http;
use GuzzleHttp\Exception\RequestException;


/**
 * Retrieve data over an HTTP connection for migration.
 * Allows to skip curl peer validation & add timeout
 * Example:
 *
 * @code
 * source:
 *   plugin: url
 *   data_fetcher_plugin: http_plus
 *   headers:
 *     Accept: application/json
 *     User-Agent: Internet Explorer 6
 *     Authorization-Key: secret
 *     Arbitrary-Header: foobarbaz
 *     curl:
 *        verify: FALSE
 *     request_options:
 *        timeout: 180
 * @endcode
 *
 * @DataFetcher(
 *   id = "http_plus",
 *   title = @Translation("HTTPPLUS")
 * )
 */
class HttpPlus extends Http {

  /**
   * {@inheritdoc}
   */
  public function getResponse($url) {
    try {
      $options = ['headers' => $this->getRequestHeaders()];
      // Add curl peer verification
      if(!empty($this->configuration['curl'])){
        $options = array_merge($options, $this->configuration['curl']);
      }
      // Add timeout
      if(!empty($this->configuration['request_options'])){
        $options = array_merge($options, $this->configuration['request_options']);
      }
      if (!empty($this->configuration['authentication'])) {
        $options = array_merge($options, $this->getAuthenticationPlugin()->getAuthenticationOptions());
      }

      $response = $this->httpClient->get($url, $options);
      if (empty($response)) {
        throw new MigrateException('No response at ' . $url . '.');
      }
    }
    catch (RequestException $e) {
      if(empty($this->configuration['no_exception'])){
        throw new MigrateException('Error message: ' . $e->getMessage() . ' at ' . $url . '.');
      }
    }
    return $response;
  }

}
