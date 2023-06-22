<template>
  <div>
    <heading class="mb-6">Codeless</heading>
    <loading-view :loading="isLoading">
      <div class="card" v-if="!codelessCollection.length">
        <div class="flex items-center py-3 border-b border-50">
          <div class="flex items-center"><!----></div>
          <div class="flex items-center ml-auto px-3">
            <div>
              <div class="filter-menu-dropdown">
                <div class="v-popover" dusk="filter-selector">
                  <div class="trigger" style="display: inline-block">
                    <button type="button"
                      class="rounded active:outline-none active:shadow-outline focus:outline-none focus:shadow-outline">
                      <div
                        class="dropdown-trigger h-dropdown-trigger flex items-center cursor-pointer select-none bg-30 px-3 border-2 border-30 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                          aria-labelledby="filter" role="presentation" class="fill-current text-80">
                          <path fill-rule="nonzero"
                            d="M.293 5.707A1 1 0 0 1 0 4.999V1A1 1 0 0 1 1 0h18a1 1 0 0 1 1 1v4a1 1 0 0 1-.293.707L13 12.413v2.585a1 1 0 0 1-.293.708l-4 4c-.63.629-1.707.183-1.707-.708v-6.585L.293 5.707zM2 2v2.585l6.707 6.707a1 1 0 0 1 .293.707v4.585l2-2V12a1 1 0 0 1 .293-.707L18 4.585V2H2z">
                          </path>
                        </svg>
                        <!---->
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" class="ml-2">
                          <path fill="var(--90)"
                            d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z">
                          </path>
                        </svg>
                      </div>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!---->
          </div>
        </div>
        <div class="relative">
          <div class="flex justify-center items-center px-6 py-8">
            <div class="text-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="65" height="51" viewBox="0 0 65 51" class="mb-3">
                <path fill="#A8B9C5"
                  d="M56 40h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1 1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1 1v2zm-5.364125-8H38v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H6c-3.313708 0-6-2.686292-6-6V6c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375C61.053323 31.5511 65 35.814652 65 41c0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM20 30h16v-8H20v8zm0 2v8h16v-8H20zm34-2v-8H38v8h16zM2 30h16v-8H2v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8H2zm18-12h16v-8H20v8zm34 0v-8H38v8h16zM2 20h16v-8H2v8zm52-10V6c0-2.209139-1.790861-4-4-4H6C3.790861 2 2 3.790861 2 6v4h52zm1 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z">
                </path>
              </svg>
              <h3 class="text-base text-80 font-normal">No Codeless found.</h3>
              <!---->
            </div>
          </div>
        </div>
      </div>

      <card class="mb-4" v-if="codelessCollection.length">
        <div class="border-b border-40">
          <div class="py-6 px-8">
            <table class="table w-full">
              <tr align="left">
                <th>Name</th>
                <th>Status</th>
                <th>From</th>
                <th>To</th>
                <th></th>
              </tr>
              <tr v-for="codeless in codelessCollection">
                <td>{{ codeless.name }}</td>
                <td v-if="codeless.status === 'enabled'">
                  <span
                    class="whitespace-no-wrap px-2 py-1 rounded-full uppercase text-xs font-bold bg-success-light text-success-dark">
                    enabled
                  </span>
                </td>

                <td v-if="codeless.status === 'disabled'">
                  <span
                    class="whitespace-no-wrap px-2 py-1 rounded-full uppercase text-xs font-bold bg-danger-light text-danger-dark">
                    disabled
                  </span>
                </td>
                <td>{{ formatDate(codeless.start) }}</td>
                <td>{{ formatDate(codeless.end) }}</td>
                <td class="td-fit text-right pr-6 align-middle">



                  <div class="inline-flex items-center">
                    <label class="switch mr-3">
                      <input type="checkbox" :checked="codeless.status === 'enabled'"
                        @click="toggleCodelessStatus(codeless.aggregateId)">
                      <span class="slider round"></span>
                    </label>
                    <span class="inline-flex">
                      <router-link :to="'/novashopengine/codeless/' + codeless.aggregateId"
                        class="inline-flex cursor-pointer text-70 hover:text-primary mr-3 has-tooltip">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="18" viewBox="0 0 22 16"
                          aria-labelledby="view" role="presentation" class="fill-current">
                          <path
                            d="M16.56 13.66a8 8 0 0 1-11.32 0L.3 8.7a1 1 0 0 1 0-1.42l4.95-4.95a8 8 0 0 1 11.32 0l4.95 4.95a1 1 0 0 1 0 1.42l-4.95 4.95-.01.01zm-9.9-1.42a6 6 0 0 0 8.48 0L19.38 8l-4.24-4.24a6 6 0 0 0-8.48 0L2.4 8l4.25 4.24h.01zM10.9 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z">
                          </path>
                        </svg>
                      </router-link>
                    </span>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </card>
    </loading-view>
  </div>
</template>


<style scoped>
/* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 49px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 15px;
  left: 4px;
  bottom: 4px;
  top: 1px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked+.slider {
  background-color: #38a169;
}

input:focus+.slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked+.slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>


<script>
export default {
  data() {
    return {
      isLoading: false,
      codelessCollection: {},
    };
  },
  async mounted() {
    this.isLoading = true;
    this.getCodelessCollection()
      .catch((error) => {
        this.$router.go(-1);
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    formatDate(date) {
      return moment(date).format("DD.MM.YYYY HH:mm:ss", "de");
    },
    async getCodelessCollection() {
      Nova.request()
        .get(`/nova-vendor/novashopengine/codeless`)
        .then((response) => {
          this.codelessCollection = response.data
        })
    },
    async toggleCodelessStatus(aggregateId) {
      this.isSaving = true;

      Nova.request()
        .patch('/nova-vendor/novashopengine/codeless/toggle-status', {
          codeless: {
            aggregateId
          },
        })
        .then(response => {
          Nova.success("Updated");
          this.getCodelessCollection();
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


