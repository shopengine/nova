<template>
    <panel-item :field="field">
        <template slot="value">
            {{field.value.name}}<br>
            <span v-if="field.value.addressLine1">{{field.value.addressLine1}}<br></span>
            {{field.value.addressLine2}}<br>
            {{field.value.postalCode}} {{field.value.addressLevel2}}<br>
            {{field.value.country}}
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>