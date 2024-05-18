<?php

/**
 * @file
 * A form to collect and email address for RSVP details.
 */

namespace Drupal\rsvplist\Form;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use const Exception;

class RsvpForm extends FormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $node = \Drupal::routeMatch()->getParameter('node');
    // check if node is not null, set nid to node id or zero
    // this accounts for cases where page has no node id
    $nid = !(is_null($node)) ? $node->id() : 0;

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => t('Email address'),
      '#size' => 25,
      '#description' => t('We will send updates to this email address.'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];

    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $email = $form_state->getValue('email');
    if (!(Drupal::service('email.validator')->isValid($email))) {
      $form_state->setErrorByName('email', $this->t('%mail is an invalid email address.', ['%mail' => $email]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
//    $submitted_email = $form_state->getValue('email');
//    $this->messenger()->addMessage(t('The form is working! Your entered @entry.', ['@entry' => $submitted_email]));

    // encapsulte code in try catch
    try {
      // initiate variables to save

      // Get current user ID
      $uid = \Drupal::currentUser()->id();

      // Demonstration for how to load full user object
      // useful for getting user data for operations that require it
      $full_user = User::load(\Drupal::currentUser()->id());

      // Get volaues enteres into Form
      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email');
      $current_time = \Drupal::time()->getRequestTime();

      // Save the values to the database
      // Start to build a questy builder object $query
      $query = \Drupal::database()->insert('rsvplist');
      // specify the filed that the query will insert into
      $query->fields([
        'uid',
        'nid',
        'mail',
        'created',
      ]);
      // set the values of the fields we selected
      $query->values([
        $uid,
        $nid,
        $email,
        $current_time,
      ]);
      // Execute the query
      $query->execute();
      \Drupal::messenger()->addMessage(
        t('The form is working! Your entered @entry.', ['@entry'])
      );
    }
    catch (Exception $e) {
      \Drupal::messenger()->addError(
        t('Whoops! Something went wrong. Please try again.')
      );
    }
  }
}


