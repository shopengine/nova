<template>
    <loading-view :loading="loading">
        <div class="mb-8">
            <div>
                <h1 class="mb-3 text-90 font-normal text-2xl">
                    <router-link :to="`/novashopengine/${resourceName}/${resourceId}`" class="no-underline text-primary font-bold dim router-link-active">←</router-link>
                    <span class="px-2 text-70">/</span>
                    Statistiken nach Zeit aufgeschlüsselt
                </h1>

                <div style="overflow-x: scroll">
                    <div class="card mb-6 py-3 px-6" style="width: calc(410px * 3 + 10px * 4 + 100px)">
                        <div class="relative remove-bottom-border">
                            <p>Der oberste Wert ist immer unvollständig, da es sich um den aktuellen Tag, Woche, Monat handelt</p>

                            <table class="w-1/3 table-bordered">
                                <thead>
                                <tr>
                                    <th>Tag</th>
                                    <th>Einlösungen</th>
                                    <th>Umsatz</th>
                                    <th>Neukunden</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in statsByDay" :key="item.DATE">
                                    <td>{{item.DATE}}</td>
                                    <td>{{item.count}}</td>
                                    <td>{{formatAmount(item.total)}}</td>
                                    <td>{{newCustomerRatio(item.count, item.newCustomer)}}%</td>
                                </tr>
                                </tbody>
                            </table>

                            <table class="w-1/3 table-bordered">
                                <thead>
                                <tr>
                                    <th>Woche</th>
                                    <th>Einlösungen</th>
                                    <th>Umsatz</th>
                                    <th>Neukunden</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in statsByWeek" :key="item.DATE">
                                    <td>{{item.DATE}}</td>
                                    <td>{{item.count}}</td>
                                    <td>{{formatAmount(item.total)}}</td>
                                    <td>{{newCustomerRatio(item.count, item.newCustomer)}}%</td>
                                </tr>
                                </tbody>
                            </table>

                            <table class="w-1/3 table-bordered">
                                <thead>
                                <tr>
                                    <th>Monat</th>
                                    <th>Einlösungen</th>
                                    <th>Umsatz</th>
                                    <th>Neukunden</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in statsByMonth" :key="item.DATE">
                                    <td>{{item.DATE}}</td>
                                    <td>{{item.count}}</td>
                                    <td>{{formatAmount(item.total)}}</td>
                                    <td>{{newCustomerRatio(item.count, item.newCustomer)}}%</td>
                                </tr>
                                </tbody>
                            </table>

                            <div style="clear: both"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </loading-view>
</template>

<script>
    import helper from '../helper'

    export default {
        data() {
            return {
                resourceName: '',
                resourceId: '',
                shop: 'de',

                loading: false,

                statsByDay: [],
                statsByWeek: [],
                statsByMonth: [],
            }
        },
        mounted() {
            this.resourceName = this.$route.params.resourceName
            this.resourceId = this.$route.params.resourceId
            this.shop = this.$route.params.shop
            this.load()
        },
        beforeRouteUpdate(to, from, next) {
            this.resourceName = to.params.resourceName
            this.resourceId = to.params.resourceId
            next()
        },
        methods: {
            load() {
                this.loading = true

                Nova.request().get(`/nova-vendor/novashopengine/${this.resourceName}/${this.resourceId}/stats-by-time`).then(response => {
                    if (response.headers['content-type'] !== 'application/json') {
                        console.error(response)
                        return
                    }

                    this.statsByDay = response.data.statsByDay
                    this.statsByWeek = response.data.statsByWeek
                    this.statsByMonth = response.data.statsByMonth

                    this.loading = false
                })
            },
            formatAmount(money) {
                return helper.formatAmount(money)
            },
            newCustomerRatio(orderCount, newCustomerOrderCount) {
                if (newCustomerOrderCount === 0) {
                    return 0
                }

                return Math.round((newCustomerOrderCount / orderCount) * 100)
            },
        },
    }
</script>

<style scoped>
    table {
        width: 400px !important;
        float: left !important;
        margin-right: 10px;
        font-family: monospace;
        text-align: right;
    }

    td, th {
        padding: 5px;
    }

    tbody > tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .table-bordered, .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > thead > tr > th {
        border: 1px solid #f4f4f4;
    }
</style>
