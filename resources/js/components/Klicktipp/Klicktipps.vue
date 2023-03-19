<template>
  <div>
    <heading class="mb-6">Klicktipps</heading>
    <div class="flex" style="">
      <div class="relative h-9 flex-no-shrink mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="search"
             role="presentation" class="fill-current absolute search-icon-center ml-3 text-70">
          <path fill-rule="nonzero"
                d="M14.32 12.906l5.387 5.387a1 1 0 0 1-1.414 1.414l-5.387-5.387a8 8 0 1 1 1.414-1.414zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"></path>
        </svg>
        <input data-testid="search-input" dusk="search" placeholder="Search" type="search" spellCheck="false"
               class="appearance-none form-search w-search pl-search shadow"></div>
      <div class="w-full flex items-center"><!---->
        <div class="flex-no-shrink ml-auto mb-6"><router-link
            to="/novashopengine/klicktipps/create"
            class="btn btn-default btn-primary" dusk="create-button">
          Klicktipp erstellen
        </router-link></div>
      </div>
    </div>
    <loading-view
    :loading="isLoading">
    <card class="mb-4">
      <div class="border-b border-40">
        <div class="py-6 px-8">
          <table class="table w-full">
            <tr align="left">
              <th>Tag</th>
              <th>From</th>
              <th>To</th>
              <th></th>
            </tr>
            <tr v-for="period in tagPeriods">
              <td>{{period.tag}}</td>
              <td>{{period.from}}</td>
              <td>{{period.to}}</td>
              <td class="td-fit text-right pr-6 align-middle">
                <div class="inline-flex items-center">
                    <span class="inline-flex">
                      <router-link
                        :to="'klicktipps/' + period.tag"
                        class="inline-flex cursor-pointer text-70 hover:text-primary mr-3 has-tooltip"
                        data-testid="codepools-items-0-view-button"
                        dusk="6-view-button"
                        data-original-title="null">
                      <svg
                          xmlns="http://www.w3.org/2000/svg"
                          width="22"
                          height="18"
                          viewBox="0 0 22 16"
                          aria-labelledby="view"
                          role="presentation"
                          class="fill-current">
                          <path
                              d="M16.56 13.66a8 8 0 0 1-11.32 0L.3 8.7a1 1 0 0 1 0-1.42l4.95-4.95a8 8 0 0 1 11.32 0l4.95 4.95a1 1 0 0 1 0 1.42l-4.95 4.95-.01.01zm-9.9-1.42a6 6 0 0 0 8.48 0L19.38 8l-4.24-4.24a6 6 0 0 0-8.48 0L2.4 8l4.25 4.24h.01zM10.9 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"
                          >

                          </path>
                      </svg
                      >
                      </router-link>
                </span>
                <span class="inline-flex">
                      <router-link
                  :to="'klicktipps/'+ period.tag +'/edit'"
                        class="inline-flex cursor-pointer text-70 hover:text-primary mr-3 has-tooltip"
                        dusk="6-edit-button"
                        data-original-title="null"
                        aria-describedby="tooltip_oie6k6eqj">

                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="20"
                      height="20"
                      viewBox="0 0 20 20"
                      aria-labelledby="edit"
                      role="presentation"
                      class="fill-current">
                          <path
                              d="M4.3 10.3l10-10a1 1 0 0 1 1.4 0l4 4a1 1 0 0 1 0 1.4l-10 10a1 1 0 0 1-.7.3H5a1 1 0 0 1-1-1v-4a1 1 0 0 1 .3-.7zM6 14h2.59l9-9L15 2.41l-9 9V14zm10-2a1 1 0 0 1 2 0v6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2h6a1 1 0 1 1 0 2H2v14h14v-6z"
                          ></path>
                  </svg
                  ></router-link>

                <button data-testid="pages-items-0-delete-button" dusk="599-delete-button"
                        class="inline-flex appearance-none cursor-pointer text-70 hover:text-primary mr-3 has-tooltip"
                        data-original-title="null"
                        @click="activeModalFor(period.tag)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                       aria-labelledby="delete" role="presentation" class="fill-current">
                    <path fill-rule="nonzero"
                          d="M6 4V2a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2h5a1 1 0 0 1 0 2h-1v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6H1a1 1 0 1 1 0-2h5zM4 6v12h12V6H4zm8-2V2H8v2h4zM8 8a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1z"></path>
                  </svg>
                </button>
              </span>
        </div>
      </td>
    </tr>
  </table>
</div>
</div>
</card>
</loading-view>

<div class="fixed pin bg-80 z-20 opacity-75 " :class="{ hidden: !isModalActive }" @click="isModalActive=false"></div>
<div class="modal select-none fixed pin z-50 overflow-x-hidden overflow-y-auto top-0" :class="{ hidden: !isModalActive }">
  <div class="relative mx-auto flex justify-center z-20 py-view">
    <div>
      <form class="bg-white rounded-lg shadow-lg overflow-hidden" style="width: 460px;" :action="'/nova/klicktipps/' + tagToDelete +'/delete'" @submit.prevent="deleteTag()">
        <div class="p-8"><h2 class="mb-6 text-90 font-normal text-xl">Delete Klicktipp</h2> <p
            class="text-80 leading-normal">
          Are you sure you want to delete this Tag?
        </p></div>
        <div class="bg-30 px-6 py-3 flex">
          <div class="ml-auto">
            <button type="button" data-testid="cancel-button" dusk="cancel-delete-button"
                    class="btn text-80 font-normal h-9 px-3 mr-3 btn-link" @click="isModalActive=false">
              Cancel
            </button>
            <button id="confirm-delete-button" data-testid="confirm-button" type="submit"
                    class="btn btn-default btn-danger"><span class="">
          Delete
        </span> <!----></button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
</template>

<script>
  export default {
  data() {
    return {
      isLoading: true,
      isSaving: false,
      data: {},
      tagPeriods: {},
      tags: {},
      tagsCodes: {},
      isModalActive: false,
      tagToDelete: ''
  }
},
  async mounted() {
    this.isLoading = true
    const {data} = await Nova.request().get(`/nova-vendor/novashopengine/klicktipps`);
    this.data = data
    this.tags = data.tags_periods.tags
    this.tagPeriods = data.tags_periods
    this.isLoading = false
},

   methods: {
    activeModalFor(tag){
      this.tagToDelete = tag;
      this.isModalActive = true
},

  async deleteTag()
{
  this.isSaving = true
  const {data} = await Nova.request().get(`/nova-vendor/novashopengine/klicktipps/${this.tagToDelete}/delete`);
  if(data) {
  this.$toasted.show(`Gelöscht`, {type: 'success'})
    this.isModalActive = false;
  console.log(data);
  this.tagPeriods = data
  // this.$router.go(this.$router.currentRoute)


}
  else {
  this.$toasted.show(`Error: ${data.data}`, {type: 'error'})
}

  this.isSaving = false

}
}
}


</script>
