<?php

/**
 * @file
 * Contains the RSVP Enabler service
 */

namespace Drupal\rsvplist;

use Drupal\Core\Database\Connection;
use Drupal\node\Entity\Node;

class EnablerService {

  protected $database_connection;

  public function __construct(Connection $connection) {
    $this->database_connection = $connection;
  }

  /**
   * Checks if RSVP is enabled for a given node.
   *
   * @param Node $node
   * @return bool
   *   TRUE if RSVP is enabled for the node, FALSE otherwise.
   */
  public function isEnabled(Node $node) {
    // if the node is new, basically impossible to have an RSVP, so just skip it
    if ($node->isNew()) {
      return FALSE;
    }
    try {
      $query = $this->database_connection->select('rsvplist_enabled', 're');
      $query->fields('re', ['nid']);
      $query->condition('nid', $node->id());
      $results = $query->execute();

      return !empty($results->fetchCol());
    } catch (\Exception $e) {
      \Drupal::messenger()->addError(t('Unable to determine RSVP settings at this time. Please try again.'));
      return NULL;
    }

  }
  /**
   * Sets an individual node to be RSVP enabled.
   *
   * @param Node $node
   * @throws Exception
   */
  public function setEnabled(Node $node)
  {
    try {
      if (!($this->isEnabled($node))) {
        $insert = $this->database_connection->insert('rsvplist_enabled');
        $insert->fields(['nid']);
        $insert->values([$node->id()]);
        $insert->execute();
      }
    } catch (\Exception $e) {
      \Drupal::messenger()->addError(t('Unable to set RSVP settings at this time. Please try again.'));
    }
  }
    /**
   * Deletes the RSVP enabled status for a given node.
   *
   * @param Node $node
   */
  public function delEnabled(Node $node)
  {
    try {
      $delete = $this->database_connection->delete('rsvplist_enabled');
      $delete->condition('nid', $node->id());
      $delete->execute();
    } catch (\Exception $e) {
      \Drupal::messenger()->addError(t('Unable to delete RSVP settings at this time. Please try again.'));
    }
  }

}
