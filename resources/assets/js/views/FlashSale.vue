 <template>
        <div>
            <div class="container-fluid hero-section d-flex align-content-center justify-content-center flex-wrap ml-auto">
                <h2 class="title">Welcome to the l-ecommerce</h2>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4 product-box" v-for="(product,index) in products" @key="index" :id="`product-${product.id}`">
                                <router-link :to="{ path: '/products/'+product.id}">
                                    <img :src="product.image" :alt="product.name" class="product-image">
                                    <h5><span v-html="product.name"></span>
                                        <span class="small-text text-muted float-right">$ {{product.price}}</span>
                                    </h5>
                                </router-link>
                                    <button v-if="!isLoggedIn" class="col-md-4 btn btn-primary float-left" @click="login">Login</button>
                                    <button v-if="isLoggedIn" class="col-md-4 btn btn-sm btn-primary float-right" @click="flashsaleOrder(product, index)">Buy Now</button>
                                    <card @close="orderStatus" :orderNumber="orderNumber" :product="buyProduct" :orderedStatus="orderedStatus" :orderId="orderId" v-show="buyProduct == product" @key="index"></card>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
  <style scoped>
    .small-text {
        font-size: 14px;
    }
    .product-box {
        border: 1px solid #cccccc;
        padding: 10px 15px;
        position: relative;
    }
    .hero-section {
        height: 30vh;
        background: #ababab;
        align-items: center;
        margin-bottom: 20px;
        margin-top: -20px;
    }
    .title {
        font-size: 60px;
        color: #ffffff;
    }
    </style>
    <script>
        import Card from '../components/orderStatusCard'

        export default {
            data(){
                return {
                    products : [],
                    isLoggedIn: false,
                    buyProduct: null,
                    orderNumber: 0,
                    orderedStatus: '',
                    orderId: 0
                }
            },
            components: {Card},
            mounted(){
                axios.get("api/products/flashsale").then(response => this.products = response.data)
                this.isLoggedIn = localStorage.getItem('l-ecommerce.jwt') != null
            },
            beforeMount() {
                if (localStorage.getItem('l-ecommerce.jwt') != null) {
                    this.user = JSON.parse(localStorage.getItem('l-ecommerce.user'))
                    axios.defaults.headers.common['Content-Type'] = 'application/json'
                    axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('l-ecommerce.jwt')
                    console.log(localStorage.getItem('l-ecommerce.jwt'))
                    }
            },
            methods : {
                login() {
                    this.$router.push({name: 'login', params: {nextUrl: this.$route.fullPath}})
                    console.log("RELOAD PAGE!")
                    window.location.reload();
                },
                flashsaleOrder(product, index) {
                    console.log(product, index)
                    this.orderedStatus = ''
                    this.orderId = 0
                    this.buyProduct = product
                    this.orderNumber = 159
                    let product_id = product.id
                    let quantity = 1
                    let address = ''
                    axios.post(`/api/orders/flashsale`, {product_id, quantity, address}).then(response => {
                         this.orderNumber = response.data.order
                         let order_id = response.data.data.id
                        Echo.private(`orders.${order_id}`)
                            .listen('OrderShipmentStatusUpdated', (e) => {
                                console.log(e);
                                this.orderedStatus = e.message
                                this.orderId = e.order.id
                                setTimeout(() => {
                                    this.$delete(this.products, index)
                                    console.log('Leave channel')
                                    Echo.leave(`orders.${order_id}`);
                                }, 5000)
                            });
                    })
                },
                orderStatus() {
                    this.buyProduct = null
                    this.orderedStatus = ''
                    this.orderId = 0
                }
            }
        }
    </script>