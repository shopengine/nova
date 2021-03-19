<template>
    <loading-view :loading="loading">
        <form
                @submit="submitViaUpdateResource"
                autocomplete="off"
                ref="form"
        >
            <form-panel
                    class="mb-8"
                    v-for="panel in panelsWithFields"
                    :panel="panel"
                    :name="panel.name"
                    :key="panel.name"
                    :resource-id="resourceId"
                    :resource-name="resourceName"
                    :fields="panel.fields"
                    mode="form"
                    :validation-errors="validationErrors"
            />

            <div class="flex items-center">
                <cancel-button @click="$router.back()"/>

                <progress-button
                        type="submit"
                        :disabled="isWorking"
                        :processing="wasSubmittedViaUpdateResource"
                >
                    {{ __('Update :resource', {resource: resourceName}) }}
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
            'resourceId',
        ]),

        data() {
            return {
                relationResponse: null,
                loading: true,
                submittedViaUpdateResource: false,
                fields: [],
                panels: [],
                validationErrors: new Errors(),
                isWorking: false,
            }
        },
        mounted() {
            this.resourceId = this.$route.params.resourceId
            this.resourceName = this.$route.params.resourceName
            this.getFields()
        },
        beforeRouteUpdate(to, from, next) {
            this.resourceId = this.$route.params.resourceId
            this.resourceName = to.params.resourceName
            next()
        },
        methods: {
            async getFields() {
                this.loading = true

                this.panels = []
                this.fields = []

                const {data: {panels, fields}} = await Nova.request()
                    .get(
                        `/nova-vendor/novashopengine/${this.resourceName}/${this.resourceId}/update-fields`,
                        {params: {editing: true, editMode: 'update', shop: this.shop}},
                    )
                    .catch((error) => {
                        if (error.response.status === 404) {
                            this.$router.push({name: '404'})
                        }
                    })

                this.panels = panels
                this.fields = fields
                this.loading = false

                Nova.$emit('resource-loaded')
            },

            async submitViaUpdateResource(e) {
                e.preventDefault()
                this.submittedViaUpdateResource = true
                await this.updateResource()
            },

            async updateResource() {
                this.isWorking = true

                if (this.$refs.form.reportValidity()) {
                    try {
                        const {data: {redirect}} = await this.updateRequest()

                        Nova.success(
                            this.__('The :resource was updated!', {
                                resource: this.resourceName,
                            }),
                        )

                        this.$router.push({path: redirect})
                    }
                    catch (error) {
                        this.submittedViaUpdateResource = false

                        if (error.response.status === 422) {
                            this.validationErrors = new Errors(error.response.data.errors)
                            Nova.error(this.__('There was a problem submitting the form.'))
                        }

                        if (error.response.status === 409) {
                            Nova.error(
                                this.__(
                                    'Another user has updated this resource since this page was loaded. Please refresh the page and try again.',
                                ),
                            )
                        }
                    }
                }

                this.submittedViaUpdateResource = false
                this.isWorking = false
            },

            updateRequest() {
                return Nova.request().post(
                    `/nova-vendor/novashopengine/${this.resourceName}/${this.resourceId}`,
                    this.updateResourceFormData,
                    {params: {editing: true, editMode: 'update'}},
                )
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

            updateResourceFormData() {
                return _.tap(new FormData(), formData => {
                    _(this.fields).each(field => {
                        field.fill(formData)
                    })

                    formData.append('_method', 'PUT')
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
