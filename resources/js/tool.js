Nova.booting((Vue, router, store) => {
    // alias for resources/:resourceName

    router.addRoutes([{
        path: '/shopengine/:resourceName',
        component: require('./components/List'),
    }])

    // custom fields
    Vue.component('detail-shippingcostoptions', require('./fields/DetailShippingCostOptions'))
    Vue.component('detail-shippingcostvalidations', require('./fields/DetailShippingCostValidaton'))

    Vue.component('detail-purchase-address', require('./fields/DetailAddress'))
    Vue.component('detail-purchase-articles', require('./fields/DetailPurchaseArticles'))
    Vue.component('detail-purchase-origin-status', require('./fields/DetailPurchaseOriginStatus'))
    Vue.component('detail-purchase-codes', require('./fields/DetailCodes'))

    Vue.component('index-money', require('./fields/IndexMoney'))
    Vue.component('detail-money', require('./fields/DetailMoney'))
    Vue.component('form-money', require('./fields/FormMoney'))

    Vue.component('detail-codepool-codes', require('./fields/CodepoolCodes'))
    Vue.component('detail-codepool-link', require('./fields/DetailCodepoolLink'))

    //Vue.component('detail-codepool-actions', require('./fields/CodepoolActions'))

    Vue.component('detail-code-validation', require('./fields/DetailCodeValidation'))
    Vue.component('form-code-validation', require('./fields/FormCodeValidation'))

    Vue.component('form-shop-engine-model', require('./fields/FormShopEngineModel'))
})
