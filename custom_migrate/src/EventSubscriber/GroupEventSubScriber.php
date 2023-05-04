<?php

namespace Drupal\custom_migrate\EventSubscriber;

use Drupal\group\Entity\Group;
use Drupal\group\Entity\GroupContent;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\migrate\Event\MigrateEvents;

use Drupal\node\Entity\Node;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class GroupEventSubScriber.
 *
 * @package Drupal\example_events
 */
class GroupEventSubScriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::POST_ROW_SAVE][] = ['onPostrowSave', 800];
    return $events;
  }

  /**
   *    *
   * @param \Drupal\migrate\Event\MigratePostRowSaveEvent $event
   *   Migration event after saving row.
   */
  public function onPostrowSave(MigratePostRowSaveEvent $event) {
    if ($event->getMigration()->id() == "events_nodes") {
      // Get the group->id()
      $gid = ($event->getRow()->getSource()['gid']);
      if ($gid) {
        // Get the node->id()
        $nid = (reset($event->getDestinationIdValues()));
        if ($nid) {
          $group = Group::load($gid);
          $node = Node::load($nid);
          $index = $event->getRow()->getSource()['_index'];

          // Check if not already attched
          if ($gg = $group->getContentByEntityId('group_node:event', $nid)) {
            // update index in group content (field_event_weight)
            if (is_array($gg)) {
              foreach ($gg as $ggg) {
                $ggg->set('field_event_weight', $index)->save();
              }
            }
            else {
              $gg->set('field_event_weight', $index)->save();
            }
          }
          else {
            // attach to node to group
            $group->addContent($node, 'group_node:event', ['field_event_weight' => $index]);
          }
        }
      }
    }
  }

}
