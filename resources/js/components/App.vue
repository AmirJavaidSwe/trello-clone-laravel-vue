<template>
  <div>
    <div class="container mt-10">
      <vue-element-loading
        :active="show"
        :is-full-screen="true"
        spinner="bar-fade-scale"
        color="#FF6700"
      />
      <notifications position="bottom right" width="400px" />
      <template v-if="validate">
        <template v-for="(obj, i) in columns" &&>
          <div class="columns cards" :key="obj.key" v-if="obj">
            <Columns
              :data="obj"
              :index="i"
              :list="obj.cards"
              v-on:update:List="handleListUpdate($event, i)"
              v-on:update:Sorting="handleSortingUpdate($event)"
              v-on:update:hideLoader="hideLoader"
            ></Columns>
          </div>
        </template>
      </template>
      <button class="btn btn-primary" @click="addColumn">
        {{ columns.length > 0 ? "Add Another List" : "Add List" }}
      </button>
      <div class="db-dumper">
        <a class="db-link" target="_blank" href="/dumpdb">Dump DB</a>
      </div>
    </div>
  </div>
</template>

<script>
import Columns from "./Columns.vue";
import draggable from "vuedraggable";
export default {
  name: "App",
  components: {
    Columns,
    draggable,
  },
  computed: {
    dragOptions() {
      return {
        animation: 0,
        group: "description",
        disabled: false,
        ghostClass: "ghost",
      };
    },
  },
  data() {
    return {
      show: false,
      columns: [],
      validate: true,
    };
  },
  methods: {
    hideLoader(param) {
      this.show = param;
    },
    async handleSortingUpdate(data) {
      this.validate = false;
      data = JSON.parse(data);
      await this.$nextTick();
      this.columns = data;
      await this.$nextTick();
      this.validate = true;
    },
    async handleListUpdate(data, i) {
      this.validate = false;
      data = JSON.parse(data);
      if (data.action == "delete") {
        await this.$nextTick();
        this.columns.splice(i, 1);
        await this.$nextTick();
        this.validate = true;
      } else {
        if (data.action == "update") {
          this.validate = false;
          await this.$nextTick();
          this.columns.splice(i, 1, data.data);
          await this.$nextTick();
          this.validate = true;
        } else {
          this.validate = false;
          await this.$nextTick();
          this.columns.pop();
          await this.$nextTick();
          this.columns.push(data.data);
          this.validate = true;
        }
      }
    },
    addColumn() {
      let data = {
        editableTitle: true,
        title: null,
        cards: [],
      };
      this.columns.push(data);
    },
    async getData() {
      this.validate = false;
      this.columns = [];
      this.hideLoader(true);
      await axios
        .get("api/column")
        .then((res) => {
          this.columns = res.data.data;
          this.hideLoader(false);
          this.validate = true;
        })
        .catch((err) => alert("Error Fetching Data! Please Refersh the Page"))
    },
  },
  mounted: function () {
    this.getData();
  },
};
</script>