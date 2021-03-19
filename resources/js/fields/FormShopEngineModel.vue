<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <search-input
                @input="performSearch"
                @selected="selectOption"
                :value="selectedOption"
                :data="filteredOptions"
                :clearable="false"
                trackBy="value"
                class="w-full"
            >
                <div slot="default" v-if="selectedOption" class="flex items-center">
                    {{ selectedOption.label }}
                </div>

                <div
                    slot="option"
                    slot-scope="{ option, selected }"
                    class="flex items-center text-sm font-semibold leading-5 text-90"
                    :class="{ 'text-white': selected }"
                >
                    {{ option.label }}
                </div>
            </search-input>
        </template>
    </default-field>
</template>

<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova'

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        data() {
            return {
                options: [],
                selectedOption: null,
                search: '',
            }
        },
        created() {
            Nova.request().get(`/nova-vendor/novashopengine/${this.field.uriKey}`, {}).then(response => {
                if (response.headers['content-type'] !== 'application/json') {
                    console.error(response)
                    return
                }

                this.options = response.data.resources
                    .map((resource) => {
                        const value = this.field.valueFieldName ?
                            resource.fields.find((field) => field.attribute === this.field.valueFieldName).value :
                            resource.id.value

                        return {
                            label: resource.fields.find((field) => field.attribute === this.field.labelFieldName).value,
                            value
                        }
                    })

                this.selectedOption = this.options
                    .find((option) => option.value == this.value)
            })
        },
        methods: {
            fill(formData) {
                formData.append(this.field.attribute, this.value)
            },

            performSearch(event) {
                this.search = event
            },

            selectOption(option) {
                this.selectedOption = option
                this.value = option.value
            },
        },
        computed: {
            filteredOptions() {
                return this.options.filter(option => {
                    return (
                        option.label.toLowerCase().indexOf(this.search.toLowerCase()) > -1
                    )
                })
            },
            placeholder() {
                return this.field.placeholder || this.__('Choose an option')
            },
            shop() {
                return Nova.config.shopEngineIdentifier
            }
        },
    }
</script>
