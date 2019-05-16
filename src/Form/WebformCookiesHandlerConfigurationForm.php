<?php

namespace Drupal\webform_cookies_handler\Form;
use Drupal\webform\Plugin\WebformHandler\RemotePostWebformHandler;
use Drupal\webform_cookies_handler\Plugin\WebformHandler\CookiesWebformHandler;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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

    $default_new_submissions_cookies = $config->get('default_new_submissions_cookies');
    if (is_string($default_new_submissions_cookies)) { 
      $default_new_submissions_cookies =  $default_new_submissions_cookies;
    } else { 
      $default_new_submissions_cookies = 'Enter comma separated list here...';
    }

    $default_on_new_submissions = $config->get('default_on_new_submissions');
    if (is_numeric($default_on_new_submissions)) { 
      $default_on_new_submissions =  $default_on_new_submissions; 
    } else { 
      $default_on_new_submissions = 0;
    }

    # Defaults for URL forwarding settings
    $default_url_checkbox = $config->get('default_url_checkbox');
    if (is_numeric($default_url_checkbox)) { 
      $default_url_checkbox =  $default_url_checkbox;
    } else { 
      $default_url_checkbox = 0;
    }

    $default_forwarding_url  = $config->get('default_forwarding_url');
    if (is_string($default_forwarding_url)) { 
      $default_forwarding_url  =  $default_forwarding_url ;
    } else { 
      $default_forwarding_url  = 'Enter fully qualified url here: https://example.com/post';
    }

    $default_on_new_submissions_forwarding_checkbox = $config->get('default_on_new_submissions_forwarding_checkbox');
    if (is_numeric($default_on_new_submissions_forwarding_checkbox)) { 
      $default_on_new_submissions_forwarding_checkbox =  $default_on_new_submissions_forwarding_checkbox;
    } else { 
      $default_on_new_submissions_forwarding_checkbox = 0;
    }

    $default_on_new_submissions_forwarding_url  = $config->get('default_on_new_submissions_forwarding_url');
    if (is_string($default_on_new_submissions_forwarding_url)) { 
      $default_on_new_submissions_forwarding_url  =  $default_on_new_submissions_forwarding_url ;
    } else { 
      $default_on_new_submissions_forwarding_url  = 'Enter fully qualified url here: https://example.com/post';
    }

    $form['webform_cookies_handler_admin'] = array(
      '#type' => 'fieldset',
      '#title' => t('Advanced Webform Cookies Settings'),
      '#description' => t('Site wide webform cookie settings'),
    );

    $form['webform_cookies_handler_admin']['default_cookies_fieldset']  = array(
      '#type' => 'fieldset',
      '#title' => t('Apply Webform Cookies Handler to Existing Webforms.'),
      '#description' => t('This setting will retroactively apply the handler for existing webforms. However, it will not overwrite Webforms already configured with Webform Cookies Handler.'),
    );
      
    $form['webform_cookies_handler_admin']['default_cookies_fieldset']['apply_to_all_webforms'] = array(
      '#type' => 'checkbox',
      '#default_value' => $default_all_webforms,
      '#required' => FALSE,
    );

    $form['webform_cookies_handler_admin']['default_cookies_fieldset']['default_cookies'] = array(
      '#type' => 'textfield', 
      '#title' => t('Default Cookies'), 
      //'#description' => t('Apply Webforms Cookies Handler to all Webforms retroactively with these cookies set as default.'),
      '#default_value' => $default_cookies,
      '#size' => 60, 
      '#maxlength' => 128, 
      '#required' => FALSE,
    );

    $form['webform_cookies_handler_admin']['default_on_new_submissions_fieldset']  = array(
      '#type' => 'fieldset',
      '#title' => t('Apply Default Webform Cookies Handler for all new Webforms.'),
      '#description' => t('This setting will pro-actively apply the handler to all future webforms. However, these webforms can always be reconfigured and customized for the future.'),
    );

    $form['webform_cookies_handler_admin']['default_on_new_submissions_fieldset']['default_on_new_submissions'] = array(
      '#type' => 'checkbox',  
      '#default_value' => $default_on_new_submissions,
      '#required' => FALSE,
    );

    $form['webform_cookies_handler_admin']['default_on_new_submissions_fieldset']['default_new_submissions_cookies'] = array(
      '#type' => 'textfield', 
      '#title' => t('Default Cookies'), 
      '#default_value' => $default_new_submissions_cookies,
      '#size' => 60, 
      '#maxlength' => 128, 
      '#required' => FALSE,
      
    );


    # Additional settings for adding default automatic forwarding to all webforms
    $form['webform_cookies_handler_additional_admin_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t('Additional Webform default forwarding Settings'),
      '#description' => t('Settings for applying URL forwarding to webforns'),
    );

    $form['webform_cookies_handler_additional_admin_settings']['default_url_checkbox_fieldset']  = array(
      '#type' => 'fieldset',
      '#title' => t('Retroactively Apply Default URL Forwarding for Existing Webforms'),
      '#description' => t('Retroactively apply default URL forwarding for existing webforms. Will not overwrite webforms already configured with the URL forwarding handler.'),
    );

    $form['webform_cookies_handler_additional_admin_settings']['default_url_checkbox_fieldset']['default_url_checkbox'] = array(
      '#type' => 'checkbox',
      '#default_value' => $default_url_checkbox,
      '#required' => FALSE,
    );

    $form['webform_cookies_handler_additional_admin_settings']['default_url_checkbox_fieldset']['default_forwarding_url'] = array(
      '#type' => 'textfield', 
      '#title' => t('Fowarding URL'), 
      '#default_value' => $default_forwarding_url,
      '#size' => 60, 
      '#maxlength' => 128, 
      '#required' => FALSE,
    );

    $form['webform_cookies_handler_additional_admin_settings']['default_on_new_submissions_forwarding_checkbox_fieldset']  = array(
      '#type' => 'fieldset',
      '#title' => t('Add default forwarding for all new Webforms created.'),
      '#description' => t('Apply default URL forwarding for all newly created webforms. However, these webforms can always be reconfigured and customized for the future.'),
    );

    $form['webform_cookies_handler_additional_admin_settings']['default_on_new_submissions_forwarding_checkbox_fieldset']['default_on_new_submissions_forwarding_checkbox'] = array(
      '#type' => 'checkbox',
      '#default_value' => $default_on_new_submissions_forwarding_checkbox,
      '#required' => FALSE,
    );

    $form['webform_cookies_handler_additional_admin_settings']['default_on_new_submissions_forwarding_checkbox_fieldset']['default_on_new_submissions_forwarding_url'] = array(
      '#type' => 'textfield', 
      '#title' => t('Forwarding URL'), 
      '#default_value' => $default_on_new_submissions_forwarding_url,
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
    # Config form values
    $values = $form_state->getValues();

    # Previously saved values
    $config = $this->config('webform_cookies_handler.settings');
    
    # Set the user specified site-wide cookies configuration
    $this->config('webform_cookies_handler.settings')
      ->set('apply_to_all_webforms', $values['apply_to_all_webforms'])
      ->save();

    # Set user specified default Cookies
    $this->config('webform_cookies_handler.settings')
    ->set('default_cookies', $values['default_cookies'])
    ->save();

    # Set the user specified site-wide cookies configuration
    $this->config('webform_cookies_handler.settings')
      ->set('default_on_new_submissions', $values['default_on_new_submissions'])
      ->save();

    # Set user specified default Cookies
    $this->config('webform_cookies_handler.settings')
      ->set('default_new_submissions_cookies', $values['default_new_submissions_cookies'])
      ->save();


    # Set URL forwarding and forwarding URL 
    $this->config('webform_cookies_handler.settings')
      ->set('default_on_new_submissions_forwarding_checkbox', $values['default_on_new_submissions_forwarding_checkbox'])
      ->save();

    $this->config('webform_cookies_handler.settings')
      ->set('default_on_new_submissions_forwarding_url', $values['default_on_new_submissions_forwarding_url'])
      ->save();
   

    # If the user updated site wide webform cookie settings
    if ($values['apply_to_all_webforms']) {
      $this->attach_webform_cookies_handler_to_all_webforms($values['default_cookies']);
    }

    # If default settings has been unchecked or the cookies have changed
    if ($values['apply_to_all_webforms'] == 0) {
      $this->update_defaulted_webform_cookies();
    }

    if ($values['default_url_checkbox']) {
      $this->attach_webform_url_forwarding_handler_to_all_webforms($values['default_forwarding_url'], $values['default_forwarding_url'], $values['default_forwarding_url']);
    }

    parent::submitForm($form, $form_state);
  }

  # Add Webform_Cookies handler to all webforms
  private function attach_webform_cookies_handler_to_all_webforms($default_cookies) {
    
    # retrieve all unedited webforms
    $webforms = $this->get_all_non_edited_webforms();

    $unmodified_webforms_cookies = $webforms[0];
    
    # retrieve all edited webforms form our running list of webform with default cookies attached
    $previouly_defaulted_webforms = $this->config('webform_cookies_handler.settings')->get('webforms_with_default_cookies');


    if (!isset($previouly_defaulted_webforms)) {
      
      # reset our running list of webforms with default cookies
      $this->config('webform_cookies_handler.settings')->set('webforms_with_default_cookies', $unmodified_webforms_cookies)->save();
    
    } else {
      
      # merge the two lists together without duplicates
      $webfoms = array_unique(array_merge($unmodified_webforms_cookies, $previouly_defaulted_webforms), SORT_REGULAR);
      
      # reset our running list of webforms with default cookies
      $this->config('webform_cookies_handler.settings')->set('webforms_with_default_cookies', $webfoms)->save();

    }

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
    foreach ($webfoms as $id) {
      
      $webform = \Drupal::entityTypeManager()->getStorage('webform')->load($id);

      if ($webform) {
        // Must set original id so that the webform can be resaved.
        $webform->setOriginalId($webform->id());

        $handlers = $webform->getHandlers();
        $already_there = FALSE;
        
        # Check if the handler already exists in this webform
        foreach ($handlers as $handle) {
          if ($handle instanceof CookiesWebformHandler) {
            $webform->updateWebformHandler($handler);
            $already_there = TRUE;
            break;
          }
        }

        # Add webform handler which triggers Webform::save().
        if ($already_there) {
          continue;
        }
        
        $webform->addWebformHandler($handler);
      }
    }
  }


  private function attach_webform_url_forwarding_handler_to_all_webforms($completed_url, $updated_url, $deleted_url) {

    # retrieve all unedited webforms
    $webforms = $this->get_all_non_edited_webforms();

    $unmodified_webforms_forwarding = $webforms[1];
    
    # retrieve all edited webforms form our running list of webform with default cookies attached
    $previouly_defaulted_webforms = $this->config('webform_cookies_handler.settings')->get('webforms_with_default_forwarding');


    if (!isset($previouly_defaulted_webforms)) {
      
      # reset our running list of webforms with default cookies
      $this->config('webform_cookies_handler.settings')->set('webforms_with_default_forwarding', $unmodified_webforms_forwarding)->save();
    
    } else {
      
      # merge the two lists together without duplicates
      $webforms = array_unique(array_merge($unmodified_webforms_forwarding, $previouly_defaulted_webforms), SORT_REGULAR);
      
      # reset our running list of webforms with default cookies
      $this->config('webform_cookies_handler.settings')->set('webforms_with_default_forwarding', $webforms)->save();

    }


    # Set up our Webform Cookies Handler Config
    $handler_configuration = [
      'id' => 'remote_post',
      'label' => 'Remote Post',
      'handler_id' => 'remote_post',
      'status' => 1,
      'weight' => 0,
      'settings' => array(
        'completed_url' => $completed_url,
        'updated_url' => $updated_url,
        'deleted_url' => $deleted_url,
      ),
      
    ];

    # Setup the handler instance
    $handler_manager = \Drupal::service('plugin.manager.webform.handler');
    $handler = $handler_manager->createInstance('remote_post', $handler_configuration);



    # Now going to open each webform one-by-one and add cookies handler with default config
    foreach ($webforms as $id) {
      
      $webform = \Drupal::entityTypeManager()->getStorage('webform')->load($id);

      if ($webform) {
        // Must set original id so that the webform can be resaved.
        $webform->setOriginalId($webform->id());

        $handlers = $webform->getHandlers();
        $already_there = FALSE;
        
        # Check if the handler already exists in this webform
        foreach ($handlers as $handle) {
          if ($handle instanceof RemotePostWebformHandler) {
            $webform->updateWebformHandler($handler);
            $already_there = TRUE;
            break;
          }
        }

        # Add webform handler which triggers Webform::save().
        if ($already_there) {
          continue;
        }
        
        $webform->addWebformHandler($handler);
      }
    }
  }

  # Only updates those webforms that have been previously edited by the site-wide default cookies settings.
  private function update_defaulted_webform_cookies() {

    $previouly_defaulted_webforms = $this->config('webform_cookies_handler.settings')->get('webforms_with_default_cookies');


    if (!isset($previouly_defaulted_webforms)) {
      
      # There is nothing to do here
      return;
    
    } else {
      foreach ($previouly_defaulted_webforms as $id) {
        $webform = \Drupal::entityTypeManager()->getStorage('webform')->load($id);

        if ($webform) {
          
          // Must set original id so that the webform can be resaved.
          $webform->setOriginalId($webform->id());

          $handlers = $webform->getHandlers();
          
          # Remove handler
          foreach ($handlers as $handle) {
            if ($handle instanceof CookiesWebformHandler) {
              $webform->deleteWebformHandler($handle);
              break;
            }
          }
        }
      }
    }
  }

  private function update_defaulted_webform_forwarding_url(){
    $this->config('webform_cookies_handler.settings');

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

  private function get_all_non_edited_webforms() {
    # get list of all unedited webforms
    $ids = $this->get_all_webform_ids();
    
    # List of webforms that havnt been modified by default cookies or forwarding
    $unmodified_webforms_cookies = [];
    $unmodified_webforms_forwarding = [];


    # Now going to open each webform one-by-one and add cookies handler with default config
    foreach ($ids as $index => $id) {
      $webform = \Drupal::entityTypeManager()->getStorage('webform')->load($id);

      if ($webform) {        
    
        // Must set original id so that the webform can be resaved.
        $webform->setOriginalId($webform->id());


        $handlers = $webform->getHandlers();


        $not_already_there_cookies = TRUE;
        $not_already_there_forwarding = TRUE;
        
        # Check if either of the handler already exists in this webform
        foreach ($handlers as $handle) {
          if ($handle instanceof CookiesWebformHandler) {
            $not_already_there_cookies = FALSE;
            break;
          }
        }
        
        if ($not_already_there_cookies) {
          $unmodified_webforms_cookies[] = $id;
        }
        
        
        foreach ($handlers as $handle) {
          if ($handle instanceof RemotePostWebformHandler) {
            $not_already_there_forwarding = FALSE;
            break;
          }
        }

        if ($not_already_there_forwarding) {
          $unmodified_webforms_forwarding[] = $id;
        }
      }
    }
  
    return [$unmodified_webforms_cookies, $unmodified_webforms_forwarding];
  }
}


