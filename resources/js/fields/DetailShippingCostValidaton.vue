<template>
    <panel-item :field="field">
        <template slot="value">
            <table class="table" v-if="period.length > 0">
                <thead>
                <tr>
                    <th colspan="2">Von</th>
                    <th colspan="2">Bis</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="validation in period">
                    <td>{{ validation.fromWeekDay }}</td>
                    <td>{{ validation.fromTime }}</td>
                    <td>{{ validation.toWeekDay }}</td>
                    <td>{{ validation.toTime }}</td>
                </tr>
                </tbody>
            </table>

            <table class="table mt-4" v-if="paymentMethod.length > 0">
                <thead>
                <tr>
                    <th>Ausgeschlossene Zahlart</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="validation in paymentMethod">
                    <td>{{ validation.invalidPaymentMethod }}</td>
                </tr>
                </tbody>
            </table>

            <table class="table mt-4" v-if="postbox.length > 0">
                <thead>
                <tr>
                    <th>Vom versand zur Packstation ausgeschlossen</th>
                </tr>
                </thead>
            </table>

            <table class="table mt-4" v-if="article.length > 0">
                <thead>
                <tr>
                    <th>Ausgeschlossene Artikel</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="validation in article">
                    <td>{{ validation }}</td>
                </tr>
                </tbody>
            </table>


        </template>
    </panel-item>
</template>

<script>
export default {
    props: ['field'],
    computed: {
        period: function() {
            return this.field.value.filter((validation) => validation.type === 'period')
        },
        paymentMethod: function() {
            return this.field.value.filter((validation) => validation.type === 'payment_method')
        },
        postbox: function() {
            return this.field.value.filter((validation) => validation.type === 'postbox')
        },
        article: function() {
            return this.field.value.filter((validation) => validation.type === 'article')
        },
    },
}
</script>

<style scoped>

</style>
