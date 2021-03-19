<template>
    <div class="">
        <div class="py-4 border-b border-40">
            <router-link :to="`/novashopengine/${resourceName}/${resourceId}/stats`" tag="button" class="p-1 text-black">Statistiken nach Zeit aufschlüsseln</router-link>
        </div>

        <div class="py-4">
            <a :href="`/nova-vendor/novashopengine/${resourceName}/${id}/export/orders`" class="p-1 text-black">Emails: Bestellungen</a>
            <a :href="`/nova-vendor/novashopengine/${resourceName}/${id}/export/newCustomers`" class="p-1 text-black">Emails: Neukunden</a>
            <a :href="`/nova-vendor/novashopengine/${resourceName}/${id}/export/activeCodes`" v-if="resourceName === 'codepools'" class="p-1 text-black">Aktive Codes</a>
        </div>

        <div class="py-4 border-t border-40" v-if="!isArchived && resourceName === 'codepools'">
            <button v-on:click="archive" :disabled="archiveLoading"  class="btn btn-default btn-danger btn-sm">Codepool Archivieren</button>
        </div>
    </div>

</template>

<script>
    export default {
        props: [
            'resourceName',
            'resourceId',
            'resource'
        ],
        data() {
            return {
                archiveLoading: false
            }
        },
        computed: {
            isArchived() {
                return this.resource.seModel && this.resource.seModel.deletedAt !== null
            },

            id() {
                return (this.resourceName === 'codes') ? this.resource.seModel.code : this.resourceId
            }
        },
        methods: {
            async archive() {
                if (this.archiveLoading) {
                    return
                }

                if (confirm('Wirklich Archivieren?')) {
                    this.archiveLoading = true

                    Nova.request().get(`/nova-vendor/novashopengine/codepools/${this.resourceId}/archive`).then(response => {
                        if (response.headers['content-type'] !== 'application/json') {
                            console.error(response)
                            return
                        }

                        if (response.data.status !== 'ok') {
                            Nova.error('Es gabe einen Fehler: ' + response.data.message)
                        }
                        else {
                            Nova.success('Der Codepool wurde archiviert')
                            setTimeout(() => window.location.reload(), 100)
                        }

                        this.archiveLoading = false
                    })
                }
            }
        }
    }
</script>

<style scoped>

</style>
