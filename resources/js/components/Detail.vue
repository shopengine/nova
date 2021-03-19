<template>
    <div>
        <div class="mb-3"></div>

        <div
                v-for="panel in availablePanels"
                :dusk="resourceName + '-detail-component'"
                class="mb-8"
                :key="panel.id"
        >
            <component
                    :is="panel.component"
                    :resource-name="resourceName"
                    :resource-id="resourceId"
                    :resource="resource"
                    :panel="panel"
            >
                <div v-if="panel.showToolbar" class="flex items-center mb-3">
                    <heading :level="1" class="flex-no-shrink">{{panel.name}}</heading>

                    <div class="ml-3 w-full flex items-center">
                        <div class="flex w-full justify-end items-center"></div>
                        <router-link v-if="resource.authorizedToUpdate" :to="`/novashopengine/${resourceName}/${resourceId}/edit`" class="btn btn-default btn-icon bg-primary" title="Edit">
                            <icon
                                    type="edit"
                                    class="text-white"
                                    style="margin-top: -2px; margin-left: 3px"
                            />
                        </router-link>
                    </div>
                </div>
            </component>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                resourceName: '',
                resourceId: '',

                resource: null,
                panels: [],
            }
        },
        computed: {
            availablePanels() {
                if (this.resource) {
                    var panels = {}

                    var fields = _.toArray(JSON.parse(JSON.stringify(this.resource.fields)))

                    fields.forEach(field => {
                        if (panels[field.panel]) {
                            return panels[field.panel].fields.push(field)
                        }

                        panels[field.panel] = this.createPanelForField(field)
                    })

                    return _.toArray(panels)
                }
            },
            shop() {
                return Nova.config.shopEngineIdentifier
            }
        },
        mounted() {
            this.resourceName = this.$route.params.resourceName
            this.resourceId = this.$route.params.resourceId
            this.load()
        },
        beforeRouteUpdate(to, from, next) {
            this.resourceName = to.params.resourceName
            this.resourceId = to.params.resourceId
            this.load()
            next()
        },
        methods: {
            load() {
                Nova.request().get(`/nova-vendor/novashopengine/${this.resourceName}/${this.resourceId}`).then(response => {
                    if (response.headers['content-type'] !== 'application/json') {
                        console.error(response)
                        return
                    }

                    this.resource = response.data.resource
                    this.resource.shop = this.shop
                    this.resource.seModel = response.data.seModel
                    this.panels = response.data.panels
                })
            },
            createPanelForField(field) {
                return _.tap(
                    _.find(this.panels, (panel) => panel.name == field.panel),
                    (panel) => panel.fields = [field],
                )
            },
        },
    }
</script>

<style scoped>
    .border-b:last-child {
        border-bottom: none;
    }
</style>
