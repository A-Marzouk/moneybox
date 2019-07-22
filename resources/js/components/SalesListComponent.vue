<template>
    <div>
        <div class="row">
            <div class="col-md-8">
                <div class="d-flex justify-content-between">
                    <h2 class="pb-3">Sales list</h2>
                    <div>
                        <a href="javascript:void(0)" @click="addNewSale = true" class="btn btn-outline-dark">Add sale</a>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Sell Price</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(sale, index) in sales" :key="index">
                        <td>{{index +1}}</td>
                        <td>{{sale.product.name}}</td>
                        <td>{{sale.products_quantity}}</td>
                        <td>{{sale.sell_price}} {{client.currency}} </td>
                        <td><a href="javascript:void(0)" class="btn btn-dark btn-sm" @click="deleteSale(sale.id)">X</a> </td>
                    </tr>
                    <tr v-show="addNewSale">
                        <td>
                           {{this.sales.length + 1}}
                        </td>
                        <td>
                            <select v-model="newSale.product_id" id="" class="custom-select">
                                <option selected disabled>Select product</option>
                                <option v-for="(product,index) in products" :key="index + 'A'" :value="product.id">
                                    {{product.name}}
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="number" v-model="newSale.products_quantity" class="form-control">
                        </td>
                        <td>
                            <input type="number" min="0" max="999999" step="any" v-model="newSale.sell_price" class="form-control">
                        </td>
                        <td class="d-flex">
                            <a href="javascript:void(0)" class="btn btn-primary mr-2 btn-sm" @click="addSale">Add</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm" @click="addNewSale = false ">Cancel</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 moneybox">
                <h2  class="pb-3">MoneyBox</h2>
                <div>
                    Total profit : <span id="totalProfit">0 </span> {{client.currency}}<br/><br/>
                    Plan : {{client.plan}} {{client.currency}}<br/>
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
                products:[],
                client:{},
                totalProfit: 1,
                difference: 0,
                ready:0,
                newSale:{
                    'product_id': '',
                    'products_quantity' : '',
                    'sell_price' : '',
                    'client_id' : '',
                },
                addNewSale:false
            }
        },
        watch:{
            ready : function(){
                if(this.ready === 2){
                    this.calculateTotalProfit();
                }
            }
        },
        methods:{
            getSalesList(){
                axios
                    .get('/api/get-sales-list')
                    .then( (response) => {
                        this.sales = response.data ;
                        this.ready ++ ;
                    })
                    .catch( (error) => {
                        console.log(error);
                    });
            },
            getProducts(){
                axios
                    .get('/api/get-products')
                    .then( (response) => {
                        this.products = response.data ;
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
                        this.ready ++ ;
                    })
                    .catch( (error) => {
                        console.log(error);
                    });
            },
            calculateTotalProfit () {
                if(this.sales.length < 1){
                    return ;
                }
                let percentage  = this.client.percentage ;
                let sales       = this.sales;
                let sumProfit   = 0 ;

                $.each(sales, (i) => {
                    // profit of one sale
                    sumProfit += (sales[i].sell_price - sales[i].product.buy_price) * sales[i].products_quantity * (percentage/100) ;
                });

                this.totalProfit = sumProfit ;
                this.animateValue('totalProfit',0,this.totalProfit,1000);
                setTimeout(()=>{
                    this.difference  = this.client.plan - this.totalProfit;
                    this.animateValue('difference',0,this.difference,100);
                },100);

            },
            addSale(){
                this.newSale.client_id = this.client.id;
                axios.post('/sales/add',this.newSale)
                  .then( (response) => {
                      this.sales.push(response.data);
                      this.calculateTotalProfit();
                      this.addNewSale = false;
                  })
                  .catch( (error) => {
                      alert('Error adding new sale.')
                  });
            },
            deleteSale(sale_id){
              axios.post('/sales/delete',{'sale_id' : sale_id})
                  .then( (response) => {
                      let sales = this.sales;
                      $.each(sales, (i) => {
                          if (sales[i].id === sale_id) {
                              sales.splice(i, 1);
                              this.calculateTotalProfit();
                              return false;
                          }
                      });
                  })
                  .catch( (error) => {

                  });
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
            this.getProducts();
            this.getCurrentClient();
        }
    }
</script>

<style scoped lang="scss">
    .moneybox{
        border-left: 1px solid lightgray;
    }
    #totalProfit{
        color: #38c172;
        font-size: 20px;
        font-weight:bold;
    }
</style>