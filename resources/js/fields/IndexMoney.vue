<template>
    <span>{{ value }}</span>
</template>

<script>
    import helper from '../helper'

    export default {
        props: ['resourceName', 'field'],
        computed: {
            value() {
                return helper.formatMoney(this.field.value, this.locale)
            },
        },
    }
</script>
