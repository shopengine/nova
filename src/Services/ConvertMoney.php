<?php

namespace ShopEngine\Nova\Services;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class ConvertMoney
{
    private static $decimalMoneyFormatter = null;

    public static function format($value)
    {
        if ($value instanceof Money) {
            $money = $value;
        } else {
            $money = self::parse($value);
        }

        return formatPrice(self::formatDecimal($money), '', $money->getCurrency()->getCode());
    }

    public static function parse($value)
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

    public static function formatDecimal($value)
    {
        if ($value instanceof Money) {
            $money = $value;
        } else {
            $money = self::parse($value);
        }

        return self::getDecimalMoneyFormatter()->format($money);
    }

    private static function getDecimalMoneyFormatter()
    {
        if (self::$decimalMoneyFormatter === null) {
            self::$decimalMoneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
        }

        return self::$decimalMoneyFormatter;
    }

    public static function fromStringToFloat($amount)
    {
        $m = self::parse($amount);
        return self::toRealFloat($m);
    }

    public static function toRealFloat(Money $money)
    {
        return floatval(self::toFloat($money));
    }

    public static function toFloat(Money $money)
    {
        return self::getDecimalMoneyFormatter()->format($money);
    }

    public static function toRaw(Money $money): array
    {
        return ['amount' => $money->getAmount(), 'currency' => $money->getCurrency()->getCode()];
    }

    public static function currency()
    {
        return config('rh.currency');
    }
}
