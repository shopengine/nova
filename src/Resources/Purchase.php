<?php namespace ShopEngine\Nova\Resources;

use ShopEngine\Nova\Fields\Address;
use ShopEngine\Nova\Fields\PurchaseArticles;
use ShopEngine\Nova\Fields\PurchaseCodes;
use ShopEngine\Nova\Fields\PurchaseManualJTL;
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

    public static function getModel() : string
    {
        return PurchaseModel::class;
    }

    public static function getShopEngineEndpoint(): string
    {
        return 'purchase';
    }

    public function fields(\Illuminate\Http\Request $request)
    {
        return [
            Text::make('Bestellnummer', 'orderId')
                ->sortable(true),
            DateTime::make('Bestelldatum', 'orderDate')
                ->format('Y-MM-DD HH:mm:ss')
                ->sortable(true),
            Text::make('Email', 'email'),
            Badge::make('Status', 'status')->map([
                ApiPurchase::STATUS__NEW => 'info',
                ApiPurchase::STATUS_PLACED => 'info',
                ApiPurchase::STATUS_PAYMENT_FAILED => 'warn',
                ApiPurchase::STATUS_PAYMENT_PENDING => 'info',
                ApiPurchase::STATUS_PAYMENT_DONE => 'success',
                ApiPurchase::STATUS_SHIPPED => 'success',
                ApiPurchase::STATUS_CANCELED => 'danger',
            ]),
            Badge::make('JTL Status', 'originStatus')->map([
                ApiPurchase::ORIGIN_STATUS_EMPTY => 'info',
                ApiPurchase::ORIGIN_STATUS_READY_TO_IMPORT => 'info',
                ApiPurchase::ORIGIN_STATUS_IMPORTED => 'success',
                ApiPurchase::ORIGIN_STATUS_ERROR_IN_IMPORT => 'danger',
                ApiPurchase::ORIGIN_STATUS_WAIT_FOR_MANUAL => 'danger',
            ]),

            PurchaseManualJTL::make('Manuellen import für JTL freigeben')
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
                        ? ConvertMoney::toRealFloat($money) . ' ' . $money->getCurrency()
                        : '';
                }),
            Number::make('Versandkosten', 'shipping')
                ->onlyOnDetail()
                ->displayUsing(function ($money) {
                    return $money instanceof Money
                        ? ConvertMoney::toRealFloat($money) . ' ' . $money->getCurrency()
                        : '';
                }),
            Number::make('Gesamtkosten', 'grandTotal')
                ->displayUsing(function ($money) {
                    return $money instanceof Money
                        ? ConvertMoney::toRealFloat($money) . ' ' . $money->getCurrency()
                        : '';
                }),
            PurchaseCodes::make('Codes', 'codes')
                ->hideFromIndex(),

            KeyValue::make('Weitere Informationen', 'paymentInformation')
                ->onlyOnDetail()
        ];
    }

    public function filters(Request $request)
    {
        return [
            new PurchaseStatusFilter()
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
