<?php

namespace Drupal\webform_cookies_handler\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformInterface;
use Drupal\webform\WebformSubmissionConditionsValidatorInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\WebformTokenManagerInterface;

/**
 * Defines a form that configures forms module settings.
 */
class WebformCookiesHandlerConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'webform_cookies_handler_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'webform_cookies_handler.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $config = $this->config('webform_cookies_handler.settings');
    
    # Make sure the default values are set
    $default_cookies = $config->get('default_cookies');
    if (is_string($default_cookies)) { 
      $default_cookies =  $default_cookies; 
    } else { 
      $default_cookies = 'Enter comma separated list here...';
    }

    $default_all_webforms = $config->get('apply_to_all_webforms');
    if (is_numeric($default_all_webforms)) { 
      $default_all_webforms =  $default_all_webforms; 
    } else { 
      $default_all_webforms = 0;
    }

    $form['webform_cookies_handler_admin'] = array(
        '#type' => 'fieldset',
        '#title' => t('Advanced Webform Cookies Settings'),
        '#description' => t('Site wide webform cookie settings'),
    );
    
    $form['webform_cookies_handler_admin']['apply_to_all_webforms'] = array(
        '#type' => 'checkbox',
        '#title' => t('Apply Webform Cookies Handler to All Webforms.'),
        '#description' => t('This setting will retroactively apply the handler to all webforms.
        However, it will not overwrite Webforms already configured with Webform Cookies Handler.'),
        '#default_value' => $default_all_webforms,
        '#required' => FALSE,
    );

    $form['webform_cookies_handler_admin']['default_cookies'] = array(
      '#type' => 'textfield', 
      '#title' => t('Default Cookies to be added to webforms'), 
      '#description' => t('Apply Webforms Cookies Handler to all Webforms retroactively with these cookies set as default.'),
      '#default_value' => $default_cookies,
      '#size' => 60, 
      '#maxlength' => 128, 
      '#required' => FALSE,
      
    );
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    
    # Set the user specified site-wide cookies configuration
    $this->config('webform_cookies_handler.settings')
      ->set('apply_to_all_webforms', $values['apply_to_all_webforms'])
      ->save();

    # Set user specified default Cookies
    $this->config('webform_cookies_handler.settings')
    ->set('default_cookies', $values['default_cookies'])
    ->save();

    # If the user updated site wide webform cookie settings
    if ($values['apply_to_all_webforms']) {
      $this->attach_handler_to_all_webforms($values['default_cookies']);
    }
    parent::submitForm($form, $form_state);
  }

  # Add Webform_Cookies handler to all webforms
  private function attach_handler_to_all_webforms($default_cookies) {

    $ids = $this->get_all_webform_ids();

    # tokenize the cookies string 
    $cookies = $default_cookies;
    $cookies = preg_replace('/\s+/', '', $cookies);
    $tokens = explode(',', $cookies);

    # Set up our Webform Cookies Handler Config
    $handler_configuration = [
      'id' => 'webform_cookies',
      'label' => 'webform_cookies',
      'handler_id' => 'webform_cookies',
      'status' => 1,
      'weight' => 0,
      'settings' => array(
        'cookies' => $default_cookies,
        'string_matching' => FALSE,
        'tokens' => $tokens,
      ),
      
    ];

    $handler_manager = \Drupal::service('plugin.manager.webform.handler');
    $handler = $handler_manager->createInstance('Webform Cookies', $handler_configuration);


    # Now going to open each webform one-by-one and add cookies handler with default config
    foreach ($ids as $index => $id) {
      $webform = \Drupal::entityTypeManager()->getStorage('webform')->load($id);
      //ksm($webform->status());
      if ($webform) {
        ksm($id);
        // Must set original id so that the webform can be resaved.
        $webform->setOriginalId($webform->id());


        $handlers = $webform->getHandlers();

        # Check if the handler already exists in this webform
        foreach ($handlers as $handle) {
          if ($handle instanceof CookiesWebformHandler) {
            $webform->updateWebformHandler($handler);
            return;
          }
        }

        # Add webform handler which triggers Webform::save().
        $webform->addWebformHandler($handler);
      }
    }


  }

  # Returns an array of all webform id's
  private function get_all_webform_ids() {
    $connection = \Drupal::service('database');
    $sql = "SELECT webform_id FROM webform";
    $result = $connection->query($sql);
    
    
    if ($result) {
      return $result->fetchAllKeyed(0,0);
    }
  }
}


