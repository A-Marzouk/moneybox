<template>
    <div>
        <div class="row">
            <div class="col-md-8">
                <h2>Sales list</h2>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Sell Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(sale, index) in sales" :key="index">
                        <td>{{index+1}}</td>
                        <td>{{sale.product.name}}</td>
                        <td>{{sale.products_quantity}}</td>
                        <td>{{sale.sell_price}} </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 moneybox">
                <h2>MoneyBox</h2>
                <div>
                    Plan : {{client.plan}}
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        name: "SalesList",
        data(){
            return {
                sales:[],
                client:{}
            }
        },
        methods:{
            getSalesList(){
                axios
                    .get('/api/get-sales-list')
                    .then( (response) => {
                        this.sales = response.data ;
                    })
                    .catch( (error) => {
                        console.log(error);
                    });
            },
            getCurrentClient(){
                axios
                    .get('/api/get-current-client')
                    .then( (response) => {
                        this.client = response.data ;
                    })
                    .catch( (error) => {
                        console.log(error);
                    });
            }
        },
        mounted(){
            this.getSalesList();
            this.getCurrentClient();
        }
    }
</script>

<style scoped lang="scss">
    .moneybox{
        border-left: 1px solid lightgray;
    }
</style>