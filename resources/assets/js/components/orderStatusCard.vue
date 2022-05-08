<template>
<div class="card">
  <div class="card-body"  v-show="this.orderedStatus == ''">
    <h5 class="card-title">Buying your product</h5>
    <h6 class="card-subtitle mb-2 text-muted">Your current order #{{this.orderNumber}} in queue</h6>
    <div class="spinner-border text-success" role="status">
        <p class="card-text d-flex align-items-center"></p>
        <span class="sr-only"></span>
    </div>
  </div>
  <div class="card-body"  v-show="this.orderedStatus == 'ORDER_ACCEPTED'">
    <h5 class="card-title">Order placed successfully</h5>
    <h6 class="card-subtitle mb-2 text-muted">Order number #{{this.orderId}}</h6>
    <div class="text-success text-center" role="status">
        <font-awesome-icon icon="check-circle" size="3x" />
    </div>
  </div>
  <div class="card-body"  v-show="this.orderedStatus == 'ORDER_CANCELED'">
    <h5 class="card-title">Product no long avaiable</h5>
    <h6 class="card-subtitle mb-2 text-muted">You just missed it.</h6>
    <div class="text-danger text-center" role="status">
        <font-awesome-icon icon="fas fa-exclamation-circle" size="3x" />
    </div>
  </div>
</div>
</template>
<style scoped>
.card {
    position: absolute;
    bottom: 0;
    width: 94%;
    min-height: 150px;
}
.spinner-border {
    margin-left: 44%;
}
</style>
<script>
    export default {
        props: ["product", "orderNumber", "orderedStatus", "orderId"],
        data() {
          return {
            count: 0
          }
        },
        mounted() {
        },
        updated() {
            console.log("updated")
        }
    }
</script>
