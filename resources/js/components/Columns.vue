<template>
  <div>
    <div class="column-header">
      <div>
        <input
          type="text"
          :id="'column-title' + column_index"
          class="form-control"
          v-model="column.title"
          v-show="column.editableTitle"
          @keyup.enter="showList"
          @blur="showList"
        />
        <div
          class="column-title"
          v-html="column.title ? column.title : 'Untitled'"
          v-show="!column.editableTitle"
          @click="editList"
        ></div>
        <div
          v-show="!column.editableTitle"
          class="delete-list"
          @click="deleteList(column.id)"
          title="Delete List"
        >
          <i class="fa fa-trash"></i>
        </div>
      </div>
    </div>
    <br />

    <template v-if="validateCard">
      <draggable
        class="dragArea"
        tag="div"
        :sort="true"
        :list="list"
        :group="{ name: 'cards' }"
        @change="handleChange"
        
        
      >
        <template v-for="(card, index) in column.cards">
          <Cards
            v-if="card"
            v-bind:key="index"
            v-bind:data="card"
            v-bind:index="index"
            v-bind:parent_index="column_index"
            v-on:update:Parent="handleCardsUpdate($event, index)"
            v-on:update:hideLoader="hideLoader"
          ></Cards>
        </template>
      </draggable>
    </template>

    <div class="add-card">
      <span class="btn btn-success" @click="addCard(column_index)">
        <i class="fa fa-plus"></i> Add a card
      </span>
      <span
        class="remove-span"
        title="Remove"
        v-show="showCencelCardButton"
        @click="removeCard(true)"
      >
        <i class="fa fa-times" aria-hidden="true"></i>
      </span>
    </div>
  </div>
</template>

<script>
import Cards from "./Cards.vue";
import draggable from "vuedraggable";
export default {
  name: "Columns",
  components: {
    Cards,
    draggable,
  },
  props: ["data", "index", "list"],
  data() {
    return {
      column: this.data,
      column_index: this.index,
      validateCard: true,
      // draggable: this.list
    };
  },
  methods: {
    async checkMove(e) {
      // window.console.log("Future index: " + e.draggedContext.futureIndex);
    },
    async handleChange(e) {
      this.removeCard(false);
      await this.$nextTick();
      let data = {
        column_id: this.column.id,
        data: this.list,
      };
      this.$emit("update:hideLoader", true);
      await axios
        .post("/api/update-sorting", JSON.stringify(data))
        .then((res) => {
          if (res.data.data && res.data.data.length)
            this.$emit("update:Sorting", JSON.stringify(res.data.data));
          this.$emit("update:hideLoader", false);
        })
        .catch((err) => {
          this.$notify({
            type: "danger",
            text: "Error Updating List! Please refersh the page and try again.",
          });
        });
    },
    showList: _.debounce(
      async function () {
        if (this.column.editableTitle) {
          this.column.editableTitle = false;
          if (this.column.id) {
            await this.updateList();
          } else {
            await this.createList();
          }
        }
      },
      500,
      false
    ),
    async updateList() {
      this.$emit("update:hideLoader", true);
      await axios
        .put("api/column/" + this.column.id, this.column)
        .then((res) => {
          this.$notify({
            type: "success",
            text: "The operation completed.",
          });
          this.$emit("update:hideLoader", false);
          this.column = res.data.data;
          this.$emit("update:List", this.dataUpdated(res.data.data, "update"));
        })
        .catch((err) => {
          this.$notify({
            type: "danger",
            text: "Error Updating List! Please refersh the page and try again.",
          });
        });
    },
    async createList() {
      this.$emit("update:hideLoader", true);
      await axios
        .post("api/column", this.column)
        .then((res) => {
          this.$notify({
            type: "success",
            text: "The operation completed.",
          });
          this.$emit("update:hideLoader", false);
          this.column = res.data.data;
          this.$emit("update:List", this.dataUpdated(res.data.data, "create"));
        })
        .catch((err) => {
          this.$notify({
            type: "danger",
            text: "Error Creating Column! Please refersh the page and try again.",
          });
        });
    },
    async deleteList(i) {
      let text = "Are you sure you want to delete this list?";
      if (confirm(text) == true) {
        this.$emit("update:hideLoader", true);
        await axios
          .delete("api/column/" + i)
          .then((res) => {
            this.$notify({
              type: "success",
              text: "The operation completed.",
            });
            this.$emit("update:hideLoader", false);
            this.$emit("update:List", this.dataUpdated([], "delete"));
          })
          .catch((err) => {
            this.$notify({
              type: "danger",
              text: "Error Deleting List! Please refersh the page and try again.",
            });
          });
      }
    },
    editList() {
      this.column.editableTitle = true;
      setTimeout(() => {
        document.getElementById("column-title" + this.column_index).focus();
      }, 100);
    },

    dataUpdated(param, action) {
      let data = {};
      data.action = action;
      data.index = this.column_index;
      data.data = param;
      return JSON.stringify(data);
    },
    async createCard(i) {
      let validate = true;
      let data = _.filter(this.column.cards, function (o) {
        return o.editableTitle && o.title;
      });
      if (data.length) {
        this.$emit("update:hideLoader", true);
        let res = await axios.post("api/card", data[0]);
        this.$emit("update:hideLoader", false);
        if (res.data.data) {
          this.validateCard = false;
          await this.$nextTick();
          this.column.cards.pop();
          await this.$nextTick();
          this.column.cards.push(res.data.data);
          validate = true;
          this.validateCard = true;
        }
      } else if (
        _.filter(this.column.cards, function (o) {
          return o.editableTitle && !o.title;
        }).length > 0
      ) {
        validate = false;
      } else if (
        _.filter(this.column.cards, function (o) {
          return !o.editableTitle && o.title;
        }).length
      ) {
        validate = true;
      }
      return validate;
    },
    async addCard(i) {
      var validate = await this.createCard(i);
      if (!validate) return false;
      let data = {
        column_id: this.column.id,
        editableTitle: true,
        title: null,
        sort_order: parseInt(this.column.cards.length) + 1,
      };
      this.column.cards.push(data);
      setTimeout(() => {
        document
          .getElementById(
            "card-title-" + i + "-" + parseInt(this.column.cards.length - 1)
          )
          .focus();
      }, 200);
    },
    removeCard(param) {
      if(param) {
        this.column.cards.pop()
      }
      else {
        let index = _.findIndex(this.column.cards, function(o) { return o.editableTitle == true; });
        if(index > 0) this.column.cards.splice(index, 1);
      }
    },
    async handleCardsUpdate(data, i) {
      this.validateCard = false;
      data = JSON.parse(data);
      if (data.action == "delete") {
        await this.$nextTick();
        this.column.cards.splice(i, 1);
        await this.$nextTick();
        this.validateCard = true;
      } else {
        this.validateCard = false;
        await this.$nextTick();
        this.column.cards.splice(i, 1, data.data);
        await this.$nextTick();
        this.validateCard = true;
      }
    },
    hideLoader(param) {
      this.$emit("update:hideLoader", param);
    },
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
    showCencelCardButton: function () {
      let res = false;
      _.forEach(this.column.cards, function (value, key) {
        if (value.editableTitle || value.editableDescription) {
          res = true;
          return false;
        }
      });
      return res;
    },
  },
};
</script>