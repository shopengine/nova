<template>
  <div>
    <heading class="mb-6">Klicktipp zeitliche Tags</heading>
    <div class="flex" style="">
      <div class="w-full flex items-center">
        <div class="flex-no-shrink ml-auto mb-6">
          <router-link
              to="/novashopengine/shop/marketing-provider/klicktipp/period-tags/create"
              class="btn btn-default btn-primary"
              dusk="create-button"
          >
            Zeitliches Tag erstellen
          </router-link>
        </div>
      </div>
    </div>
    <loading-view :loading="isLoading">
      <div class="card" v-if="!periodTags.length">
        <div class="flex items-center py-3 border-b border-50">
          <div class="flex items-center ml-auto px-3">
            <div>
              <div class="filter-menu-dropdown">
                <div class="v-popover" dusk="filter-selector">
                  <div class="trigger" style="display: inline-block;">
                    <button type="button"
                            class="rounded active:outline-none active:shadow-outline focus:outline-none focus:shadow-outline">
                      <div
                          class="dropdown-trigger h-dropdown-trigger flex items-center cursor-pointer select-none bg-30 px-3 border-2 border-30 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                             aria-labelledby="filter" role="presentation" class="fill-current text-80">
                          <path fill-rule="nonzero"
                                d="M.293 5.707A1 1 0 0 1 0 4.999V1A1 1 0 0 1 1 0h18a1 1 0 0 1 1 1v4a1 1 0 0 1-.293.707L13 12.413v2.585a1 1 0 0 1-.293.708l-4 4c-.63.629-1.707.183-1.707-.708v-6.585L.293 5.707zM2 2v2.585l6.707 6.707a1 1 0 0 1 .293.707v4.585l2-2V12a1 1 0 0 1 .293-.707L18 4.585V2H2z"></path>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" class="ml-2">
                          <path fill="var(--90)"
                                d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path>
                        </svg>
                      </div>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="relative">
          <div class="flex justify-center items-center px-6 py-8" style="">
            <div class="text-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="65" height="51" viewBox="0 0 65 51" class="mb-3">
                <path fill="#A8B9C5"
                      d="M56 40h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1 1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1 1v2zm-5.364125-8H38v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H6c-3.313708 0-6-2.686292-6-6V6c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375C61.053323 31.5511 65 35.814652 65 41c0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM20 30h16v-8H20v8zm0 2v8h16v-8H20zm34-2v-8H38v8h16zM2 30h16v-8H2v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8H2zm18-12h16v-8H20v8zm34 0v-8H38v8h16zM2 20h16v-8H2v8zm52-10V6c0-2.209139-1.790861-4-4-4H6C3.790861 2 2 3.790861 2 6v4h52zm1 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z"></path>
              </svg>
              <h3 class="text-base text-80 font-normal">Keine Tags gefunden</h3>
            </div>
          </div>
        </div>
      </div>

      <card class="mb-4" v-if="periodTags.length">
        <div class="border-b border-40">
          <table class="table w-full">
            <tr align="left">
              <th>Id</th>
              <th>Name</th>
              <th>From</th>
              <th>To</th>
              <th></th>
            </tr>
            <tr v-for="periodTag in periodTags">
              <td>{{ periodTag.id }}</td>
              <td>{{ periodTag.name }}</td>
              <td>{{ formatDate(periodTag.from) }}</td>
              <td>{{ formatDate(periodTag.to) }}</td>
              <td class="td-fit text-right pr-6 align-middle">
                <div class="inline-flex items-center">
                    <span class="inline-flex">
                      <router-link
                          :to="'/novashopengine/shop/marketing-provider/klicktipp/period-tags/' + periodTag.tag"
                          class="inline-flex cursor-pointer text-70 hover:text-primary mr-3 has-tooltip"
                          data-testid="codepools-items-0-view-button"
                          dusk="6-view-button"
                          data-original-title="null"
                      >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="22"
                            height="18"
                            viewBox="0 0 22 16"
                            aria-labelledby="view"
                            role="presentation"
                            class="fill-current"
                        >
                          <path
                              d="M16.56 13.66a8 8 0 0 1-11.32 0L.3 8.7a1 1 0 0 1 0-1.42l4.95-4.95a8 8 0 0 1 11.32 0l4.95 4.95a1 1 0 0 1 0 1.42l-4.95 4.95-.01.01zm-9.9-1.42a6 6 0 0 0 8.48 0L19.38 8l-4.24-4.24a6 6 0 0 0-8.48 0L2.4 8l4.25 4.24h.01zM10.9 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"
                          ></path>
                        </svg>
                      </router-link>
                </span>
                  <span class="inline-flex">
                      <router-link
                          :to="'/novashopengine/shop/marketing-provider/klicktipp/period-tags/' + periodTag.tag + '/edit'"
                          class="inline-flex cursor-pointer text-70 hover:text-primary mr-3 has-tooltip"
                          dusk="6-edit-button"
                          data-original-title="null"
                          aria-describedby="tooltip_oie6k6eqj"
                      >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 20 20"
                            aria-labelledby="edit"
                            role="presentation"
                            class="fill-current"
                        >
                          <path
                              d="M4.3 10.3l10-10a1 1 0 0 1 1.4 0l4 4a1 1 0 0 1 0 1.4l-10 10a1 1 0 0 1-.7.3H5a1 1 0 0 1-1-1v-4a1 1 0 0 1 .3-.7zM6 14h2.59l9-9L15 2.41l-9 9V14zm10-2a1 1 0 0 1 2 0v6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2h6a1 1 0 1 1 0 2H2v14h14v-6z"
                          ></path></svg
                        ></router-link>

                <button
                    data-testid="pages-items-0-delete-button"
                    dusk="599-delete-button"
                    class="inline-flex appearance-none cursor-pointer text-70 hover:text-primary mr-3 has-tooltip"
                    data-original-title="null"
                    @click="activeModalFor(periodTag.tag)"
                >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 20 20"
                    aria-labelledby="delete"
                    role="presentation"
                    class="fill-current"
                >
                  <path
                      fill-rule="nonzero"
                      d="M6 4V2a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2h5a1 1 0 0 1 0 2h-1v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6H1a1 1 0 1 1 0-2h5zM4 6v12h12V6H4zm8-2V2H8v2h4zM8 8a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1z"
                  ></path>
                </svg>
              </button>
            </span>
                </div>
              </td>
            </tr>
          </table>
        </div>
      </card>
    </loading-view>

    <div
        class="fixed pin bg-80 z-20 opacity-75"
        :class="{ hidden: !isModalActive }"
        @click="isModalActive=false"
    ></div>
    <div
        class="modal select-none fixed pin z-50 overflow-x-hidden overflow-y-auto top-0"
        :class="{ hidden: !isModalActive }"
    >
      <div class="relative mx-auto flex justify-center z-20 py-view">
        <div>
          <form
              class="bg-white rounded-lg shadow-lg overflow-hidden"
              style="width: 460px"
              @submit.prevent="deleteTag()"
          >
            <div class="p-8">
              <h2 class="mb-6 text-90 font-normal text-xl">Klicktipp löschen</h2>
              <p class="text-80 leading-normal">Möchtest du den Tag wirklich löschen?</p>
            </div>
            <div class="bg-30 px-6 py-3 flex">
              <div class="ml-auto">
                <button
                    type="button"
                    data-testid="cancel-button"
                    dusk="cancel-delete-button"
                    class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
                    @click="isModalActive=false"
                >Abbrechen
                </button>
                <button
                    id="confirm-delete-button"
                    data-testid="confirm-button"
                    type="submit"
                    class="btn btn-default btn-danger"
                >
                  <span class="">Löschen</span>
                </button>
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
      periodTags: {},
      isModalActive: false,
      tagToDelete: "",
    };
  },
  async mounted() {
    this.isLoading = true;

    Nova.request()
        .get('/nova-vendor/novashopengine/shop/marketing-provider/klicktipp/period-tags')
        .then(response => {
          this.periodTags = response.data
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
    },
    activeModalFor(tag) {
      this.tagToDelete = tag;
      this.isModalActive = true;
    },

    async deleteTag(tag) {
      this.isSaving = true;

      Nova.request()
          .post('/nova-vendor/novashopengine/shop/marketing-provider/klicktipp/period-tags/' + this.tagToDelete + '/delete')
          .then(response => {
            this.isModalActive = false;
            this.periodTags = data.data;
            Nova.success('Gelöscht')
          })
          .catch((error) => {
          })
          .finally(() => {
            this.isSaving = false
          });
    },
  },
};
</script>
