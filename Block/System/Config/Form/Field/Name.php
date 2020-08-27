<?php

namespace Vexpro\GerminiConfig\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Name extends \Magento\Config\Block\System\Config\Form\Field
{
  protected function _getElementHtml(AbstractElement $element)
  {
    $element->setDisabled('disabled');
    return $element->getElementHtml();
  }
}
