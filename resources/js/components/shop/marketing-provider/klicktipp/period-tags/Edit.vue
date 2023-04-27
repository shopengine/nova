<template>
  <div data-testid="content" class="px-view py-view mx-auto">
    <div
        data-v-8affbeb6=""
        class="progress"
        style="
        width: 0%;
        height: 3px;
        opacity: 0;
        background-color: var(--primary);
      "
    ></div>
    <div class="relative">
      <div class="mb-3"></div>
      <form autocomplete="off">
        <div class="mb-8">
          <h1 class="text-90 font-normal text-2xl mb-3">
            Zeitliche Tags bearbeiten:
          </h1>
          <!---->
          <div class="card">
            <div class="flex border-b border-40">
              <div class="px-8 py-6 w-1/5">
                <label
                    htmlFor="class"
                    class="inline-block text-80 pt-2 leading-tight"
                >
                  Tag <span class="text-danger text-sm">*</span></label
                >
              </div>
              <div class="py-6 px-8 w-1/2">
                <input
                    id="class"
                    dusk="tag"
                    list="tag-list"
                    type="number"
                    placeholder="Tag"
                    class="w-full form-control form-input form-input-bordered"
                    v-model="periodTag.tag"
                    disabled="disabled"
                />
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
                    id="class"
                    dusk="tag"
                    list="tag-list"
                    type="datetime-local"
                    placeholder="beginnt am"
                    step="any"
                    class="w-full form-control form-input form-input-bordered"
                    v-model="periodTag.from"
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
                    id="class"
                    dusk="tag"
                    list="tag-list"
                    type="datetime-local"
                    placeholder="endet am"
                    step="any"
                    class="w-full form-control form-input form-input-bordered"
                    v-model="periodTag.to"
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
            <span class="">Zeitlichen Tag aktualisieren</span>
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
      tag: this.$route.params.tag,
      periodTag: {},
    };
  },
  async mounted() {
    this.isLoading = true;
    const {data} = await Nova.request().get(
        `/nova-vendor/novashopengine/shop/marketing-provider/klicktipp/period-tags/` + this.tag
    );
    if (data.hasOwnProperty('notApplicable')) {
      this.$toasted.show(`Error: ${data.message}`, {type: "error"});
      this.$router.go(-1);
      return;
    }
    this.periodTag = data;
    this.isLoading = false;
  },

  methods: {
    async submit(e) {
      this.isSaving = true;
      const {data} = await Nova.request().patch(
          `/nova-vendor/novashopengine/shop/marketing-provider/klicktipp/period-tags`,
          {
            periodTag: this.periodTag,
          }
      );
      if (data.success) {
        this.value = data.data;
        this.$router.push(
            "/novashopengine/shop/marketing-provider/klicktipp/period-tags"
        );
        this.$toasted.show(`${data.message}`, {type: "success"});
      } else {
        this.$toasted.show(`Error: ${data.message}`, {type: "error"});
      }
      this.isSaving = false;
    },
  },
};
</script>
<template>
  <div data-testid="content" class="px-view py-view mx-auto">
    <div
        data-v-8affbeb6=""
        class="progress"
        style="
        width: 0%;
        height: 3px;
        opacity: 0;
        background-color: var(--primary);
      "
    ></div>
    <div class="relative">
      <div class="mb-3"></div>
      <form autocomplete="off">
        <div class="mb-8">
          <h1 class="text-90 font-normal text-2xl mb-3">
            Zeitlichen Tag bearbeiten:
          </h1>
          <!---->
          <div class="card">
            <div class="flex border-b border-40">
              <div class="px-8 py-6 w-1/5">
                <label
                    htmlFor="class"
                    class="inline-block text-80 pt-2 leading-tight"
                >
                  Tag <span class="text-danger text-sm">*</span></label
                >
              </div>
              <div class="py-6 px-8 w-1/2">
                <input
                    id="class"
                    dusk="tag"
                    list="tag-list"
                    type="number"
                    placeholder="Tag"
                    class="w-full form-control form-input form-input-bordered"
                    v-model="periodTag.tag"
                    disabled="disabled"
                />
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
                    id="class"
                    dusk="tag"
                    list="tag-list"
                    type="datetime-local"
                    placeholder="beginnt am"
                    step="any"
                    class="w-full form-control form-input form-input-bordered"
                    v-model="periodTag.from"
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
                    id="class"
                    dusk="tag"
                    list="tag-list"
                    type="datetime-local"
                    placeholder="endet am"
                    step="any"
                    class="w-full form-control form-input form-input-bordered"
                    v-model="periodTag.to"
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
            <span class="">Zeitlichen Tag aktualisieren</span>
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
      tag: this.$route.params.tag,
      periodTag: {},
    };
  },
  async mounted() {
    this.isLoading = true;
    const {data} = await Nova.request().get(
        `/nova-vendor/novashopengine/shop/marketing-provider/klicktipp/period-tags/` +
        this.tag
    );
    this.periodTag = data;
    this.isLoading = false;
  },

  methods: {
    async submit(e) {
      this.isSaving = true;
      const {data} = await Nova.request().patch(
          `/nova-vendor/novashopengine/shop/marketing-provider/klicktipp/period-tags`,
          {
            periodTag: this.periodTag,
          }
      );
      if (data.success) {
        this.value = data.data;
        this.$router.push(
            "/novashopengine/shop/marketing-provider/klicktipp/period-tags"
        );
        this.$toasted.show(`${data.message}`, {type: "success"});
      } else {
        this.$toasted.show(`Error: ${data.message}`, {type: "error"});
      }
      this.isSaving = false;
    },
  },
};
</script>
