<template>
    <div>
        <div class="mb-3"></div>
        <heading :level="1" class="mb-3" v-html="label"></heading>

        <div class="flex">
            <div class="relative h-9 flex-no-shrink mb-6" v-if="isSearchable">
                <icon type="search" class="absolute search-icon-center ml-3 text-70"/>

                <input
                    class="appearance-none form-search w-search pl-search shadow"
                    :placeholder="__('Search')"
                    type="search"
                    v-model.trim="searchString"
                    @keyup="search"
                    @search="search"
                />
            </div>

            <div class="w-full flex items-center mb-6">
                <div class="flex w-full justify-end items-center mx-3"></div>
                <div class="flex-no-shrink ml-auto">
                    <router-link v-if="authToCreate" :to="`/novashopengine/${resourceName}/new`" class="btn btn-default btn-primary">
                        {{ __('Create :resource', {resource: label}) }}
                    </router-link>
                </div>
            </div>
        </div>

        <card>
            <div
                class="flex items-center"
                :class="{'border-b border-50': hasFilters && shopSettings.length > 1}"
            >
                <div v-if="shopSettings.length > 1" v-for="shopSetting in shopSettings" class="flex flex-row">
                    <router-link
                        :to="`/novashopengine/${shopSetting.shop}/${resourceName}`"
                        class="py-4 px-8 focus:outline-none no-underline"
                        :class="shopSetting.shop === shop ? ['text-primary', 'border-primary', 'border-b-2'] : ['text-80', 'border-40']"
                    >
                        {{shopSetting.name}}
                    </router-link>
                </div>

                <div class="flex items-center ml-auto px-3">
                    <filter-menu
                        :resource-name="resourceName"
                        :soft-deletes="softDeletes"
                        :trashed="false"
                        :per-page="perPage"
                        :per-page-options="perPageOptions"
                        @per-page-changed="updatePerPageChanged"
                        @clear-selected-filters="clearSelectedFilters"
                        @filter-changed="filterChanged"
                    />
                </div>
            </div>

            <loading-view :loading="loading">
                <div class="overflow-hidden overflow-x-auto relative">
                    <ListTable
                        :resource-name="resourceName"
                        :resources="resources"
                        :fields="fields"
                        :shop="shop"
                        @orderChange="orderChange"
                    />
                </div>

                <pagination-simple
                    :next="hasNextPage"
                    :previous="hasPreviousPage"
                    @page="selectPage"
                    :pages="totalPages"
                    :page="query.page"
                >
                    <span class="text-sm text-80 px-4">{{query.page * query.pageSize + 1}}-{{Math.min((query.page + 1) * query.pageSize, count)}} of {{count}}</span>
                </pagination-simple>
            </loading-view>
        </card>
    </div>
</template>

<script>
import ListTable from './ListTable'
import defaults from 'lodash/defaults'

export default {
    components: {ListTable},
    data() {
        return {
            resourceName: '',
            label: '&nbsp;',
            resources: [],

            query: {
                pageSize: 25,
                page: 0,
            },

            count: 0,

            hasPreviousPage: false,
            hasNextPage: false,

            searchString: '',
            loading: false,
            authToCreate: false,
            hasFilters: true,

            perPage: 25,
            perPageOptions: [],

            shopSettings: [],
            isSearchable: true,
        }
    },
    computed: {
        fields() {
            if (this.resources[0]) {
                return this.resources[0].fields
            }
        },
        availableActions() {
            return []
        },

        pageParameter() {
            return 'page'
        },

        filterParameter() {
            return this.resourceName + '_filter'
        },

        initialEncodedFilters() {
            return this.$route.query[this.filterParameter] || ''
        },
        totalPages() {
            return Math.ceil(this.count / this.query.pageSize)
        },
        shop() {
            return Nova.config.shopEngineIdentifier
        }
    },
    async created() {
        this.resourceName = this.$route.params.resourceName || this.$route.path.replace(/^\/+/, '').split('/')[1]
        await this.initializeFilters()
    },
    mounted() {
        this.resourceName = this.$route.params.resourceName || this.$route.path.replace(/^\/+/, '').split('/')[1]
        this.setDataFromRouter(this.$route.query)
        this.list()
    },
    beforeRouteUpdate(to, from, next) {
        if (to.params.resourceName !== from.params.resourceName) {
            this.query.sort = undefined
            this.query.search = undefined
        }

        this.resourceName = to.params.resourceName
        //this.shop = to.params.shop

        if (to.query) {
            this.setDataFromRouter(to.query)
            if (to.params.resourceName === from.params.resourceName) {
                this.list()
            }
        }

        next()
    },
    methods: {
        orderChange(query) {
            for (let q in query) {
                this.query[q] = query[q]
            }

            this.$router.push({path: this.resourceName, query: this.query})
        },
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
            this.searchString = newQuery.search
        },
        search: _.debounce(function() {
            this.query.search = this.searchString === '' ? undefined : this.searchString
            this.$router.push({path: this.resourceName, query: this.query})
        }, 200),
        updatePerPageChanged(perPage) {
            this.perPage = perPage
            this.query.pageSize = perPage
            this.list()
        },
        list() {
            this.loading = true

            let params = {...this.query}

            Nova.request().get(`/nova-vendor/novashopengine/${this.resourceName}`, {params}).then(response => {
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
                this.shopSettings = response.data.shopSettings
                this.isSearchable = response.data.isSearchable !== false

                console.log(response.data)

                if (response.data.resources[0]) {
                    this.authToCreate = response.data.resources[0].authorizedToCreate
                }
                else {
                    this.authToCreate = false //true // todo: ???
                }
            })
        },

        updateQueryString(value) {
            this.$router.push({query: defaults(value, this.$route.query)})
        },

        selectPage(page) {
            this.updateQueryString({ [this.pageParameter]: page })
        },

        async clearSelectedFilters() {
            await this.$store.dispatch(`${this.resourceName}/resetFilterState`, {
                resourceName: this.resourceName,
            })

            this.updateQueryString({
                [this.pageParameter]: 0,
                [this.filterParameter]: '',
            })
        },

        filterChanged() {
            this.updateQueryString({
                [this.pageParameter]: 0,
                [this.filterParameter]: this.$store.getters[`${this.resourceName}/currentEncodedFilters`],
            })
        },

        async initializeFilters() {
            // Clear out the filters from the store first
            this.$store.commit(`${this.resourceName}/clearFilters`)

            await this.$store.dispatch(`${this.resourceName}/fetchFilters`, {
                resourceName: this.resourceName,
                viaResource: this.viaResource,
                viaResourceId: this.viaResourceId,
                viaRelationship: this.viaRelationship,
            })

            await this.initializeState()
        },

        async initializeState() {
            this.initialEncodedFilters ?
                await this.$store.dispatch(
                    `${this.resourceName}/initializeCurrentFilterValuesFromQueryString`,
                    this.initialEncodedFilters,
                ) :
                await this.$store.dispatch(`${this.resourceName}/resetFilterState`, {
                    resourceName: this.resourceName,
                })
        },
    },
}
</script>
