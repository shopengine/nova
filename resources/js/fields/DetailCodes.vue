<template>
    <panel-item :field="field">
        <template slot="value">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Code</th>
                    <th class="text-left">Codepool</th>
                    <th class="text-left">Rabatt</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="code in field.value">
                    <td>{{code.code}}</td>
                    <td><router-link :to="`/shopengine-codepools/${code.codepoolId}`" class="text-80">{{code.codepoolName}}</router-link></td>
                    <td>{{code.usedValue}}</td>
                </tr>
                </tbody>
            </table>
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['resource', 'field']
    }
</script>

<style scoped>
    tbody tr:last-child td {
        border-bottom: none;
    }
</style>
