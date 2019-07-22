<template>
    <div>
        <div class="row">
            <div class="col-md-8">
                <h2 class="pb-3">Sales list</h2>
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
                        <td>{{index +1}}</td>
                        <td>{{sale.product.name}}</td>
                        <td>{{sale.products_quantity}}</td>
                        <td>{{sale.sell_price}} {{client.currency}} </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 moneybox">
                <h2  class="pb-3">MoneyBox</h2>
                <div>
                    Plan : {{client.plan}} {{client.currency}}<br/>
                    Total profit : <span id="totalProfit">0 </span> {{client.currency}}<br/>
                    Difference : <span id="difference"> 0 </span> {{client.currency}}
                </div>
                <div class="p-5">
                    <a href="javascript:void(0)" @click="calculateTotalProfit" class="btn btn-outline-dark btn-block">Calculate</a>
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
                client:{},
                totalProfit: 0,
                difference: 0,
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
            },
            calculateTotalProfit () {
                let percentage  = this.client.percentage ;
                let sales       = this.sales;
                let sumProfit   = 0 ;

                $.each(sales, (i) => {
                    // profit of one sale
                    console.log('1' + sales[i].sell_price );
                    console.log('2' + sales[i].product.buy_price);
                    console.log('3' + sales[i].products_quantity);
                    console.log('4' +percentage);

                    sumProfit += (sales[i].sell_price - sales[i].product.buy_price) * sales[i].products_quantity * (percentage/100) ;
                });

                this.totalProfit = sumProfit ;
                this.animateValue('totalProfit',0,this.totalProfit,1000);
                setTimeout(()=>{
                    this.difference  = this.client.plan - this.totalProfit;
                    this.animateValue('difference',0,this.difference,1000);
                },2000);

            },
            animateValue(id, start, end, duration) {
                let range = end - start;
                let current = start;
                let increment = end > start? 1 : -1;
                let stepTime = Math.abs(Math.floor(duration / range));
                let obj = document.getElementById(id);
                let timer = setInterval(function() {
                    current += increment;
                    obj.innerHTML = current;
                    if (current === end) {
                        clearInterval(timer);
                    }
                }, stepTime);
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