<template>
  <div>
    <loading-view
        :loading="isLoading">

      <div dusk="codepools-detail-component" class="mb-8">
        <div>
          <div class="flex items-center mb-3"><h1
              class="flex-auto truncate text-90 font-normal text-2xl">Zeitliches Tag Details:</h1>
            <div class="ml-3 flex items-center">
              <div class="flex w-full justify-end items-center"></div>
              <div class="ml-3">
                <div class="v-portal" transition="fade-transition" style="display: none;"></div>
              </div>
              <router-link
                  :to="'/novashopengine/shop/marketing-provider/klicktipp/period-tags/' + periodTag.tag + '/edit'"
                  class="btn btn-default btn-icon bg-primary"
                  data-testid="edit-resource" dusk="edit-resource-button" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                     aria-labelledby="edit"
                     role="presentation" class="fill-current text-white" style="margin-top: -2px; margin-left: 3px;">
                  <path
                      d="M4.3 10.3l10-10a1 1 0 0 1 1.4 0l4 4a1 1 0 0 1 0 1.4l-10 10a1 1 0 0 1-.7.3H5a1 1 0 0 1-1-1v-4a1 1 0 0 1 .3-.7zM6 14h2.59l9-9L15 2.41l-9 9V14zm10-2a1 1 0 0 1 2 0v6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2h6a1 1 0 1 1 0 2H2v14h14v-6z"></path>
                </svg>
              </router-link>
            </div>
          </div>
          <div class="card mb-6 py-3 px-6">
            <div class="flex border-b border-40 -mx-6 px-6">
              <div class="w-1/4 py-4"><h4 class="font-normal text-80">Id</h4></div>
              <div class="w-3/4 py-4 break-words">
                <p class="text-90">{{ periodTag.id }}</p>
              </div>
            </div>

            <div class="flex border-b border-40 -mx-6 px-6">
              <div class="w-1/4 py-4"><h4 class="font-normal text-80">Name</h4></div>
              <div class="w-3/4 py-4 break-words">
                <p class="text-90">{{ periodTag.name }}</p>
              </div>
            </div>

            <div class="flex border-b border-40 -mx-6 px-6">
              <div class="w-1/4 py-4"><h4 class="font-normal text-80">From</h4></div>
              <div class="w-3/4 py-4 break-words">
                <div>
                  <div class="markdown leading-normal whitespace-pre-wrap">{{ formatDate(periodTag.from) }}</div>
                </div>
              </div>
            </div>

            <div class="flex border-b border-40 -mx-6 px-6">
              <div class="w-1/4 py-4"><h4 class="font-normal text-80">To</h4></div>
              <div class="w-3/4 py-4 break-words">
                <div>
                  <div class="markdown leading-normal whitespace-pre-wrap">{{ formatDate(periodTag.to) }}</div>
                </div>
              </div>
            </div>
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
      isLoading: true,
      isSaving: false,
      tag: this.$route.params.tag,
      periodTag: {},
    }
  },
  async mounted() {
    this.isLoading = true

    Nova.request()
        .get(`/nova-vendor/novashopengine/shop/marketing-provider/klicktipp/period-tags/` + this.tag)
        .then(response => {
          this.periodTag = response.data
        })
        .catch((error) => {
          this.$router.go(-1)
        })
        .finally(() => {
          this.isLoading = false
        });
  },
  methods: {
    formatDate(date) {
      return moment(date).format('DD.MM.YYYY HH:mm:ss', 'de')
    }
  }
}
</script>
