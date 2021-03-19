<template>
    <panel-item :field="field">
        <template slot="value">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Artikel</th>
                    <th class="text-left">SKU</th>
                    <th class="text-left">Anzahl</th>
                    <th class="text-right">Einzelpreis</th>
                    <th class="text-right">Preis</th>
                    <th class="text-right">Steuern</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="article in field.value">
                    <td>{{article.name}}</td>
                    <td>{{article.sku}}</td>
                    <td>{{article.quantity}}</td>
                    <td class="text-right">{{formatMoney(article.price)}}</td>
                    <td class="text-right">{{articleTotalPrice(article)}}</td>
                    <td class="text-right">{{article.tax}}%</td>
                </tr>
                </tbody>
            </table>
        </template>
    </panel-item>
</template>

<script>
    import helper from '../helper'

    export default {
        props: ['field'],
        methods: {
            articleTotalPrice(article) {
                const totalPrice = {...article.price}
                totalPrice.amount = totalPrice.amount * article.quantity
                return this.formatMoney(totalPrice)
            },
            formatMoney(money) {
                return helper.formatMoney(money)
            }
        }
    }
</script>

<style scoped>
    tbody tr:last-child td {
        border-bottom: none;
    }
</style>
