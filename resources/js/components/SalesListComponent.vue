<template>
    <div class="moneyBoxContainer">
        <div class="row">
            <div class="col-md-10">
                <div class="d-flex justify-content-between">
                    <h2 class="pb-3">Sales list</h2>
                    <div>
                        <a href="javascript:void(0)" @click="addNewSale = true" class="btn btn-outline-dark">Add
                            sale</a>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Sell Price</th>
                        <th>Other payments</th>
                        <th>Bonus</th>
                        <th>Actions</th>

                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(sale, index) in sales" :key="index">
                        <td>{{index +1}}</td>
                        <td>{{sale.product.name}}</td>
                        <td>{{sale.products_quantity}}</td>
                        <td>{{sale.sell_price}} {{client.currency}}</td>
                        <td>

                            <a v-if="sale.costs.length > 0" href="javascript:void(0)" data-toggle="modal" :data-target="'#costsModal_' + sale.id">
                                 {{getTotalCost(sale)}}
                            </a>

                            <span v-else>
                                 {{getTotalCost(sale)}}
                            </span>

                        </td>
                        <td>{{calculateSingleBonus(sale)}}</td>
                        <td><a href="javascript:void(0)" class="btn btn-dark btn-sm" @click="deleteSale(sale.id)">X</a>
                        </td>
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
                            <input type="number" min="0" max="999999" step="any" v-model="newSale.sell_price"
                                   class="form-control">
                        </td>
                        <td>
                            <input type="number"  min="0" max="999999"  class="form-control" @click="showOtherPaymentsBox" v-model="newSale.totalCost">
                            <div class="box-popup" v-show="paymentsBox">
                                <div class="container">
                                    <h5>Other payments :</h5>
                                    <div v-for="(cost,index) in newSale.costs" :key="index">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="d-flex">
                                                    <input type="text" class="form-control" v-model="cost.label">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" class="form-control" placeholder="cost" v-model="cost.cost">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pt-2">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-dark addCostBtn" @click="addNewCost">+</a>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm" @click="addOtherPayments">OK</a>
                                        </div>
                                        <div>
                                            Total : {{getTotalCost(newSale)}} UAH
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="d-flex">
                            <a href="javascript:void(0)" class="btn btn-primary mr-2 btn-sm" @click="addSale">Add</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm" @click="addNewSale = false ">Cancel</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-2 moneybox">
                <h2 class="pb-3">MoneyBox</h2>
                <div>
                    Total profit : <span id="totalProfit"> {{totalProfit.toFixed(2)}} </span> {{client.currency}}<br/>
                    <br/>
                    Plan : {{client.plan}} {{client.currency}}<br/>
                    Difference : <span id="difference"> {{ difference.toFixed(2)}} </span> {{client.currency}}
                </div>
                <div class="">
                    <a href="javascript:void(0)" @click="calculateTotalProfit" class="btn btn-outline-dark btn-block">Calculate</a>
                </div>
            </div>

        </div>

        <!-- modals -->

        <div v-for="(sale, index) in sales" :key="index">
            <div class="modal fade" :id="'costsModal_'+sale.id" tabindex="-1" role="dialog" aria-labelledby="costsModal"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Other costs</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div v-for="(cost,index) in sale.costs" :key="index">
                                {{index+1}} - {{cost.label}} : {{cost.cost}} UAH
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "SalesList",
        data() {
            return {
                sales: [],
                products: [],
                client: {},
                totalProfit: 1,
                difference: 0,
                ready: 0,
                newSale: {
                    'product_id': '',
                    'products_quantity': '',
                    'sell_price': '',
                    'client_id': '',
                    'costs' : [
                        {
                            label:'Transport',
                            cost:0,
                        },
                        {
                            label:'Storing',
                            cost:0,
                        },
                        {
                            label:'Other',
                            cost:0,
                        }
                    ],
                    totalCost : 0
                },
                addNewSale: false,
                paymentsBox: false,
            }
        },
        watch: {
            ready: function () {
                if (this.ready === 2) {
                    this.calculateTotalProfit();
                }
            }
        },
        methods: {
            getSalesList() {
                axios
                    .get('/api/get-sales-list')
                    .then((response) => {
                        this.sales = response.data;
                        this.ready++;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
            getProducts() {
                axios
                    .get('/api/get-products')
                    .then((response) => {
                        this.products = response.data;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
            getCurrentClient() {
                axios
                    .get('/api/get-current-client')
                    .then((response) => {
                        this.client = response.data;
                        this.ready++;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
            calculateTotalProfit() {
                if (this.sales.length < 1) {
                    return;
                }
                let percentage = this.client.percentage;
                let sales = this.sales;
                let sumProfit = 0;

                $.each(sales, (i) => {
                    // profit of one sale
                    sumProfit += (sales[i].sell_price - sales[i].product.buy_price) * sales[i].products_quantity * (percentage / 100);
                });

                this.totalProfit = sumProfit;
                this.difference = this.client.plan - this.totalProfit;
                this.animateValue('totalProfit', 0, sumProfit, 30);
            },
            addSale() {
                this.newSale.client_id = this.client.id;
                axios.post('/sales/add', this.newSale)
                    .then((response) => {
                        this.sales.push(response.data);
                        this.calculateTotalProfit();
                        this.addNewSale = false;
                        this.clearInputs();
                    })
                    .catch((error) => {
                        alert('Error adding new sale.');
                    });
            },
            showOtherPaymentsBox(){
                this.paymentsBox = true ;
            },
            addNewCost(){
              this.newSale.costs.push({
                  label:'New cost',
                  cost:0
              })
            },
            clearInputs() {
                this.newSale = {
                    'product_id': '',
                    'products_quantity': '',
                    'sell_price': '',
                    'client_id': '',
                };
            },
            deleteSale(sale_id) {
                axios.post('/sales/delete', {'sale_id': sale_id})
                    .then((response) => {
                        let sales = this.sales;
                        $.each(sales, (i) => {
                            if (sales[i].id === sale_id) {
                                sales.splice(i, 1);
                                this.calculateTotalProfit();
                                return false;
                            }
                        });
                    })
                    .catch((error) => {

                    });
            },
            animateValue(id, start, end, duration) {
                let range = end - start;
                let current = start;
                let increment = 50;
                let stepTime = Math.abs(Math.floor(duration / range));
                let obj = document.getElementById(id);

                let timer = setInterval(() => {
                    current += increment;
                    obj.innerHTML = current;
                    if (current >= end) {
                        clearInterval(timer);
                        obj.innerHTML = this.totalProfit;
                    }
                }, stepTime);
            },
            calculateSingleBonus(sale) {
                let bonus = (sale.sell_price - sale.product.buy_price) * sale.products_quantity * (this.client.percentage / 100);
                return Math.ceil(bonus);
            },
            getTotalCost(sale) {
                let costs = sale.costs;
                let sum = 0;
                $.each(costs, (i, cost) => {
                    sum = (sum - (cost.cost * -1));
                });

                return sum;
            },
            addOtherPayments(){
                this.newSale.totalCost = this.getTotalCost(this.newSale) ;
                this.paymentsBox = false;
            }
        },
        mounted() {
            this.getSalesList();
            this.getProducts();
            this.getCurrentClient();
        }
    }
</script>

<style scoped lang="scss">
    .moneybox {
        border-left: 1px solid lightgray;
    }

    #totalProfit {
        color: #38c172;
        font-size: 20px;
        font-weight: bold;
    }

    .box-popup{
        width:250px;
        background: white;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
        position: absolute;
        margin-top: 5px;
        margin-bottom: 30px;

        .container{
            padding:10px;
        }

        input{
            height: 30px;
        }

        .row{
            padding-top: 5px;
        }
    }

    .addCostBtn{
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 25px;
    }


</style>