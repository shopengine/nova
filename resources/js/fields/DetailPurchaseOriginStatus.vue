<template>
    <panel-item :field="field" v-if="canUpdateOriginStatus">
        <template slot="value">
            <button v-on:click="send" :disabled="loading"  class="btn btn-default btn-primary">Freigeben</button>
        </template>
    </panel-item>
</template>

<script>
export default {
    props: [
        'field',
        'resourceName',
        'resourceId',
        'resource'
    ],
    data() {
        return {
            loading: false,
            canUpdateOriginStatus: this.resource.seModel.originStatus !== 'imported'
        }
    },
    methods: {
        send() {
            if (this.loading) {
                return
            }

            if (confirm('Wirklich?')) {
                this.loading = true

                Nova.request().post(`/nova-vendor/shopengine/purchases/${this.resourceId}/origin-status`).then(response => {
                    if (response.headers['content-type'] !== 'application/json') {
                        console.error(response)
                        return
                    }

                    if (response.data !== 'ok') {
                        Nova.error('Es gabe einen Fehler: ' + response.data)
                    }
                    else {
                        Nova.success('Gespeichert')
                        setTimeout(() => window.location.reload(), 100)
                    }

                    this.loading = false
                })
            }
        }
    }
}
</script>

<style scoped>

</style>
