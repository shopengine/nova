Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            path: '/novashopengine/lastCodes',
            component: require('./components/ListLegacy'),
        },
        {
            path: '/novashopengine/codepool-groups',
            component: require('./components/ListLegacy'),
        },

        {
            path: '/novashopengine/:resourceName',
            component: require('./components/List'),
        },
        {
            path: '/novashopengine/:resourceName/new',
            component: require('./components/New'),
        },
        {
            path: '/novashopengine/:resourceName/:resourceId/edit',
            component: require('./components/Update'),
        },
        {
            path: '/novashopengine/:resourceName/:resourceId/stats',
            component: require('./components/StatsByTime'),
        },
        {
            path: '/novashopengine/:resourceName/:resourceId',
            component: require('./components/Detail'),
        }
    ])

    Vue.component('detail-shippingcostoptions', require('./fields/DetailShippingCostOptions'))
    Vue.component('detail-shippingcostvalidations', require('./fields/DetailShippingCostValidaton'))
    Vue.component('detail-purchase-address', require('./fields/DetailAddress'))
    Vue.component('detail-purchase-articles', require('./fields/DetailPurchaseArticles'))
    Vue.component('detail-purchase-codes', require('./fields/DetailCodes'))
    Vue.component('detail-codepool-codes', require('./fields/CodepoolCodes'))
    Vue.component('detail-codepool-group-codepools', require('./fields/CodepoolGroupCodepools'))
    Vue.component('detail-codepool-link', require('./fields/DetailCodepoolLink'))

    Vue.component('detail-codepool-statistics', require('./fields/CodepoolStatistics'))
    Vue.component('detail-codepool-actions', require('./fields/CodepoolActions'))
    Vue.component('detail-purchase-manual-jtl', require('./fields/DetailPurchaseManualJTL'))

    Vue.component('index-money', require('./fields/IndexMoney'))
    Vue.component('detail-money', require('./fields/DetailMoney'))
    Vue.component('form-money', require('./fields/FormMoney'))

    Vue.component('index-code-statistics', require('./fields/IndexCodeStatistics'))
    Vue.component('detail-code-statistics', require('./fields/DetailCodeStatistics'))

    Vue.component('detail-code-validation', require('./fields/DetailCodeValidation'))
    Vue.component('form-code-validation', require('./fields/FormCodeValidation'))

    Vue.component('form-shop-engine-model', require('./fields/FormShopEngineModel'))
})
