<template>
    <loading-view :loading="loading">
        <div class="py-4">
            <table style="width: 100%">
                <thead>
                <tr>
                    <th></th>
                    <th class="text-right" style="width:99px">Summe</th>
                    <th class="text-right" style="width:99px">Durschnitt</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="3"><b>Insgesamt</b> ({{overall.count}})</td>
                </tr>
                <tr class="border-b border-40">
                    <td class="pt-1">Umsatz</td>
                    <td class="pt-1 text-right">{{formatAmount(overall.grandTotal)}}</td>
                    <td class="pt-1 text-right">{{formatAmount(overall.grandTotalAverage)}}</td>
                </tr>
                <tr class="border-b border-40">
                    <td class="pt-1">Warenkorbwert</td>
                    <td class="pt-1 text-right">{{formatAmount(overall.subTotal)}}</td>
                    <td class="pt-1 text-right">{{formatAmount(overall.subTotalAverage)}}</td>
                </tr>
                <tr>
                    <td class="pt-1">Gewährter Discount (Ohne Geschenke)</td>
                    <td class="pt-1 text-right">{{formatAmount(overall.discountTotal)}}</td>
                    <td class="pt-1 text-right">{{formatAmount(overall.discountTotalAverage)}}</td>
                </tr>
                <tr>
                    <td class="pt-3" colspan="3"><b>Neukunden</b> ({{newCustomer.count}})</td>
                </tr>
                <tr>
                    <td class="pt-1">Umsatz</td>
                    <td class="pt-1 text-right">{{formatAmount(newCustomer.grandTotal)}}</td>
                    <td class="pt-1 text-right">{{formatAmount(newCustomer.grandTotalAverage)}}</td>
                </tr>
                <tr>
                    <td class="pt-3" colspan="3"><b>Weitere Bestellungen von Neukunden ({{secondCustomer.count}})</b></td>
                </tr>
                <tr>
                    <td class="pt-1">Umsatz</td>
                    <td class="pt-1 text-right">{{formatAmount(secondCustomer.grandTotal)}}</td>
                    <td class="pt-1 text-right">{{formatAmount(secondCustomer.grandTotalAverage)}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </loading-view>
</template>

<script>
    import helper from '../helper'

    export default {
        props: [
            'resourceName',
            'resourceId',
            'resource',
        ],
        data() {
            return {
                loading: false,
                usageCount: 0,
                overall: {},
                newCustomer: {},
                secondCustomer: {},
            }
        },
        computed: {},
        mounted() {
            this.load()
        },
        methods: {
            async load() {
                this.loading = true

                Nova.request().get(`/nova-vendor/novashopengine/${this.resourceName}/${this.resourceId}/stats`).then(response => {
                    if (response.headers['content-type'] !== 'application/json') {
                        console.error(response)
                        return
                    }

                    this.usageCount = response.data.usageCount
                    this.overall = response.data.overall
                    this.newCustomer = response.data.newCustomer
                    this.secondCustomer = response.data.secondCustomer

                    this.loading = false
                })
            },
            formatAmount(money) {
                return helper.formatAmount(money)
            },
        },
    }
</script>
