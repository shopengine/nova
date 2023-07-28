<template>
  <div data-testid="content" class="px-view py-view mx-auto">
    <div
        data-v-8affbeb6=""
        class="progress"
        style="width: 0%;height: 3px;opacity: 0;background-color: var(--primary);"
    ></div>
    <div class="relative">
      <div class="mb-3"></div>
      <form autocomplete="off">
        <div class="mb-8">
          <h1 class="text-90 font-normal text-2xl mb-3">
            Zeitlichen Tag erstellen:
          </h1>
          <!---->
          <div class="card">
            <div class="flex border-b border-40">
              <div class="px-8 py-6 w-1/5">
                <label
                    htmlFor="class"
                    class="inline-block text-80 pt-2 leading-tight"
                >
                  Tag <span class="text- danger text-sm">*</span></label
                >
              </div>
              <div class="py-6 px-8 w-1/2">
                <SearchableDropDown
                    :options="tagOptions"
                    v-on:selected="setTag"
                    :disabled="false"
                    placeholder="Please select an option">
                </SearchableDropDown>
                <div class="text-xs my-4">Tag Id: <span v-html="tag" class="font-bold"></span></div>
              </div>
            </div>
            <div class="flex border-b border-40">
              <div class="px-8 py-6 w-1/5">
                <label
                    htmlFor="class"
                    class="inline-block text-80 pt-2 leading-tight"
                >
                  From&nbsp;<span class="text-danger text-sm">*</span></label
                >
              </div>
              <div class="py-6 px-8 w-1/2">
                <input
                    type="datetime-local"
                    placeholder="Tag"
                    step="any"
                    class="w-full form-control form-input form-input-bordered"
                    v-model="from"
                />
              </div>
            </div>
            <div class="flex border-b border-40">
              <div class="px-8 py-6 w-1/5">
                <label
                    htmlFor="class"
                    class="inline-block text-80 pt-2 leading-tight"
                >
                  To&nbsp;<span class="text-danger text-sm">*</span></label
                >
              </div>
              <div class="py-6 px-8 w-1/2">
                <input
                    type="datetime-local"
                    placeholder="Tag"
                    step="any"
                    class="w-full form-control form-input form-input-bordered"
                    v-model="to"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center">
          <router-link
              to="/novashopengine/shop/marketing-provider/klicktipp/period-tags"
              tabIndex="0"
              class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6"
          >
            Cancel
          </router-link>
          <button
              type="button"
              class="btn btn-default btn-primary inline-flex items-center relative"
              dusk="update-button"
              @click="submit"
          >
            <span class="">Klicktipp erstellen</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import SearchableDropDown from "./Components/Dropdown.vue";
export default {
  components: {
    SearchableDropDown
  },
  data() {
    return {
      isLoading: true,
      isSaving: false,
      tagOptions: [],
      tag: "",
      from: this.now(),
      to: this.tomorrow(),
    };
  },

  async mounted() {
    Nova.request()
        .get('/nova-vendor/novashopengine/shop/marketing-provider/klicktipp/period-tags/options')
        .then((response) => {
          this.tagOptions = Object.entries(response.data).map(([id, name]) => ({ id, name }))
        })
  },

  methods: {
    setTag(tag) {
      this.tag = tag.id
    },
    now() {
      return moment().format(moment.HTML5_FMT.DATETIME_LOCAL)
    },
    tomorrow() {
      return moment().add(1, 'days').format(moment.HTML5_FMT.DATETIME_LOCAL)
    },
    async submit() {
      if (this.tag === "" || this.from === "" || this.to === "") {
        this.$toasted.show(`Error: All Fields are required`, {
          type: "error",
        });
        return;
      }

      this.isSaving = true;

      Nova.request()
          .post('/nova-vendor/novashopengine/shop/marketing-provider/klicktipp/period-tags', {
            periodTag: {
              tag: this.tag,
              from: moment(this.from).toISOString(),
              to: moment(this.to).toISOString(),
            },
          })
          .then(response => {
            this.$router.push(
                "/novashopengine/shop/marketing-provider/klicktipp/period-tags"
            );
          })
          .catch((error) => {
            this.$router.go(-1);
          })
          .finally(() => {
            this.isSaving = false
          });
    },
  },
};
</script>
