<?php

namespace Drupal\glossarytooltip\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;

require_once __DIR__ . '/../../module_helpers.php';

/**
 * @file
 * Implement GlossaryTooltipController
 */
class GlossaryTooltipController extends ControllerBase
{
  private int $maxLengthDefinition = 100;

  public function glossaryFormPage(): array
  {
    $form = \Drupal::formBuilder()->getForm('\Drupal\glossarytooltip\Form\FormCreateVocabulary');

    return [
      '#theme' => 'glossarytooltip-form',
      '#title' => 'Glossary Form',
      '#form' => $form,
      '#cache' => [
        'max-age' => 0,
      ]
    ];
  }

  public function glossaryListPage(): array
  {
    $db = Drupal::database();
    $query = $db->select(TABLEPRINCNAME, 'gl');
    $query->fields('gl', ['pid', 'word', 'definition']);
    $glossary = $query->execute()->fetchAll();

    $items = [];
    foreach ($glossary as $item) {
      $items[] = [
        'pid' => $item->pid,
        'word' => $item->word,
        'definition' => $item->definition,
        'smallerdefinition' => substr($item->definition, 0, $this->maxLengthDefinition)
      ];
    }

    return [
      '#theme' => 'glossarytooltip-list',
      '#title' => 'Glossary List',
      '#items' => $items,
      '#qtitems' => count($items),
      '#cache' => [
        'max-age' => 0,
      ]
    ];
  }
}