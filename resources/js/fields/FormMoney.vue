<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <span style='position:relative;'>
                <input
                    :id="field.name"
                    type="number"
                    step="0.01"
                    class="w-full form-control form-input form-input-bordered"
                    :class="errorClasses"
                    :placeholder="field.name"
                    v-model="amount"
                />
                <span class="floaty">{{field.currency}}</span>
            </span>
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
                amount: 0
            }
        },

        methods: {
            setInitialValue() {
                this.amount = 0
                if (this.field.value && this.field.value.amount) {
                    this.amount = parseInt(this.field.value.amount, 10) / 100
                }
            },

            fill(formData) {
                const json = JSON.stringify({amount: Math.round(this.amount * 100), currency: this.field.currency})
                formData.append(this.field.attribute, json)
            },

            handleChange(amount) {
                this.amount = amount
            },
        },
    }
</script>

<style scoped>
    input {
        padding-right: 45px
    }

    .floaty {
        position: absolute;
        top: 0;
        right: 8px;
        width: 30px;
        text-align: right;
        padding-right: .75rem;
        height: 2.25rem;
        line-height: normal;
    }
</style>
