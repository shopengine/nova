<template>
    <panel-item :field="reformattedField" />
</template>

<script>
    import helper from '../helper'

    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],
        computed: {
            reformattedField() {
                let newField = {...this.field}
                newField.value = helper.formatMoney(this.field.value)
                return newField
            },
        }
    }
</script>
