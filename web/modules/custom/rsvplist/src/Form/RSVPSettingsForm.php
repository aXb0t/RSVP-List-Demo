<?php

/**
 * @file
 * Contains the settings for admin'ing the RSVP form
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'rsvplist_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      'rsvplist.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {

    // Add $types variable that calls method node_type_get_names()
    $types = node_type_get_names();

    // Add config variable that gets config from $this config machine name: 'rsvplist.settings'
    $config = $this->config('rsvplist.settings');

    // Define $form index 'rsvplist_types' with type, title, default_value, options and description
    $form['rsvplist_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('The content types to enable RSVP collection for'),
      '#default_value' => $config->get('allowed_types'),
      '#options' => $types,
      '#description' => $this->t('On the sprecified node types, an RSVP option will be available and can be enabled while the code is being edited.'),
    ];

    // Form elements go here
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $selected_allowed_types = array_filter($form_state->getValue(
      'rsvplist_types'));
    sort($selected_allowed_types);

    $this->config('rsvplist.settings')
    ->set('allowed_types', $selected_allowed_types)
    ->save();

    parent::submitForm($form, $form_state);
  }

}
