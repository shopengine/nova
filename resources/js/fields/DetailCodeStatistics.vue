<template>
    <panel-item :field="field">
        <template slot="value">
            <span v-html="field.value"></span>
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>
