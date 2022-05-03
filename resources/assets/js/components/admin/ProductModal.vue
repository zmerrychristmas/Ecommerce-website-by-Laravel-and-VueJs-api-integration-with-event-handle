    <template>
<div class="modal-mask">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLongTitle" v-show="data.name">{{ data.name }}</h5>
        <h5 class="modal-title" id="exampleModalLongTitle" v-show="!data.name">Add Product</h5>
        <button type="button" class="close" data-dismiss="modal-mask" @click="closeModal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <slot name="body">
            <div class="form-group">
              <label for="name">Name</label>
              <input
                type="text"
                id="name"
                v-model="data.name"
                class="form-control"
              />
              <small id="namehelp" class="form-text text-muted"
                >product name</small
              >
            </div>
            <div class="form-group">
              <label for="units">Product Units</label>
              <input
                type="number"
                class="form-control"
                id="units"
                v-model="data.units"
              />
            </div>
            <div class="form-group">
              <label for="price">Product Price</label>
              <input
                type="number"
                class="form-control"
                id="price"
                v-model="data.price"
                step="any"
              />
            </div>
            <div class="form-group">
              <p for="price">FlashSale</p>
              <label class="switch">
                <input type="checkbox"  v-model="data.is_flashsale" @click="toogleCheckbox(data)"/>
                <div class="slider round"></div>
              </label>              
            </div>
            <div class="form-group">
              <label for="description">Priduct Description</label>
              <textarea
                v-model="data.description"
                class="form-control"
                placeholder="description"
              ></textarea>
            </div>
            <div class="form-group">
              <label for="file">Product image</label>
              <img :src="data.image" v-show="data.image != null" id="modal-product-image"/>
              <input type="file" id="file" @change="attachFile" />
            </div>
          </slot>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal-mask" @click="closeModal">Close</button>
             <button class="btn btn-primary" @click="uploadFile">
              Finish
            </button>        
      </div>
    </div>
  </div>
</div>
</template>

    <style scoped>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  transition: opacity 0.3s ease;
}
.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}
.modal-container {
  width: 300px;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
  font-family: Helvetica, Arial, sans-serif;
}
.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}
.modal-body {
  margin: 20px 0;
}
.modal-default-button {
  float: right;
}
.modal-enter {
  opacity: 0;
}
.modal-leave-active {
  opacity: 0;
}
.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}

#modal-product-image {
    width: 100%; 
    height: 100%;
    max-height: 200px;
}
</style>
        <script>
export default {
  props: ["product"],
  data() {
    return {
      attachment: null,
    };
  },
  computed: {
    data: function () {
      if (this.product != null) {
        return this.product;
      }
      return {
        name: "",
        units: "",
        price: "",
        description: "",
        is_flashsale: false,
        image: false,
      };
    },
  },
  methods: {
    attachFile(event) {
      this.attachment = event.target.files[0];
    },
    closeModal(event) {
        this.$emit("close", this.product);
    },
    uploadFile(event) {
      if (this.attachment != null) {
        var formData = new FormData();
        formData.append("image", this.attachment);
        let headers = { "Content-Type": "multipart/form-data" };
        axios
          .post("/api/upload-file", formData, { headers })
          .then((response) => {
            this.product.image = response.data;
            this.$emit("close", this.product);
          });
      } else {
        this.$emit("close", this.product);
      }
    },
    toogleCheckbox(data) {
        this.product.is_flashsale = !data.is_flashsale
    }
  },
};
</script>