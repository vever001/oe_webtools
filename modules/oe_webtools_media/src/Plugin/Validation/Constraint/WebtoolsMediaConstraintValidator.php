<?php

declare(strict_types = 1);

namespace Drupal\oe_webtools_media\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drupal\Component\Serialization\Json;
use Drupal\oe_webtools_media\Plugin\media\Source\WebtoolsInterface;

/**
 * Validates the webtools media constraint.
 */
class WebtoolsMediaConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\media\MediaInterface $media */
    $media = $value->getEntity();

    /** @var \Drupal\oe_webtools_media\Plugin\media\Source\WebtoolsInterface $source */
    $source = $media->getSource();

    if (!($source instanceof WebtoolsInterface)) {
      throw new \LogicException('Media source must implement ' . WebtoolsInterface::class);
    }

    // Get widget types.
    $widget_types = $source->getWidgetTypes();

    // Decode the snippet.
    $snippet = Json::decode($source->getSourceFieldValue($media));

    // Add violation in case incorrect services.
    if (!isset($snippet['service']) || $snippet['service'] !== $widget_types[$constraint->widgetType]['service']) {
      $this->context->addViolation($constraint->message, ['%widget_type_name' => $widget_types[$constraint->widgetType]['name']]);
    }
  }

}
