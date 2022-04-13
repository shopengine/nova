<?php

namespace ShopEngine\Nova\Resources;

use Carbon\Carbon;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngine\Nova\Fields\Address;
use ShopEngine\Nova\Fields\PurchaseArticles;
use ShopEngine\Nova\Fields\PurchaseCodes;
use ShopEngine\Nova\Fields\PurchaseOriginStatus;
use ShopEngine\Nova\Filter\PurchaseOriginStatusFilter;
use ShopEngine\Nova\Filter\PurchaseStatusFilter;
use ShopEngine\Nova\Models\PurchaseModel;
use ShopEngine\Nova\Services\ConvertMoney;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Money\Money;
use SSB\Api\Model\Purchase as ApiPurchase;

class Purchase extends ShopEngineResource
{
    public static $title = 'orderId';
    public static $search = ['orderId'];

    public static $defaultSort = '-orderDate';
    public static $id = 'aggregateId';

    public static function getModel(): string
    {
        return PurchaseModel::class;
    }

    public static function label()
    {
        return __('shopengine.purchases');
    }

    public static function singularLabel()
    {
        return __('shopengine.purchase');
    }

    public function fields(\Illuminate\Http\Request $request)
    {
        return $this->appendShopEngineFields([
            Text::make('Bestellnummer', 'orderId')
                ->sortable(true),
            DateTime::make('Bestelldatum', function () {
                if ($this->orderDate) {
                    return sprintf(
                        '%s',
                        Carbon::parse($this->orderDate)
                            ->setTimezone('Europe/Berlin')
                            ->format('Y-m-d H:i:s')
                    );
                }

                return '';
            }),
            Text::make('Email', 'email'),
            Badge::make('Status', 'status')
                ->map([
                    ApiPurchase::STATUS__NEW => 'info',
                    ApiPurchase::STATUS_PLACED => 'info',
                    ApiPurchase::STATUS_PAYMENT_FAILED => 'warn',
                    ApiPurchase::STATUS_PAYMENT_PENDING => 'info',
                    ApiPurchase::STATUS_PAYMENT_DONE => 'success',
                    ApiPurchase::STATUS_SHIPPED => 'success',
                    ApiPurchase::STATUS_CANCELED => 'danger',
                ]),
            Badge::make('ERP Status', 'originStatus')
                ->map([
                    ApiPurchase::ORIGIN_STATUS_EMPTY => 'info',
                    ApiPurchase::ORIGIN_STATUS_READY_TO_IMPORT => 'info',
                    ApiPurchase::ORIGIN_STATUS_IMPORTED => 'success',
                    ApiPurchase::ORIGIN_STATUS_ERROR_IN_IMPORT => 'danger',
                    ApiPurchase::ORIGIN_STATUS_WAIT_FOR_MANUAL => 'danger',
                ]),
            PurchaseOriginStatus::make('ERP Import freigeben')
                ->onlyOnDetail(),
            Text::make('Zahlart', 'paymentMethod'),
            Text::make('Versandart', 'shippingMethod'),
            Address::make('Rechnungsadresse', 'billingAddress')
                ->hideFromIndex(),
            Address::make('Lieferadresse', 'shippingAddress')
                ->hideFromIndex(),
            PurchaseArticles::make('Warenkorb', 'purchaseArticles')
                ->hideFromIndex(),
            // @todo MoneyField?
            Number::make('Artikelsumme', 'subTotal')
                ->onlyOnDetail()
                ->displayUsing(function ($money) {
                    return $money instanceof Money
                        ? ConvertMoney::toRealFloat($money).' '.$money->getCurrency()
                        : '';
                }),
            Number::make('Versandkosten', 'shipping')
                ->onlyOnDetail()
                ->displayUsing(function ($money) {
                    return $money instanceof Money
                        ? ConvertMoney::toRealFloat($money).' '.$money->getCurrency()
                        : '';
                }),
            Number::make('Gesamtkosten', 'grandTotal')
                ->displayUsing(function ($money) {
                    return $money instanceof Money
                        ? ConvertMoney::toRealFloat($money).' '.$money->getCurrency()
                        : '';
                }),
            PurchaseCodes::make('Codes', 'codes')
                ->hideFromIndex(),
            KeyValue::make('Weitere Informationen', 'paymentInformation')
                ->onlyOnDetail()
        ]);
    }

    public function fieldsForIndex(NovaRequest $request)
    {
        return [
            Text::make('Bestellnummer', 'orderId'),
            Text::make('Email', 'email'),
            DateTime::make('Bestelldatum', function () {
                if ($this->orderDate) {
                    return sprintf(
                        '%s',
                        Carbon::parse($this->orderDate)
                            ->setTimezone('Europe/Berlin')
                            ->format('Y-m-d H:i:s')
                    );
                }

                return '';
            }),
            Number::make('Gesamtkosten', 'grandTotal')
                ->displayUsing(function ($money) {
                    return $money instanceof Money
                        ? ConvertMoney::toRealFloat($money).' '.$money->getCurrency()
                        : '';
                })->withMeta(['textAlign' => 'right']),
            Text::make('Zahlart', 'paymentMethod'),
            Text::make('Versandart', 'shippingMethod'),
            Badge::make('Status', 'status')
                ->map([
                    ApiPurchase::STATUS__NEW => 'info',
                    ApiPurchase::STATUS_PLACED => 'info',
                    ApiPurchase::STATUS_PAYMENT_FAILED => 'warn',
                    ApiPurchase::STATUS_PAYMENT_PENDING => 'info',
                    ApiPurchase::STATUS_PAYMENT_DONE => 'success',
                    ApiPurchase::STATUS_SHIPPED => 'success',
                    ApiPurchase::STATUS_CANCELED => 'danger',
                ]),
            Badge::make('ERP Status', 'originStatus')
                ->map([
                    ApiPurchase::ORIGIN_STATUS_EMPTY => 'info',
                    ApiPurchase::ORIGIN_STATUS_READY_TO_IMPORT => 'info',
                    ApiPurchase::ORIGIN_STATUS_IMPORTED => 'success',
                    ApiPurchase::ORIGIN_STATUS_ERROR_IN_IMPORT => 'danger',
                    ApiPurchase::ORIGIN_STATUS_WAIT_FOR_MANUAL => 'danger',
                ]),
        ];
    }

    public function filters(Request $request)
    {
        return [
            new PurchaseStatusFilter(),
            new PurchaseOriginStatusFilter(),
        ];
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }
}
