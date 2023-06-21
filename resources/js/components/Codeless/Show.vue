<template>
  <div>
    <loading-view :loading="isLoading">
    <div dusk="codeless-detail-component" class="mb-8">
      <div>
        <div class="flex items-center mb-3">
          <h1 class="mb-3 text-90 font-normal text-2xl">
            <a href="/nova/novashopengine/codeless"
               class="no-underline text-primary font-bold dim router-link-active"
               data-testid="lens-back">←</a> <span
              class="px-2 text-70">/</span> Codeless Details
          </h1>
        </div>
        <div class="card mb-6 py-3 px-6">
          <div class="flex border-b border-40 -mx-6 px-6">
            <div class="w-1/4 py-4"><h4 class="font-normal text-80">Name</h4></div>
            <div class="w-3/4 py-4 break-words">
              <p class="text-90">
              {{ codeless.name }}
            </p>
            </div>
          </div>

          <div class="flex border-b border-40 -mx-6 px-6">
            <div class="w-1/4 py-4"><h4 class="font-normal text-80">Start</h4></div>
            <div class="w-3/4 py-4 break-words">
              <div>
                <div class="markdown leading-normal whitespace-pre-wrap">{{ formatDate(codeless.start) }}</div>
              </div>
            </div>
          </div>

          <div class="flex border-b border-40 -mx-6 px-6">
            <div class="w-1/4 py-4"><h4 class="font-normal text-80">End</h4></div>
            <div class="w-3/4 py-4 break-words">
              <div>
                <div class="markdown leading-normal whitespace-pre-wrap">{{ formatDate(codeless.end) }}</div>
              </div>
            </div>
          </div>

          <div class="flex border-b border-40 -mx-6 px-6">
            <div class="w-1/4 py-4"><h4 class="font-normal text-80">Note</h4></div>
            <div class="w-3/4 py-4 break-words">
              <div>
                <div class="markdown leading-normal whitespace-pre-wrap">{{ codeless.note }}</div>
              </div>
            </div>
          </div>

          <div class="flex border-b border-40 -mx-6 px-6">
            <div class="w-1/4 py-4"><h4 class="font-normal text-80">Status</h4></div>
            <div class="w-3/4 py-4 break-words">
              <div>
              <span v-if="codeless.status === 'enabled'"
                    class="whitespace-no-wrap px-2 py-1 rounded-full uppercase text-xs font-bold bg-success-light text-success-dark">
                enabled
              </span>

                <span v-if="codeless.status === 'disabled'"
                      class="whitespace-no-wrap px-2 py-1 rounded-full uppercase text-xs font-bold bg-danger-light text-danger-dark">
                  disabled
                </span>

              </div>
            </div>
          </div>

        </div>
        <div class="w-full flex justify-end">
          <button
          @click="toggleCodelessStatus" type="submit" class="btn btn-default btn-primary inline-flex items-end relative"
          dusk="update-button">
          <span class="" v-if="enabled">
                Disable Codeless
               </span>
          <span class="" v-else>
                Enable Codeless
               </span>

        </button>
      </div>
    </div>
  </div>
</loading-view>
</div>
</template>


<script>
  export default {
  data() {
  return {
  isLoading: false,
  codeless: {},
  id: this.$route.params.id,
};
},
  computed: {
  enabled() {
  if(this.codeless.status === 'enabled') return true;
  return false;
}
},
  async mounted() {
  this.getCodeless()
},
  methods: {
  getCodeless(){
  this.isLoading = true

  Nova.request()
  .get(`/nova-vendor/novashopengine/codeless/` + this.id)
  .then(response => {
  this.codeless = response.data

})
  .catch((error) => {
  console.log(error)
})
  .finally(() => {
  this.isLoading = false
});
},
  formatDate(date) {
  return moment(date).format('DD.MM.YYYY HH:mm:ss', 'de')
},

  async toggleCodelessStatus(e) {
  this.isSaving = true;

  Nova.request()
  .patch('/nova-vendor/novashopengine/codeless', {
  codeless: {
  aggregateId: this.id
},
})
  .then(response => {
  this.codeless = response.data
  this.$router.push(
  "/novashopengine/codeless?update=success"
  );
})
  .catch((error) => {
})
  .finally(() => {
  this.isSaving = false
});
},
}
};
</script>
