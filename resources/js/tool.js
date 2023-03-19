import List from './components/List.vue';
import Klicktipp from './components/Klicktipp/Klicktipps.vue'
import Show from './components/Klicktipp/Show.vue'
import Edit from './components/Klicktipp/Edit.vue'
import Create from './components/Klicktipp/Create.vue'

Nova.booting((Vue, router, store) => {
    // alias for resources/:resourceName

    router.addRoutes([
        {
            name: 'klicktipps',
            path: '/novashopengine/klicktipps',
            component: Klicktipp
        },
        {
            name: 'klicktipps.create',
            path: '/novashopengine/klicktipps/create',
            component: Create
        },
        {
            name: 'klicktipps.show',
            path: '/novashopengine/klicktipps/:klicktipp',
            component: Show
        },
        {
            name: 'klicktipps.edit',
            path: '/novashopengine/klicktipps/:klicktipp/edit',
            component: Edit
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
