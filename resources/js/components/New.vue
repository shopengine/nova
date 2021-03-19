<template>
    <loading-view :loading="loading">
        <form
                @submit="submitViaCreateResource"
                autocomplete="off"
                ref="form"
        >
            <form-panel
                    class="mb-8"
                    v-for="panel in panelsWithFields"
                    :shown-via-new-relation-modal="false"
                    :panel="panel"
                    :name="panel.name"
                    :key="panel.name"
                    :resource-name="resourceName"
                    :fields="panel.fields"
                    mode="form"
                    :validation-errors="validationErrors"
                    :via-resource="viaResource"
                    :via-resource-id="viaResourceId"
                    :via-relationship="viaRelationship"
            />

            <div class="flex items-center">
                <cancel-button @click="$router.back()"/>

                <progress-button
                        type="submit"
                        :disabled="isWorking"
                        :processing="wasSubmittedViaCreateResource"
                >
                    {{ __('Create :resource', { resource: resourceName }) }}
                </progress-button>
            </div>
        </form>
    </loading-view>
</template>

<script>
    import {Errors, mapProps} from 'laravel-nova'

    export default {
        props: mapProps([
            'resourceName',
        ]),

        data() {
            return {
                fields: [],
                panels: [],
                validationErrors: new Errors(),
                isWorking: false,
                submittedViaCreateResourceAndAddAnother: false,
                submittedViaCreateResource: false,
                loading: true,
            }
        },
        mounted() {
            this.resourceName = this.$route.params.resourceName
            this.getFields()
        },
        beforeRouteUpdate(to, from, next) {
            this.resourceName = to.params.resourceName
            next()
        },
        methods: {
            async getFields() {
                this.loading = true
                this.panels = []
                this.fields = []

                const {data: {panels, fields}} = await Nova.request().get(
                    `/nova-api/${this.resourceName}/creation-fields`,
                    {params: {editing: true, editMode: 'create', shop: this.shop}},
                )

                var qd = {}
                if (location.search) location.search.substr(1).split`&`.forEach(item => {
                    let [k, v] = item.split`=`
                    v = v && decodeURIComponent(v);
                    (qd[k] = qd[k] || []).push(v)
                })

                for (const field of Object.values(fields)) {
                    if (qd[field.attribute] && field.value === null) {
                        field.value = qd[field.attribute][0]
                    }
                }

                this.panels = panels
                this.fields = fields
                this.loading = false
            },

            async submitViaCreateResource(e) {
                e.preventDefault()
                this.submittedViaCreateResource = true
                this.submittedViaCreateResourceAndAddAnother = false
                await this.createResource()
            },

            async createResource() {
                this.isWorking = true

                if (this.$refs.form.reportValidity()) {
                    try {
                        const {data: {redirect, id}} = await this.createRequest()

                        Nova.success(
                            this.__('The :resource was created!', {
                                resource: this.resourceName,
                            }),
                        )

                        if (this.submittedViaCreateResource) {
                            this.$router.push({path: redirect})
                        }
                        else {
                            // Reset the form by refetching the fields
                            this.getFields()
                            this.validationErrors = new Errors()
                            this.submittedViaCreateAndAddAnother = false
                            this.submittedViaCreateResource = false
                            this.isWorking = false

                            return
                        }
                    }
                    catch (error) {
                        this.submittedViaCreateAndAddAnother = false
                        this.submittedViaCreateResource = true
                        this.isWorking = false

                        if (error.response.status === 422) {
                            this.validationErrors = new Errors(error.response.data.errors)
                            Nova.error(this.__('There was a problem submitting the form.'))
                        }
                    }
                }

                this.submittedViaCreateAndAddAnother = false
                this.submittedViaCreateResource = true
                this.isWorking = false
            },

            createRequest() {
                return Nova.request().post(
                    `/nova-vendor/novashopengine/${this.resourceName}`,
                    this.createResourceFormData(),
                    {params: {editing: true, editMode: 'create'}},
                )
            },

            createResourceFormData() {
                return _.tap(new FormData(), formData => {
                    _.each(this.fields, field => {
                        field.fill(formData)
                    })
                })
            },
        },
        computed: {
            panelsWithFields() {
                return _.map(this.panels, panel => {
                    return {
                        name: panel.name,
                        fields: _.filter(this.fields, field => field.panel === panel.name),
                    }
                })
            },
            shop() {
                return Nova.config.shopEngineIdentifier
            }
        },
    }
</script>

<style scoped>

</style>
