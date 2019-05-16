<?php

namespace Drupal\webform_cookies_handler\Plugin\WebformHandler;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Render\Markup;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformInterface;
use Drupal\webform\WebformSubmissionConditionsValidatorInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\WebformTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Webform Cookies handler.
 *
 * @WebformHandler(
 *   id = "Webform Cookies",
 *   label = @Translation("Webform Cookies"),
 *   category = @Translation("Webform Cookies"),
 *   description = @Translation("Drupal 8 Webform Cookies Handler"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_IGNORED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class CookiesWebformHandler extends WebformHandlerBase {
 
  /**
   * The token manager.
   *
   * @var \Drupal\webform\WebformTokenManagerInterface
   */
  protected $tokenManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerChannelFactoryInterface $logger_factory, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, WebformSubmissionConditionsValidatorInterface $conditions_validator, WebformTokenManagerInterface $token_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger_factory, $config_factory, $entity_type_manager, $conditions_validator);
    $this->tokenManager = $token_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory'),
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('webform_submission.conditions_validator'),
      $container->get('webform.token_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'cookies' => 'Enter Comma Seperated list...',
      'string_matching' => FALSE,
      'tokens' => [],
    ];
  }



  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Message.
    $form['cookies_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Cookies settings'),
    ];
    $form['cookies_fieldset']['cookies'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cookies To be searched for and added to webform'),
      '#default_value' => $this->configuration['cookies'],
      '#required' => TRUE,
    ];

    // Advanced cookie settings
    $form['Advanced_Options'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced Options'),
    ];
    $form['Advanced_Options']['string_matching'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('String Matching on Cookies'),
      '#description' => $this->t("If checked, cookies whose stings names match closely will be added to webform. For example '_example123' entry will be marked same as '_example'."),
      '#return_value' => TRUE,
      '#default_value' => $this->configuration['string_matching'],
    ];

    return $this->setSettingsParents($form);
  }

  /**
   * Saves local handler's configurations
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['cookies'] = $form_state->getValue('cookies');
    $cookies = $form_state->getValue('cookies');
    $cookies = preg_replace('/\s+/', '', $cookies);
    $tokens = explode(',', $cookies);
    $this->configuration['tokens'] = $tokens;
    $this->configuration['string_matching'] = (bool) $form_state->getValue('string_matching');
  }


  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    $this->debug(__FUNCTION__, $update ? 'update' : 'insert');
    $cookies = $this->grab_cookies();
    $data = $webform_submission->getData();
    $data['cookies'] = $cookies;
    $webform_submission->setData($data);

    if ($this->isResultsEnabled()) {
      $webform_submission->resave();
    }
  }

  protected function grab_cookies() {
    $string_matching = $this->configuration['string_matching'];
    $field_data = array();
    $cookies = $this->configuration['tokens']; 
    foreach ($_COOKIE as $name => $value) {
      foreach($cookies as $cookie) {
        if (strcmp($name, $cookie) == 0) {
          $field_data[$name] = $value;
        }
      }
      if ($string_matching){
        ksm("I am working");
      }

    }

    return $field_data;
  }

   /**
   * Determine if saving of results is enabled.
   *
   * @return bool
   *   TRUE if saving of results is enabled.
   */
  protected function isResultsEnabled() {
    return ($this->getWebform()->getSetting('results_disabled') === FALSE);
  }

    /**
   * {@inheritdoc}
   */
  public function preSave(WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
    
  }


  /**
   * {@inheritdoc}
   */
  public function alterElements(array &$elements, WebformInterface $webform) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function overrideSettings(array &$settings, WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
    if ($value = $form_state->getValue('element')) {
      $form_state->setErrorByName('element', $this->t('The element must be empty. You entered %value.', ['%value' => $value]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function confirmForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $message = $this->configuration['message'];
    $message = $this->replaceTokens($message, $this->getWebformSubmission());
    $this->messenger()->addStatus(Markup::create(Xss::filter($message)), FALSE);
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function preCreate(array $values) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function postCreate(WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function postLoad(WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function preDelete(WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function postDelete(WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

 

  /**
   * {@inheritdoc}
   */
  public function preprocessConfirmation(array &$variables) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function createHandler() {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function updateHandler() {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteHandler() {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function createElement($key, array $element) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function updateElement($key, array $element, array $original_element) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteElement($key, array $element) {
    $this->debug(__FUNCTION__);
  }

  /**
   * Display the invoked plugin method to end user.
   *
   * @param string $method_name
   *   The invoked method name.
   * @param string $context1
   *   Additional parameter passed to the invoked method name.
   */
  protected function debug($method_name, $context1 = NULL) {
    if (!empty($this->configuration['debug'])) {
      $t_args = [
        '@id' => $this->getHandlerId(),
        '@class_name' => get_class($this),
        '@method_name' => $method_name,
        '@context1' => $context1,
      ];
      $this->messenger()->addWarning($this->t('Invoked @id: @class_name:@method_name @context1', $t_args), TRUE);
    }
  }

  

}
