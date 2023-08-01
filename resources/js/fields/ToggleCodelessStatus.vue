<template>
  <div class="inline-flex items-center">
    <label class="switch mr-3">
      <input type="checkbox" v-model="checked" @change="toggleCodelessStatus(aggregateId)">
      <span class="slider round"></span>
    </label>
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
  props: ["resourceName", "field"],
  computed: {
    status() {
      return this.field.value;
    },
    checked() {
      return this.field.value === "enabled";
    },
    aggregateId() {
      return this.field.aggregateId;
    },
  },
  methods: {
    toggleStatusButton() {
      this.field.value = this.field.value === "enabled" ? "disabled" : "enabled";
    },
    async toggleCodelessStatus(aggregateId) {
      Nova.request()
        .patch("/nova-vendor/novashopengine/codeless/toggle-status", {
          codeless: {
            aggregateId,
          },
        })
        .then((response) => {
          if (response.data.success) {
            Nova.success("Updated");
            this.toggleStatusButton()
            location.reload();
          } else {
            Nova.error("Something went wrong");
          }
        });
    },
  },
};
</script>
