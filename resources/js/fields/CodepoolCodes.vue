<template>
    <loading-view :loading="loading" style="margin: -0.75rem -1.5rem" class="card">
        <router-link v-if="!isArchived" :to="`/novashopengine/${inlineResourceName}/new?codepoolId=${resourceId}`" class="btn btn-default btn-primary" style="position: absolute; right: 0; top: -48px;">
            {{ __('Create :resource', {resource: label}) }}
        </router-link>

        <div class="overflow-hidden overflow-x-auto relative">
            <resource-table
                :resource-name="inlineResourceName"
                :resources="resources"
                :fields="fields"
                @orderChange="orderChange"
            />
        </div>

        <div class="bg-20 rounded-b">
            <nav class="flex justify-between items-center">
                <router-link :to="`/resources/${resourceName}/${resourceId}?page=${query.page - 1}`" v-bind:disabled="!hasPreviousPage" v-bind:class="{'opacity-50': !hasPreviousPage}" tag="button" rel="prev" class="btn btn-link py-3 px-4 text-80">Previous</router-link>
                <span class="text-sm text-80 px-4">{{query.page * query.pageSize + 1}}-{{Math.min((query.page + 1) * query.pageSize, count)}} of {{count}}</span>
                <router-link :to="`/resources/${resourceName}/${resourceId}?page=${query.page + 1}`" v-bind:disabled="!hasNextPage" v-bind:class="{'opacity-50': !hasNextPage}" tag="button" rel="next" class="btn btn-link py-3 px-4 text-80">Next</router-link>
            </nav>
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
        this.list()
    },
    watch: {
        $route(to, from) {
            if (to.query) {
                this.setDataFromRouter(to.query)
                this.list()
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
        list() {
            this.loading = true

            const params = {...this.query, 'codepoolId-eq': this.resourceId}

            Nova.request().get(`/nova-vendor/novashopengine/codes`, {params}).then(response => {
                if (response.headers['content-type'] !== 'application/json') {
                    console.error(response)
                    return
                }

                this.label = response.data.label
                this.resources = response.data.resources
                this.count = response.data.count
                const maxPages = Math.ceil(this.count / this.query.pageSize) - 1

                this.hasPreviousPage = this.query.page >= 1
                this.hasNextPage = this.query.page < maxPages
                this.loading = false
                this.perPageOptions = response.data.per_page_options
                this.perPage = response.data.per_page
            })
        }
    }
}
</script>
