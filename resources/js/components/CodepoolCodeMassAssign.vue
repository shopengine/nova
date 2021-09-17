<template>
    <modal
        data-testid="confirm-action-modal"
        tabindex="-1"
        role="dialog"
        @modal-close="handleClose"
        :classWhitelist="[
      'flatpickr-current-month',
      'flatpickr-next-month',
      'flatpickr-prev-month',
      'flatpickr-weekday',
      'flatpickr-weekdays',
      'flatpickr-calendar',
    ]"
    >
        <form
            autocomplete="off"
            @keydown="handleKeydown"
            @submit.prevent.stop="handleConfirm"
            class="bg-white rounded-lg shadow-lg overflow-hidden"
            :class="{
        'w-action-fields': action.fields.length > 0,
        'w-action': action.fields.length == 0,
      }"
        >
            <div>
                <heading :level="2" class="border-b border-40 py-8 px-8">{{
                        action.name
                    }}</heading>


                <div>
                    <!-- Validation Errors -->
                    <validation-errors :errors="errors" />

                    <!-- Action Fields -->
                    <div
                        class="action"
                        v-for="field in action.fields"
                        :key="field.attribute"
                    >
                        <component
                            :is="'form-' + field.component"
                            :errors="errors"
                            :resource-name="resourceName"
                            :field="field"
                        />
                    </div>
                </div>
            </div>

            <div class="bg-30 px-6 py-3 flex">
                <div class="flex items-center ml-auto">
                    <button
                        dusk="cancel-action-button"
                        type="button"
                        @click.prevent="handleClose"
                        class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6"
                    >
                        {{ action.cancelButtonText }}
                    </button>

                    <button
                        ref="runButton"
                        dusk="confirm-action-button"
                        :disabled="working"
                        type="submit"
                        class="btn btn-default"
                        :class="action.class"
                    >
                        <loader v-if="working" width="30"></loader>
                        <span v-else>{{ action.validateText }}</span>
                    </button>
                </div>
            </div>
        </form>
    </modal>
</template>

<script>
export default {
    props: {
        working: Boolean,
        resourceName: { type: String, required: true },
        action: { type: Object, required: true },
        selectedResources: { type: [Array, String], required: true },
        errors: { type: Object, required: true },
        validate: false
    },

    /**
     * Mount the component.
     */
    mounted() {
        if (document.querySelectorAll('.modal input').length) {
            document.querySelectorAll('.modal input')[0].focus()
        } else {
            this.$refs.runButton.focus()
        }
    },

    methods: {

        handleKeydown(e) {
            if (['Escape', 'Enter'].indexOf(e.key) !== -1) {
                return
            }

            e.stopPropagation()
        },

        handleConfirm() {
            this.$emit('confirm')
        },


        handleClose() {
            this.$emit('close')
        },
    },
}
</script>
