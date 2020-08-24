<?php

namespace Vexpro\GerminiConf\Model;

use Exception;
use Vexpro\GerminiConf\Api\CurrencyInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Currency implements CurrencyInterface
{
  /**
   * @var string
   */
  protected $senha;

  /**
   *  @var \Magento\Framework\App\Config\Storage\WriterInterface
   */
  protected $configWriter;

  /**
   *
   * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
   */
  public function __construct(
    \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
  ) {
    $this->configWriter = $configWriter;
    $this->senha = 'kFsrvHwmIMO5ldntjeTurBqYn1btKzbKY5PtZ8h6mdsToE15H6M54MQjyBuIomZx';
  }

  /**
   * changeCurrency
   *
   * @param string $programCurrencyName
   * @param string $programCurrencySymbol
   * @return string $response
   */

  public function changeCurrency($programCurrencyName, $programCurrencySymbol)
  {
    //Get Object Manager Instance
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    $cabeca = $objectManager->create('Magento\Framework\App\RequestInterface');
    $autorizacao = $cabeca->getHeader('Authorization');
    $autorizacao = explode(" ", $autorizacao);
    $autorizacao = $autorizacao[1];
    $status = 'success';

    if ($autorizacao != $this->senha) {
      throw new \Magento\Framework\Exception\AuthorizationException(
        __(\Magento\Framework\Exception\AuthorizationException::NOT_AUTHORIZED)
      );
    }

    try {
      $this->configWriter->save('acessos/general/programCurrencyName',  $programCurrencyName, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
    } catch (Exception $e) {
      return;
    }

    try {
      $this->configWriter->save('acessos/general/programCurrencySymbol',  $programCurrencySymbol, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
    } catch (Exception $e) {
      return;
    }

    return True;
  }
}
