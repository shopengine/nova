Nova.booting((Vue, router, store) => {

     // alias for resources/:resourceName

     router.addRoutes([
        {
            path: '/novashopengine/:resourceName',
            component: require('./components/List'),
        }
     ])

    Vue.component('detail-shippingcostoptions', require('./fields/DetailShippingCostOptions'))
    Vue.component('detail-shippingcostvalidations', require('./fields/DetailShippingCostValidaton'))

    Vue.component('detail-purchase-address', require('./fields/DetailAddress'))
    Vue.component('detail-purchase-articles', require('./fields/DetailPurchaseArticles'))
    Vue.component('detail-purchase-manual-jtl', require('./fields/DetailPurchaseManualJTL'))

    Vue.component('index-money', require('./fields/IndexMoney'))
    Vue.component('detail-money', require('./fields/DetailMoney'))
    Vue.component('form-money', require('./fields/FormMoney'))


    // ???
    Vue.component('form-shop-engine-model', require('./fields/FormShopEngineModel'))
})
