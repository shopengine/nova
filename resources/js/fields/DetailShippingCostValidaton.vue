<template>
    <div class="relative w-full">
        <div class="flex justify-between items-center mb-3">
            <h1 class="flex-no-shrink text-90 font-normal text-2xl">Validierungen</h1>
        </div>

        <table class="table bg-white w-full">
            <thead>
                <tr>
                    <th class="text-left">Typ</th>
                    <th class="text-left">Beschreibung</th>
                    <th class="text-left">Wert</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="period.length > 0">
                    <td>Zeit</td>
                    <td>Versandart ist nur innerhalb dieser gültig</td>
                    <td v-for="validation in period">
                        <div>{{ validation.fromWeekDay }}</div>
                        <div>{{ validation.fromTime }}</div>
                        <div>{{ validation.toWeekDay }}</div>
                        <div>{{ validation.toTime }}</div>
                    </td>
                </tr>
                <tr v-if="postbox.length > 0">
                    <td>Packstation</td>
                    <td>Die Versandart kann nicht nicht in Kombination mit einer Packstation gewählt werden</td>
                    <td>Ausgeschlossen</td>
                </tr>
                <tr v-if="paymentMethod.length > 0">
                    <td>Zahlarten</td>
                    <td>Die Versandart kann nicht nicht in Kombination mit diesen Zahlarten gewählt werden</td>
                    <td v-for="validation in paymentMethod">
                        <div>{{ validation.invalidPaymentMethod }}</div>
                    </td>
                </tr>
                <tr v-if="article.length > 0">
                    <td>Artikel</td>
                    <td>Die Versandart kann nicht nicht in Kombination mit diesen Artikel gewählt werden</td>
                    <td v-for="validation in article">
                        <div>{{ validation.id }}</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
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
