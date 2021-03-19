<template>
    <modal @modal-close="handleClose">
        <form
                @submit.prevent="handleSave"
                class="bg-white rounded-lg shadow-lg overflow-hidden"
                style="width: 460px"
        >
            <slot>
                <div class="p-8">
                    <heading :level="2" class="mb-6">Option hinzufügen</heading>
                    <p class="py-2 text-80 leading-normal">
                        <label for="shipping-cost-option-price">Preis</label>
                        <input
                                id="shipping-cost-option-price"
                                class="w-full form-control form-input form-input-bordered"
                                v-model="price"
                                required
                                type="number"
                                step="0.01"
                                min="0"
                                ref="firstInput"
                        />
                    </p>
                    <p class="py-2 text-80 leading-normal">
                        <label for="shipping-cost-option-min">Warenkorb Mindestwert</label>
                        <input
                                id="shipping-cost-option-min"
                                class="w-full form-control form-input form-input-bordered"
                                v-model="min"
                                required
                                type="number"
                                step="0.01"
                                min="0"
                        />
                    </p>
                </div>
            </slot>

            <div class="bg-30 px-6 py-3 flex">
                <div class="ml-auto">
                    <button
                            type="button"
                            @click.prevent="handleClose"
                            class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
                    >
                        {{ __('Cancel') }}
                    </button>

                    <button
                            id="confirm-delete-button"
                            ref="confirmButton"
                            type="submit"
                            class="btn btn-default btn-primary"
                    >
                        Save
                    </button>
                </div>
            </div>
        </form>
    </modal>
</template>

<script>
    export default {
        props: ['shop'],
        data () {
            return {
                price: null,
                min: null
            }
        },
        computed: {
            currency() {
                return Nova.config.shop.currency
            }
        },
        mounted() {
            this.$refs.firstInput.focus()
        },
        methods: {
            handleClose() {
                this.$emit('close')
            },

            handleSave() {
                if (this.price < 0 || this.min < 0) {
                    return
                }

                let currency = 'EUR'
                if (this.shop === 'reishunger-chf') {
                    currency = 'CHF'
                }

                const data = {
                    price: {amount: Math.round(this.price * 100), currency},
                    validation: {sub: {amount: Math.round(this.min * 100), currency}}
                }

                this.$emit('save', data)
            },
        }
    }
</script>
