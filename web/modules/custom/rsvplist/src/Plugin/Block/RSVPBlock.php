<?php

/**
 * @file
 * Creates a block wichi dsipalys the RSVPForm containted here
 */

namespace Drupal\rsvplist\Plugin\Block;

use Drupal;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides the RSVP main block type.
 * @Block(
 *   id = "rsvp_block",
 *   admin_label = @Translation("RSVP Form block"),
 *   )
 */
class RSVPBlock extends BlockBase
{

  public function build()
  {
//    just kidding
//    $markup = '<div class="rsvp-block">';
//    $markup .= '<p>' . $this->t('RSVP List Block') . '</p>';
//    $markup .= '</div>';
//    return [
//      '#type' => 'markup',
//      '#markup' => $markup,
//    ];
    return Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
  }

  public function blockAccess(AccountInterface $account)
  {
    $node = Drupal::routeMatch()->getParameter('node');

    if (!(is_null($node))) {
      $enabler = \Drupal::service('rsvplist.enabler');
      if ($enabler->isEnabled($node)) {
        return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
      }
    }
    return AccessResult::forbidden();
  }
}
