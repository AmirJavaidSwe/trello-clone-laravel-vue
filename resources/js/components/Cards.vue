<template>
  <div>
    <div class="column-header mb-3">
      <input
        type="text"
        :id="'card-title-' + column_index + '-' + card_index"
        class="form-control"
        v-model="card.title"
        v-show="card.editableTitle"
      />
      <div class="column-card-title" v-show="!card.editableTitle" @click="editCard($event, card, card_index)">
        {{ card.title ? card.title : "Untitled" }}
      </div>
      <div
        v-show="!card.editableTitle"
        class="delete-list"
        @click="deleteCard(card.id)"
        title="Delete Card"
      >
        <i class="fa fa-trash"></i>
      </div>
      <template v-if="card.description">
        <div class="description-span" title="This card has a description">
          <i class="fa-solid fa-list-ul"></i>
        </div>
      </template>
    </div>
    <modal
      class="card-edit-modal"
      :name="'edit-card-' + column_index + '-' + card_index"
      scrollable
      focusTrap
      adaptive
      height="auto"
    >
      <div class="editing">
        <div class="edit-card-title">
          <label for="">Card Title:</label>
          <input type="text" class="form-control" v-model="modal_data.title" />
        </div>
        <div class="edit-card-description">
          <label for="">Card Description:</label>
          <textarea
            type="text"
            rows="10"
            cols="50"
            class="form-control"
            v-model="modal_data.description"
          ></textarea>
        </div>
        <div class="save-details">
          <button class="btn btn-primary" @click="updateCard(modal_data)">
            Update
          </button>
          <button
            class="btn btn-danger"
            @click="$modal.hide('edit-card-' + column_index + '-' + card_index)"
          >
            Cancel
          </button>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  name: "Cards",
  props: ["data", "index", "parent_index"],
  data() {
    return {
      card: this.data,
      card_index: this.index,
      column_index: this.parent_index,
      modal_data: [],
      modal_index: 0,
    };
  },
  methods: {
    deleteCard(id) {
      let text = "Are you sure you want to delete this card?";
      if (confirm(text) == true) {
        this.$emit("update:hideLoader", true);
        axios
          .delete("api/card/" + id)
          .then((res) => {
            this.$emit("update:hideLoader", false);
            this.$emit("update:Parent", this.dataUpdated([], "delete"));
            this.$notify({ type: "success", text: "The operation completed." });
          })
          .catch((err) =>
            this.$notify({
              type: "danger",
              text: "Error Deleting Card! Please refersh the page and try again.",
            })
          )
      }
    },
    makeChnageData() {
      let data = {};
      data.city = this.cityFilter;
      data.staffMember = this.staffMember;
      data.date_range = this.dateFilter;
      return JSON.stringify(data);
    },
    editCard(e, data, index) {
      this.modal_data = data;
      this.$modal.show(
        "edit-card-" + this.column_index + "-" + this.card_index
      );
      this.modal_index = index;
    },
    updateCard(data) {
      this.$emit("update:hideLoader", true);
      axios
        .put("api/card/" + data.id, data)
        .then((res) => {
          this.$emit("update:hideLoader", false);
          this.card = res.data.data;
          this.$modal.hide(
            "edit-card-" + this.column_index + "-" + this.card_index
          );
          this.$emit(
            "update:Parent",
            this.dataUpdated(res.data.data, "update")
          );
          this.$notify({ type: "success", text: "The operation completed" });
        })
        .catch((err) => {
          this.$notify({ type: "danger", text: "Error Updating Card! Please refersh the page and try again.", });
        })
    },
    dataUpdated(param, action) {
      let data = {};
      data.action = action;
      data.index = this.card_index;
      data.data = param;
      return JSON.stringify(data);
    },
  },
};
</script>