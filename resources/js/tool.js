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
import PeriodTagIndex from './components/shop/marketing-provider/klicktipp/period-tags/Index.vue'
import PeriodTagShow from './components/shop/marketing-provider/klicktipp/period-tags/Show.vue'
import PeriodTagEdit from './components/shop/marketing-provider/klicktipp/period-tags/Edit.vue'
import PeriodTagCreate from './components/shop/marketing-provider/klicktipp/period-tags/Create.vue'
import Toggle from './fields/Toggle.vue'

Nova.booting((Vue, router, store) => {

    /*
    Nova.inertia("ShopengineList", List.default);
    Nova.inertia("PeriodTagIndex", PeriodTagIndex.default);
    Nova.inertia("PeriodTagCreate", PeriodTagCreate.default);
    Nova.inertia("PeriodTagShow", PeriodTagShow.default);
    Nova.inertia("PeriodTagEdit", PeriodTagEdit.default);
    */

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
    Vue.component('index-toggle', Toggle)
})
