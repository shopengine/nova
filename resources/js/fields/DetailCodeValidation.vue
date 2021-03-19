<template>
    <panel-item :field="field">
        <template slot="value">
            <table class="table">
                <tr v-for="validation in field.value">
                    <td>{{label(validation.type)}}</td>
                    <td>{{value(validation.value)}}</td>
                </tr>
            </table>
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],

        methods: {
            label(type) {
                switch (type) {
                    case 'start':
                        return 'Startet (UTC)'
                    case 'expires':
                        return 'Endet (UTC)'
                    case 'usageCount':
                        return 'Verbleibende Benutzungen'
                }

                return type
            },
            value(v) {
                if (typeof v === 'object' && v !== null) {
                    if (v.date) {
                        return moment(v.date).format('Y-MM-DD HH:mm:ss')
                    }
                }

                return v
            }
        }
    }
</script>
