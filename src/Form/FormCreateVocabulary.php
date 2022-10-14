<?php

namespace Drupal\glossarytooltip\Form;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

require_once __DIR__ . '/../../module_helpers.php';

/**
 * @file
 * Formulario Inscripcion Form
 */
class FormCreateVocabulary extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'glossarytooltip_create_vocabulary_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $id = (int) ($_GET['id'] ?? 0);
    $word = '';
    $definition = '';

    if (!empty($id)) {
      $db = Drupal::database();
      $query = $db->select(TABLEPRINCNAME, 'gl');
      $query->fields('gl', ['word', 'definition']);
      $query->condition('pid', $id, '=');
      $glossary = $query->execute()->fetch();

      if ($glossary) {
        $word = $glossary->word;
        $definition = $glossary->definition;
      }
    }

    $form['pid'] = [
      '#type' => 'hidden',
      '#value' => $id
    ];

    $form['word'] = [
      '#title' => $this->t('Word'),
      '#type' => 'textfield',
      '#attributes' => [
        'placeholder' => $this->t('Word'),
      ],
      '#required' => TRUE,
      '#value' => $word
    ];

    $form['definition'] = [
      '#title' => $this->t('Definition'),
      '#type' => 'textarea',
      '#attributes' => [
        'placeholder' => $this->t('Write the definition'),
      ],
      '#required' => TRUE,
      '#value' => $definition
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#buttom_type' => 'primary'
    ];

    return $form;
  }

  private function hasVocabulary(string $word): int
  {
    $db = Drupal::database();
    $query = $db->select(TABLEPRINCNAME, 'gl');
    $query->fields('gl', ['pid']);
    $query->condition('word', $word, '=');
    $glossary = $query->execute()->fetch();

    if ($glossary) {
      return (int) $glossary->pid;
    } else {
      return false;
    }
  }

  private function addVocabulary(string $word, string $definition): int
  {
    $timestamp = (new \Datetime('now'))->format(MYSQL_DATETIMEFORMAT);
    $db = Drupal::database();

    $word = trim($word);
    $definition = trim($definition);

    if (empty($word) || empty($definition)) {
      return false;
    }

    $value = [
      'word' => $word,
      'definition' => $definition,
      'dt_creation' => $timestamp
    ];
    $query = $db->insert(TABLEPRINCNAME)->fields(array_keys($value));
    $query->values($value);
    $query->execute();

    return true;
  }

  private function updateVocabulary(int $pid = null, string $word, string $definition): int
  {
    $timestamp = (new \Datetime('now'))->format(MYSQL_DATETIMEFORMAT);
    $db = Drupal::database();

    $word = trim($word);
    $definition = trim($definition);

    if (empty($word) || empty($definition)) {
      return false;
    }

    $fields = [
      'definition' => $definition,
      'dt_update' => $timestamp
    ];

    if (empty($pid)) {
      $fields['word'] = $word;
    }

    $query = $db->update(TABLEPRINCNAME);
    $query->fields($fields);
    $query->condition('pid', $pid, '=');
    $query->execute();

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $idform = filter_input(INPUT_POST, 'pid', FILTER_SANITIZE_NUMBER_INT) ?? '';
    $word = filter_input(INPUT_POST, 'word', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
    $definition = filter_input(INPUT_POST, 'definition', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
    $id = $this->hasVocabulary($word);

    if (!empty($idform)) {
      if ($id == $idform) {
        $this->updateVocabulary($id, $word, $definition);
      }
    }

    if ($id) {
      $this->updateVocabulary($id, $word, $definition);
      return $this->messenger()->addStatus($this->t(
        'The word @word has been updated',
        ['@word' => $word]
      ));
    } else {
      $this->addVocabulary($word, $definition);
      return $this->messenger()->addStatus($this->t(
        'The word @word has been added',
        ['@word' => $word]
      ));
    }
  }

}