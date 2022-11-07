<template>
    <loading-view :loading="loading" class="card">
        <div class="overflow-hidden overflow-x-auto relative">
            <resource-table
                :resource-name="inlineResourceName"
                :resources="resources"
                :fields="fields"
                @orderChange="orderChange"
            />
        </div>
    </loading-view>
</template>

<script>

export default {
    components: {

    },
    props: [
        'resourceName',
        'resourceId',
        'resource'
    ],
    data() {
        return {
            loading: false,
            inlineResourceName: 'codes',
            resources: [],

            perPage: 25,
            perPageOptions: [],
            count: 0,

            query: {
                pageSize: 25,
                page: 0
            },

            hasPreviousPage: false,
            hasNextPage: false,
        }
    },
    computed: {
        fields() {
            if (this.resources[0]) {
                return this.resources[0].fields
            }
        },
        isArchived() {
            return this.resource.seModel.deletedAt !== null
        }
    },
    mounted() {
        this.setDataFromRouter(this.$route.query)
    },
    watch: {
        $route(to, from) {
            if (to.query) {
                this.setDataFromRouter(to.query)
            }
        }
    },
    methods: {
        setDataFromRouter(query) {
            if (!query) {
                return
            }

            let newQuery = {
                pageSize: 25,
                page: 0,
            }

            for (let q in query) {
                let value = query[q]

                if (value === undefined) {
                    continue
                }

                if (q === 'page' || q === 'pageSize') {
                    value = parseInt(value, 10)
                }

                newQuery[q] = value
            }

            this.query = newQuery
        },
        orderChange(query) {
            console.log(query)
        },
    }
}
</script>
