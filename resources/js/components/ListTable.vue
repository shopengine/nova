<template>
    <table class="table w-full">
        <thead>
        <tr>
            <th v-for="field in fields" :class="`text-${field.textAlign}`">
                <sortable-icon
                        @sort="requestOrderByChange(field)"
                        @reset="resetOrderBy(field)"
                        :resource-name="resourceName"
                        :uri-key="field.sortableUriKey"
                        v-if="field.sortable"
                >
                    {{ field.indexName }}
                </sortable-icon>

                <span v-else>{{ field.indexName }}</span>
            </th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(resource, index) in resources">
            <td v-for="field in resource.fields">
                <component
                        :is="'index-' + field.component"
                        :class="`text-${field.textAlign}`"
                        :resource-name="resourceName"
                        :via-resource="viaResource"
                        :via-resource-id="viaResourceId"
                        :field="field"
                />
            </td>

            <td class="td-fit text-right pr-6 align-middle" v-if="resource.authorizedToView || resource.authorizedToUpdate">
                <div class="inline-flex items-center">
                    <span class="inline-flex">
                        <router-link v-if="resource.authorizedToView" :to="`/novashopengine/${resourceName}/${resource.id.value}`" class="inline-flex cursor-pointer text-70 hover:text-primary mr-3" title="View">
                            <icon type="view" width="22" height="18" view-box="0 0 22 16"/>
                        </router-link>
                    </span>
                    <span class="inline-flex" v-if="resource.authorizedToUpdate">
                        <router-link :to="`/novashopengine/${resourceName}/${resource.id.value}/edit`" class="inline-flex cursor-pointer text-70 hover:text-primary mr-3" title="Edit">
                            <icon type="edit"/>
                        </router-link>
                    </span>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    export default {
        props: [
            'resourceName',
            'resources',
            'fields',
            'shop'
        ],
        mounted() {

        },
        computed: {
            orderByParameter() {
                return `${this.resourceName}_order`
            },
            currentOrderBy() {
                return this.$route.query[this.orderByParameter] || ''
            },
            orderByDirectionParameter() {
                return `${this.resourceName}_direction`
            },
            currentOrderByDirection() {
                return this.$route.query[this.orderByDirectionParameter] || null
            },
        },
        methods: {
            requestOrderByChange(field) {
                let direction = this.currentOrderByDirection === 'asc' ? 'desc' : 'asc'

                if (this.currentOrderBy !== field.sortableUriKey) {
                    direction = 'asc'
                }

                const query = {
                    [this.orderByParameter]: field.attribute,
                    [this.orderByDirectionParameter]: direction
                }

                this.$emit('orderChange', query)
            },
            resetOrderBy(field) {
                const query = {
                    [this.orderByParameter]: null,
                    [this.orderByDirectionParameter]: null
                }

                this.$emit('orderChange', query)
            },
        }
    }
</script>

<style scoped>

</style>
