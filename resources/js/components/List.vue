<template>
    <div v-if="resourceName">
        <resource-index
            :field="null"
            :resource-name="resourceName"
            :via-resource="null"
            :via-resource-id="null"
            :via-relationship="null"
            :relationship-type="null"
            :load-cards="false"
            :disable-pagination="false"
        />
    </div>
</template>
<script>
export default {
    computed: {
        resourceName() {
            return this.$route.params.resourceName
        },
        fields() {
            if (this.resources[0]) {
                return this.resources[0].fields
            }
        }
    }
}
</script>
