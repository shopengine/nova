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

Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            path: '/novashopengine/:resourceName',
            component: List,
        }
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
