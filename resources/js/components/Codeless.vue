<template>
  <div>
    <h1>Hello {{name}}</h1>
    <pre>
    {{ codeless }}
  </pre>
  </div>
</template>


<script>
  export default {
  data() {
  return {
  name: "Faisal",
  codeless: null,
}
},

  async mounted() {
    console.log("Mounted")
  this.isLoading = true;

  Nova.request()
  .get('/nova-vendor/novashopengine/codeless')
  .then(response => {
  this.codeless = response.data
    console.log(response.data)
})
  .catch((error) => {
  // this.$router.go(-1)
    console.log(error)
})
  .finally(() => {
  this.isLoading = false
});
},
}
</script>