<?php

define('TABLEPRINCNAME', 'glossary');
define('MYSQL_DATETIMEFORMAT', 'Y-n-d H:i:s');

/**
 * @file
 * Implement hook_form_alter().
 */
function glossarytooltip_form_alter (&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id): void
{
  if ($form_id == 'comment_comment_form') {
    $form['actions']['submit']['#value'] = t('Publish this comment');
    $form['actions']['preview']['#value'] = t('Preview');
  }
}

/**
 * @file
 * Implement hook_theme().
 */
function glossarytooltip_theme ($existing, $type, $theme, $path): array
{
  return [
    'glossarytooltip-list' => [
      'variables' => [
        'items' => [],
        'title' => null
      ]
    ],
    'glossarytooltip-form' => [
      'variables' => [
        'items' => [],
        'form' => null
      ]
    ]
  ];
}
