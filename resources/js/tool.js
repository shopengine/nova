import DetailShippingCostOptions from './fields/DetailShippingCostOptions'
import DetailShippingCostValidaton from './fields/DetailShippingCostValidaton'
import DetailAddress from './fields/DetailAddress'
import DetailPurchaseArticles from './fields/DetailPurchaseArticles'
import DetailCodes from './fields/DetailCodes'
import IndexMoney from './fields/IndexMoney'
import DetailMoney from './fields/DetailMoney'
import FormMoney from './fields/FormMoney'
import CodepoolCodes from './fields/CodepoolCodes'
import DetailCodepoolLink from './fields/DetailCodepoolLink'
import DetailCodeValidation from './fields/DetailCodeValidation'
import FormCodeValidation from './fields/FormCodeValidation'
import FormShopEngineModel from './fields/FormShopEngineModel'
import List from './components/List'
import TagsPeriodsIndex from './components/settings/newsletter-provider/klicktipps/tags-periods/Index.vue'
import tagPeriodShow from './components/settings/newsletter-provider/klicktipps/tags-periods/Show.vue'
import tagPeriodEdit from './components/settings/newsletter-provider/klicktipps/tags-periods/Edit.vue'
import tagPeriodCreate from './components/settings/newsletter-provider/klicktipps/tags-periods/Create.vue'

Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            path: '/novashopengine/:resourceName',
            component: List,
        },
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
    ])

    Vue.component('detail-shippingcostoptions', DetailShippingCostOptions)
    Vue.component('detail-shippingcostvalidations', DetailShippingCostValidaton)
    Vue.component('detail-purchase-address', DetailAddress)
    Vue.component('detail-purchase-articles', DetailPurchaseArticles)
    Vue.component('detail-purchase-codes', DetailCodes)
    Vue.component('index-money', IndexMoney)
    Vue.component('detail-money', DetailMoney)
    Vue.component('form-money', FormMoney)
    Vue.component('detail-codepool-codes', CodepoolCodes)
    Vue.component('detail-codepool-link', DetailCodepoolLink)
    Vue.component('detail-code-validation', DetailCodeValidation)
    Vue.component('form-code-validation', FormCodeValidation)
    Vue.component('form-shop-engine-model', FormShopEngineModel)
})
