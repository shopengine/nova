<template>
    <panel-item :field="field">
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
            loading: false
        }
    },
    methods: {
        send() {
            if (this.loading) {
                return
            }

            if (confirm('Wirklich?')) {
                this.loading = true

                Nova.request().post(`/nova-vendor/novashopengine/purchases/${this.resourceId}/manualJTL`).then(response => {
                    if (response.headers['content-type'].indexOf('application/json') === -1) {
                      console.error(response, response.headers['content-type'])
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
