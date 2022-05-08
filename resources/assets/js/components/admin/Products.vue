    <template>
        <div>
            <table class="table table-responsive table-striped">
                <thead>
                    <tr>
                        <td></td>
                        <td>Product</td>
                        <td>Product Image</td>
                        <td>Units</td>
                        <td>Price</td>
                        <td>Flashsale</td>
                        <td>Description</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(product,index) in products" @key="index" @dblclick="editingItem = product">
                        <td>{{index+1}}</td>
                        <td v-model="product.name">{{product.name}}</td>
                        <td v-model="product.image"><img :src="product.image" alt="thumbnail" class="img-thumbnail"></td>
                        <td v-model="product.units">{{product.units}}</td>
                        <td v-model="product.price">{{product.price}}</td>
                        <td>              <label class="switch">
                <input type="checkbox"  v-model="product.is_flashsale" @click="toggleCheckbox(product)"/>
                <div class="slider round"></div>
              </label>  </td>                        
                        <td v-model="product.price">{{product.description}}</td>
                    </tr>
                </tbody>
            </table>
            <modal @close="endEditing" :product="editingItem" v-show="editingItem != null"></modal>
            <modal @close="addProduct"  :product="addingProduct" v-show="addingProduct != null"></modal>
            <br>
            <button class="btn btn-primary" @click="newProduct">Add New Product</button>
        </div>
    </template>
    <style scoped>
    td > .img-thumbnail {
        height: 100%;
        max-height: 120px;
    }
    </style>
        <script>
    import Modal from './ProductModal'

    export default {
        data() {
            return {
                products: [],
                editingItem: null,
                addingProduct: null
            }
        },
        components: {Modal},
        beforeMount() {
            axios.get('/api/products/').then(response => this.products = response.data)
        },
        methods: {
            newProduct() {
                this.addingProduct = {
                    name: null,
                    units: null,
                    price: null,
                    image: null,
                    is_flashsale: null,
                    description: null,
                }
            },
            endEditing(product) {
                this.editingItem = null

                let index = this.products.indexOf(product)
                let name = product.name
                let units = product.units
                let price = product.price
                let description = product.description
                let is_flashsale = product.is_flashsale
                let image = product.image 

                axios.put(`/api/products/${product.id}`, {name, units, price, is_flashsale, description, image})
                     .then(response => this.products[index] = product)
            },
            addProduct(product) {
                this.addingProduct = null

                let name = product.name
                let units = product.units
                let price = product.price
                let is_flashsale = product.is_flashsale
                let description = product.description
                let image = product.image 

                axios.post("/api/products/", {name, units, price, description, is_flashsale, image})
                     .then(response => this.products.push(product))
            },
            toggleCheckbox(product) {
                console.log(product)
                product.is_flashsale = !product.is_flashsale
                let index = this.products.indexOf(product)                
                let is_flashsale = product.is_flashsale

                axios.patch(`/api/products/${product.id}/flashsale/change`, {is_flashsale})
                     .then(response => this.products[index] = product)
            }
        }
    }
    </script>