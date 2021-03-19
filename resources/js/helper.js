
export default {
    formatAmount(amount, currency = 'EUR', locale = 'de-DE') {
        return new Intl.NumberFormat(locale, {style: 'currency', currency})
            .format(parseInt(amount, 10) / 100)
    },

    formatMoney(moneyObj, locale = 'de-DE') {
        return new Intl.NumberFormat(locale, {style: 'currency', currency: moneyObj.currency})
            .format(parseInt(moneyObj.amount, 10) / 100)
    }
}
