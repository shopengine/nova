<template>
    <div style="position:relative;">
        <button @click="openOptionModal" class="btn btn-default btn-primary" style="position:absolute;right: -1.5rem; top: -50px">
            Add Option
        </button>

        <portal
                to="modals"
                v-if="optionModalOpen"
        >
            <ShippingCostOptionCreateModal
                    v-if="optionModalOpen"
                    :shop="this.resource.shop"
                    @save="saveOption"
                    @close="closeOptionModal"
            />
        </portal>

        <table class="table card" style="width:calc(100% + 3rem);margin: -0.75rem -1.5rem">
            <thead>
            <tr>
                <th class="text-left">Preis</th>
                <th class="text-left">Warenkorb Mindestwert</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="option in field.value" :key="option.id">
                <td class="text-left">{{formatMoney(option.price)}}</td>
                <td class="left">{{formatMoney(option.validation.sub)}}</td>
                <td>
                    <button
                            @click="openDeleteModal(option.id)"
                            class="btn btn-default btn-icon btn-white mr-3"
                            :title="__('Delete')"
                    >
                        <icon type="delete" class="text-80" />
                    </button>

                    <portal
                            to="modals"
                            v-if="deleteModalOpen"
                    >
                        <delete-resource-modal
                                v-if="deleteModalOpen"
                                @confirm="confirmDelete()"
                                @close="closeDeleteModal"
                                mode="delete"
                        />
                    </portal>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import ShippingCostOptionCreateModal from '../components/Modals/ShippingCostOptionCreateModal'
import helper from '../helper'

export default {
        components: {ShippingCostOptionCreateModal},
        props: ['resource', 'resourceName', 'resourceId', 'field'],
        data() {
            return {
                optionModalOpen: false,
                deleteModalOpen: false,
                selectedOption: null
            }
        },
        methods: {
            openDeleteModal(id) {
                this.selectedOption = id
                this.deleteModalOpen = true
            },
            closeDeleteModal() {
                this.deleteModalOpen = false
            },
            async confirmDelete() {
                await Nova.request().post(`/nova-vendor/shopengine/shipping-costs/${this.resourceId}/removeOption`, {
                    values: {
                        optionId: this.selectedOption
                    }
                })

                Nova.success(
                    this.__('The :resource was deleted!', {
                        resource: 'Versandoption',
                    }),
                )

                this.field.value = this.field.value.filter((option) => option.id !== this.selectedOption)
                this.selectedOption = null

                this.closeDeleteModal()
            },
            openOptionModal() {
                this.optionModalOpen = true
            },
            closeOptionModal() {
                this.optionModalOpen = false
            },
            async saveOption(values) {
                 await Nova.request()
                    .post(`/nova-vendor/shopengine/shipping-costs/${this.resourceId}/addOption`, {values})

                Nova.success(
                    this.__('The :resource was created!', {
                        resource: 'Versandoption',
                    }),
                )

                // im lazy
                // should use data from api
                this.field.value.push(values)
                this.closeOptionModal()
            },
            formatMoney(obj) {
                return helper.formatMoney(obj)
            }
        }
    }
</script>
