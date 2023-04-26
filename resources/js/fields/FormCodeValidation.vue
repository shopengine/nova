<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <div class="pb-4">
                <label>Startet (UTC)</label>

                <div class="flex items-center">
                    <date-time-picker
                        class="w-full form-control form-input form-input-bordered mt-2"
                        ref="startDateTimePicker"
                        :placeholder="'Sofort'"
                        :dateFormat="'Y-m-d H:i:S'"
                        :value="start"
                        :twelve-hour-time="false"
                        @change="handleStartChange"
                        :disabled="false"
                    />

                    <a
                        @click.prevent="$refs.startDateTimePicker.clear()"
                        href="#"
                        :title="__('Clear value')"
                        tabindex="-1"
                        class="p-1 px-2 cursor-pointer leading-none focus:outline-none"
                        :class="{
                            'text-50': !start,
                            'text-black hover:text-danger': !!start,
                        }"
                    >
                        <icon type="x-circle" width="22" height="22" viewBox="0 0 22 22" />
                    </a>
                </div>
            </div>

            <div class="pb-4">
                <label>Endet (UTC)</label>

                <div class="flex items-center">
                    <date-time-picker
                        class="w-full form-control form-input form-input-bordered mt-2"
                        ref="expiresDateTimePicker"
                        :placeholder="'Niemals'"
                        :dateFormat="'Y-m-d H:i:S'"
                        :value="expires"
                        :twelve-hour-time="false"
                        @change="handleExpiresChange"
                        :disabled="false"
                    />

                    <a
                        @click.prevent="$refs.expiresDateTimePicker.clear()"
                        href="#"
                        :title="__('Clear value')"
                        tabindex="-1"
                        class="p-1 px-2 cursor-pointer leading-none focus:outline-none"
                        :class="{
                            'text-50': !expires,
                            'text-black hover:text-danger': !!expires,
                        }"
                    >
                        <icon type="x-circle" width="22" height="22" viewBox="0 0 22 22" />
                    </a>
                </div>
            </div>

            <div class="pb-4">
                <label>Verbleibende Benutzungen</label>
                <input
                    type="number"
                    step="1"
                    min="0"
                    class="w-full form-control form-input form-input-bordered mt-2"
                    :class="errorClasses"
                    :placeholder="'Unendlich'"
                    v-model="usageCount"
                />
            </div>

            <div class="pb-4">
                <label>Email</label>

                <input
                        type="email"
                        class="w-full form-control form-input form-input-bordered mt-2"
                        :class="errorClasses"
                        :placeholder="'max@musterman.de'"
                        v-model="email"
                />
                <div class="help-text help-text mt-2">
                    Code kann nur mit dieser Email Adresse benutzt werden.
                </div>
            </div>

            <div class="pb-4"
                v-if="this.resourceId"
            >
                <label>Ein mal pro Email</label>
                <div>
                    <input
                        type="checkbox"
                        class="checkbox mt-2"
                        :class="errorClasses"
                        v-model="emailOnce"
                    />
                </div>
            </div>
            <div class="pb-4">
                <label>Neukunden</label>
                <div>
                    <input
                        type="checkbox"
                        class="checkbox mt-2"
                        :class="errorClasses"
                        v-model="newCustomer"
                    />
                </div>
            </div>
          <div class="pb-4">
            <label>Guthaben</label>
            <div>
              <input
                  type="checkbox"
                  class="checkbox mt-2"
                  :class="errorClasses"
                  v-model="showLeftOver"
              />
              <div class="flex items-center my-2"
                   v-if="showLeftOver"
              >
                <input
                    type="number"
                    min="0"
                    step="0.01"
                    class="w-30 form-control form-input form-input-bordered"
                    :class="errorClasses"
                    :placeholder="''"
                    v-model="leftOverAmount"
                />
                <div class="px-2 whitespace-no-wrap">{{ currency }} auf</div>
                <select v-model="leftOverTarget"
                        class="w-30 form-control form-select form-input-bordered"
                        :class="errorClasses"
                >
                  <option value="totals">Summe</option>
                  <option value="sub">Warenkorb</option>
                </select>
              </div>
            </div>
          </div>
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
                start: null,
                expires: null,
                usageCount: null,
                email: null,
                emailOnce: null,
                newCustomer: null,
                showLeftOver: null,
                leftOverAmount: 100,
                leftOverTarget: 'totals',
                currency: Nova.config.shopCurrency,
            }
        },

        methods: {
            setInitialValue() {
                if (!this.field.value) {
                    return
                }

                for (let v of this.field.value) {
                    let value = v.value

                    switch (v.type) {
                        case 'start':
                        case 'expires':
                            if (typeof value === 'object' && value !== null) {
                                if (value.date) {
                                    value = moment(value.date).format('Y-MM-DD HH:mm:ss')
                                }
                            }
                            break;
                        case 'newCustomer':
                            value = 1
                            break;
                        case 'emailOnce':
                            value = 1
                            break;
                        case 'leftOver':
                            this.showLeftOver = true
                            this.leftOverAmount = value.amount / 100.0
                            this.leftOverTarget = v.target
                            break;
                    }

                    this[v.type] = value
                }
            },

            fill(formData) {
                let obj = {}

                if (this.start) {
                    obj.start = this.start
                }

                if (this.expires) {
                    obj.expires = this.expires
                }

                if (this.usageCount) {
                    obj.usageCount = this.usageCount
                }

                if (this.email) {
                    obj.email = this.email
                }

                if (this.emailOnce) {
                    obj.emailOnce = this.resourceId
                }

                if (this.showLeftOver && this.leftOverAmount > 0 && this.leftOverTarget) {
                    const currency = Nova.config.shopCurrency
                    obj.leftOver = {
                      value: {
                        amount: Math.round(this.leftOverAmount * 100),
                        currency
                      },
                      target: this.leftOverTarget
                    }
                }

                if (this.newCustomer) {
                    obj.newCustomer = true
                }

                const json = JSON.stringify(obj)
                formData.append(this.field.attribute, json)
            },

            handleStartChange(value) {
                this.start = value
            },

            handleExpiresChange(value) {
                this.expires = value
            },
        },
    }
</script>

<style scoped>

</style>
