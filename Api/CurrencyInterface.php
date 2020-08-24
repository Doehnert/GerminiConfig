<?php

namespace Vexpro\GerminiConf\Api;

interface CurrencyInterface
{
  /**
   * changeCurrency
   *
   * @param string $programCurrencyName
   * @param string $programCurrencySymbol
   * @return string $response
   */
  public function changeCurrency($programCurrencyName, $programCurrencySymbol);
}
