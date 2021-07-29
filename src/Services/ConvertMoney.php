<?php

namespace ShopEngine\Nova\Services;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class ConvertMoney
{
    static private $decimalMoneyFormatter = null;

    static function format($value)
    {
        if ($value INSTANCEOF Money) {
            $money = $value;
        }
        else {
            $money = self::parse($value);
        }

        return formatPrice(self::formatDecimal($money), '', $money->getCurrency()->getCode());
    }

    static function parse($value)
    {
        $currencies = new ISOCurrencies();

        if (is_float($value)) {
            $value = (string)$value;
            $moneyParser = new DecimalMoneyParser($currencies);
            $value = $moneyParser->parse($value, self::currency());
            $value = $value->getAmount();
        }

        return new Money($value, new Currency(self::currency()));
    }

    static function formatDecimal($value)
    {
        if ($value INSTANCEOF Money) {
            $money = $value;
        }
        else {
            $money = self::parse($value);
        }

        return self::getDecimalMoneyFormatter()->format($money);
    }

    static private function getDecimalMoneyFormatter()
    {
        if (self::$decimalMoneyFormatter === null) {
            self::$decimalMoneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
        }

        return self::$decimalMoneyFormatter;
    }

    static function fromStringToFloat($amount)
    {
        $m = self::parse($amount);
        return self::toRealFloat($m);
    }

    static function toRealFloat(Money $money)
    {
        return floatval(self::toFloat($money));
    }

    static function toFloat(Money $money)
    {
        return self::getDecimalMoneyFormatter()->format($money);
    }

    static function toRaw(Money $money): array
    {
        return ['amount' => $money->getAmount(), 'currency' => $money->getCurrency()->getCode()];
    }

    static function currency()
    {
        return config('rh.currency');
    }
}
