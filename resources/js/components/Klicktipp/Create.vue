<template>
  <div data-testid="content" class="px-view py-view mx-auto">
    <div data-v-8affbeb6="" class="progress"
         style="width: 0%; height: 3px; opacity: 0; background-color: var(--primary);"></div>
    <div class="relative">
      <div class="mb-3"></div>
      <form autoComplete="off">
        <div class="mb-8">
          <h1 class="text-90 font-normal text-2xl mb-3">Klicktipp bearbeiten: </h1>
          <!---->
          <div class="card">
            <div class="flex border-b border-40">
              <div class="px-8 py-6 w-1/5">
                <label htmlFor="class" class="inline-block text-80 pt-2 leading-tight">
                  Tag <span class="text-danger text-sm">*</span></label>
              </div>
              <div class="py-6 px-8 w-1/2">
                <input id="class" dusk="tag" list="tag-list" type="number"
                       placeholder="Tag"
                       class="w-full form-control form-input form-input-bordered"
                       v-model="tag"
                >
              </div>
            </div>
            <div class="flex border-b border-40">
              <div class="px-8 py-6 w-1/5"><label htmlFor="class"
                                                  class="inline-block text-80 pt-2 leading-tight">
                From&nbsp;<span class="text-danger text-sm">*</span></label>
              </div>
              <div class="py-6 px-8 w-1/2">
                <input id="class" dusk="tag" list="tag-list" type="datetime-local"
                       placeholder="Tag"
                       step="any"
                       class="w-full form-control form-input form-input-bordered"
                       v-model="from">
              </div>
            </div>
            <div class="flex border-b border-40">
              <div class="px-8 py-6 w-1/5"><label htmlFor="class"
                                                  class="inline-block text-80 pt-2 leading-tight">
                To&nbsp;<span class="text-danger text-sm">*</span></label>
              </div>
              <div class="py-6 px-8 w-1/2">
                <input id="class" dusk="tag" list="tag-list" type="datetime-local"
                       placeholder="Tag"
                       step="any"
                       class="w-full form-control form-input form-input-bordered"
                       v-model="to">
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center">
          <router-link to="/novashopengine/klicktipps" tabIndex="0"
                       class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6">
            Cancel
          </router-link>
          <button type="button" class="btn btn-default btn-primary inline-flex items-center relative"
                  dusk="update-button"
          @click="submit">
          <span class="">
                  Klicktipp erstellen
                  </span>
        </button>
    </div>
  </form>
</div>
</div>
</template>


<script>
  export default {
  data() {
  return {
  isLoading: true,
  isSaving: false,
  klicktipp: this.$route.params.klicktipp,
  tag: '',
  from: this.now(),
  to: this.now()
}
},

  methods: {
    now(){
    return new Date(new Date().toString().split('GMT')[0]+' UTC').toISOString().split('.')[0]
},
  async submit() {
      if(this.tag === '' || this.from ==='' || this.to === '')
{
  this.$toasted.show(`Error: All Fields are required`, {type: 'error'})
  return
}
  this.isSaving = true
  let klicktipp =[ {
  tag: this.tag,
  from: this.from,
  to: this.to
}
  ]
  const {data} = await Nova.request().post(`/nova-vendor/novashopengine/klicktipps`, {
  value: klicktipp
})
  if (data.success) {
  this.value = data.data
  this.$router.push('/novashopengine/klicktipps');
  this.$toasted.show(`Gespeichert`, {type: 'success'})
}
  else {
  this.$toasted.show(`Error: ${data.data}`, {type: 'error'})
}
  this.isSaving = false
},
},
}
</script>