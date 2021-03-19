<template>
    <panel-item :field="field">
        <template slot="value">
            <router-link :to="`/novashopengine/codepools/${field.value}`" class="inline-flex cursor-pointer text-70 hover:text-primary mr-3" title="View">
                {{resource.seModel.codepoolName}}
            </router-link>
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],
    }
</script>
