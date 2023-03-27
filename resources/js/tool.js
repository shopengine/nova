import List from './components/List.vue';
import TagsPeriodsIndex from './components/settings/newsletter-provider/klicktipps/tags-periods/Index.vue'
import tagPeriodShow from './components/settings/newsletter-provider/klicktipps/tags-periods/Show.vue'
import tagPeriodEdit from './components/settings/newsletter-provider/klicktipps/tags-periods/Edit.vue'
import tagPeriodCreate from './components/settings/newsletter-provider/klicktipps/tags-periods/Create.vue'

Nova.booting((Vue, router, store) => {
    // alias for resources/:resourceName

    router.addRoutes([
        {
            name: 'settings.newsletter-provider.klicktipps.tags-periods.index',
            path: '/novashopengine/settings/newsletter-provider/klicktipp/tags-periods',
            component: TagsPeriodsIndex
        },
        {
            name: 'settings.newsletter-provider.klicktipps.tags-periods.create',
            path: '/novashopengine/settings/newsletter-provider/klicktipp/tags-periods/create',
            component: tagPeriodCreate
        },
        {
            name: 'settings.newsletter-provider.klicktipps.tags-periods.show',
            path: '/novashopengine/settings/newsletter-provider/klicktipp/tags-periods/:tag',
            component: tagPeriodShow
        },
        {
            name: 'settings.newsletter-provider.klicktipps.tags-periods.edit',
            path: '/novashopengine/settings/newsletter-provider/klicktipp/tags-periods/:tag/edit',
            component: tagPeriodEdit
        },
        {
            path: '/novashopengine/:resourceName',
            component: List
        },

    ])

    // custom fields
    Vue.component('detail-shippingcostoptions', require('./fields/DetailShippingCostOptions'))
    Vue.component('detail-shippingcostvalidations', require('./fields/DetailShippingCostValidaton'))

    Vue.component('detail-purchase-address', require('./fields/DetailAddress'))
    Vue.component('detail-purchase-articles', require('./fields/DetailPurchaseArticles'))
    Vue.component('detail-purchase-manual-jtl', require('./fields/DetailPurchaseManualJTL'))
    Vue.component('detail-purchase-codes', require('./fields/DetailCodes'))

    Vue.component('index-money', require('./fields/IndexMoney'))
    Vue.component('detail-money', require('./fields/DetailMoney'))
    Vue.component('form-money', require('./fields/FormMoney'))

    Vue.component('detail-codepool-codes', require('./fields/CodepoolCodes'))
    Vue.component('detail-codepool-link', require('./fields/DetailCodepoolLink'))

    //Vue.component('detail-codepool-actions', require('./fields/CodepoolActions'))

    Vue.component('detail-code-validation', require('./fields/DetailCodeValidation'))
    Vue.component('form-code-validation', require('./fields/FormCodeValidation'))


    // ???
    Vue.component('form-shop-engine-model', require('./fields/FormShopEngineModel'))
})
