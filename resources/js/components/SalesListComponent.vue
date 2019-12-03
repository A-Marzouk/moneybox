<template>
    <div class="moneyBoxContainer">
        <div class="row">
            <div class="col-md-12 border-bottom">
                <div class="d-flex justify-content-between">
                    <h2 class="pb-3">Список продаж</h2>
                    <div class="display-1">
                        <a href="javascript:void(0)" @click="addNewSale = true" class="btn btn-outline-dark">
                            Добавить продажу
                        </a>
                         <a :href="'/client/export/sales/' + client.id" class="btn  btn-primary ml-2">Скачать все продажи XLSX</a>
                    </div>
                </div>
                <div class="alert alert-danger" v-show="Object.entries(errors).length !== 0">
                    <div v-for="(error,index) in errors" :key="index">
                        {{error[0]}}
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Товар</th>
                        <th>Поставщик</th>
                        <th>Количество</th>
                        <th>Цена продажи</th>
                        <th>Другие расходы</th>
                        <th>Бонус</th>
                        <th>Новый клиент</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-show="addNewSale">
                        <td>
                            {{this.sales.length + 1}}
                        </td>
                        <td>
                            <select v-model="newSale.product" id="" class="custom-select">
                                <option selected disabled>Select product</option>
                                <option v-for="(product,index) in orderedProducts" :key="index + 'A'" :value="product" v-show="product.quantity > 0">
                                    {{product.name}} (Кол. {{product.quantity}})  - {{product.supplier}} - {{product.date}}
                                </option>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                        <td>
                            <input type="number" min="0" max="999999" v-model="newSale.products_quantity" class="form-control">
                        </td>
                        <td>
                            <input type="number" min="0" max="999999" step="any" v-model="newSale.sell_price"
                                   class="form-control">
                        </td>
                        <td>
                            <input type="number"  min="0" max="999999"  class="form-control" @click="showOtherPaymentsBox" v-model="newSale.totalCost">
                            <div class="box-popup" v-show="paymentsBox">
                                <div class="container">
                                    <h5>Другие расходы :</h5>
                                    <div v-for="(cost,index) in newSale.costs" :key="index">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="d-flex">
                                                    <input type="text" class="form-control" v-model="cost.label">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" min="0" max="999999" class="form-control" placeholder="cost" v-model="cost.cost">
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
                                            Общая сумма : {{getTotalCost(newSale)}} UAH
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            0
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-checkbox mr-2 btn-sm" :class="{active : newSale.for_new_client}" @click="newSale.for_new_client = !newSale.for_new_client">Новый клиент</a>
                        </td>

                        <td class="d-flex">
                            <a href="javascript:void(0)" class="btn btn-primary mr-2 btn-sm" @click="addSale">Добавить</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm" @click="addNewSale = false ">Отменить</a>
                        </td>
                    </tr>
                    <tr v-for="(sale, index) in sales" :key="index" v-if="sale.product !== null">

                            <td>{{index +1}}</td>


                            <td>
                                <div>{{sale.product.name}}</div>
                            </td>
                            <td>{{sale.product.supplier}}</td>
                            <td>

                                <div v-if="editedSale.id === sale.id">
                                    <input type="number" min="0" max="999999" v-model="new_quantity" class="form-control">
                                </div>
                                <div v-else>   {{sale.products_quantity}}</div>

                            </td>
                            <td>
                                <div v-if="editedSale.id === sale.id">
                                    <input type="number" min="0" max="999999" step="any" v-model="new_sell_price"
                                           class="form-control">
                                </div>
                                <div v-else> {{sale.sell_price}} {{client.currency}}</div>
                            </td>
                            <td>

                                <a v-if="sale.costs.length > 0" href="javascript:void(0)" data-toggle="modal" :data-target="'#costsModal_' + sale.id">
                                    {{getTotalCost(sale)}}
                                </a>

                                <span v-else>
                                 {{getTotalCost(sale)}}
                                </span>

                            </td>
                            <td>{{calculateSingleBonus(sale)}}</td>
                            <td class="d-flex justify-content-center">

                                <div v-if="editedSale.id === sale.id">
                                    <a href="javascript:void(0)" class="btn btn-checkbox mr-2 btn-sm" :class="{active : new_for_new_client}" @click="new_for_new_client = !new_for_new_client">Новый клиент</a>
                                </div>
                                <div v-else>
                                <span v-if="sale.for_new_client" class="green-circle">
                                </span>
                                    <span v-else class="orange-circle">
                                </span>

                                </div>
                            </td>

                            <td>
                                <a href="javascript:void(0)" @click="toggleEditedSale(sale)" v-show="editedSale.id != sale.id">
                                    <img src="/images/edit.svg" alt="edit" style="width: 14px;">
                                </a>
                                <div v-show="editedSale.id == sale.id">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-danger" @click="toggleEditedSale(sale)" >
                                        Cancel
                                    </a>
                                    <a href="javascript:void(0)"  class="btn btn-sm btn-success" @click="editSale">
                                        Save
                                    </a>
                                </div>
                            </td>

                            <td>
                                <a href="javascript:void(0)" @click="deleteSale(sale.id)">X</a>
                            </td>

                    </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 moneybox">
                <h2 class="pb-3">Копилка</h2>
                <div>
                    Общая прибыль : <span id="totalProfit"> {{totalProfit.toFixed(2)}} </span> {{client.currency}}<br/>
                    <br/>
                    План : {{client.plan}} {{client.currency}}<br/>
                    Разница : {{ difference.toFixed(2)}} {{client.currency}} <br/>
                    <span v-show="abovePlan > 0 ">
                        Сверху плана : {{ abovePlan.toFixed(2)}} {{client.currency}}
                    </span>
                </div>
                <div class="">
                    <a href="javascript:void(0)" @click="calculateTotalProfit" class="mt-3 btn btn-outline-dark btn-block">Вычислять</a>
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
                            <h5 class="modal-title" id="exampleModalLabel">Другие расходы</h5>
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

        <div class="changesSavedText d-none" id="changesSaved">
            <span class="alert alert-success">
                Сохранено
            </span>
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
                abovePlan: 0,
                ready: 0,
                USD_rate:'',
                EUR_rate:'',
                newSale: {
                    'product': {},
                    'products_quantity': '',
                    'sell_price': '',
                    'client_id': '',
                    'for_new_client':false,
                    'costs' : [
                        {
                            label:'Транспорт',
                            cost:0,
                        },
                        {
                            label:'Склад',
                            cost:0,
                        },
                        {
                            label:'Другой',
                            cost:0,
                        }
                    ],
                    totalCost : 0
                },
                addNewSale: false,
                paymentsBox: false,
                errors: {},
                currentPage: 1,
                lastPage: '',
                editedSale:{},
                new_quantity : '',
                new_sell_price :'',
                new_for_new_client : '',
            }
        },
        computed:{
            orderedProducts: function () {
                return _.orderBy(this.products, 'name');
            }
        },
        watch: {
            ready: function () {
                if (this.ready === 3) {
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
                    .get('/api/get-products?limit=20&&page=' + this.currentPage)
                    .then((response) => {
                        this.lastPage = response.data.last_page ;
                        this.currentPage++;
                        this.products.push(...response.data.data);
                        if(this.currentPage !== this.lastPage){
                            this.getProducts();
                        }
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
                let sales = this.sales;
                let sumProfit = 0;

                $.each(sales, (i) => {
                    // profit of one sale
                    sumProfit += this.calculateSingleBonus(sales[i]);
                });

                this.totalProfit = sumProfit;
                this.difference = this.client.plan - this.totalProfit;

                if(this.totalProfit > this.client.plan){ // profit is higher than target
                    this.abovePlan  = this.totalProfit - this.client.plan ;
                    this.difference = 0 ;
                }else{
                    this.abovePlan = 0 ;
                }
                this.animateValue('totalProfit', 0, sumProfit, 30);
            },
            addSale() {
                this.errors = {} ;
                this.newSale.client_id = this.client.id;
                this.newSale.product_id   = this.newSale.product.id;
                this.newSale.bonus = this.calculateSingleBonus(this.newSale);
                console.log(this.newSale);
                axios.post('/sales/add', this.newSale)
                    .then((response) => {
                        this.sales.push(response.data);
                        this.calculateTotalProfit();
                        this.addNewSale = false;
                        this.clearInputs();

                        // update products quantity
                        this.getProducts();
                    })
                    .catch((error) => {
                        console.log(error.response.data.errors);
                        this.errors = error.response.data.errors;
                    });
            },
            toggleEditedSale(sale){
                if(this.editedSale.id == sale.id){
                    // toggle
                    this.editedSale = {};
                }else{
                    this.new_sell_price = sale.sell_price;
                    this.new_quantity = sale.products_quantity;
                    this.new_for_new_client = sale.for_new_client;
                    this.editedSale = sale ;
                }
            },
            editSale(){
                this.editedSale.products_quantity = this.new_quantity;
                this.editedSale.sell_price = this.new_sell_price;
                this.editedSale.for_new_client= this.new_for_new_client;

                axios.post('/sales/edit', this.editedSale)
                    .then((response) => {
                        if (response.data.status === 'success') {
                            $('#changesSaved').fadeIn('slow');
                            $('#changesSaved').removeClass('d-none');
                            setTimeout(function () {
                                $('#changesSaved').fadeOut();
                            },2000);
                            this.errors = {} ;
                        }
                        this.calculateTotalProfit();
                        this.editedSale = {};
                    })
                    .catch((error) => {
                        console.log(error.response.data.errors);
                        this.errors = error.response.data.errors;
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
                    'for_new_client': false,
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
                };

            },
            deleteSale(sale_id) {
                if (!confirm('Вы уверены, что хотите удалить этот sale ?')){
                    return;
                }
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
                        obj.innerHTML = this.totalProfit.toFixed(2);
                    }
                }, stepTime);
            },
            calculateSingleBonus(sale) {
                if(!sale.product){
                    return 0;
                }
                let rate = 1.00 ;
                if(sale.product.currency === 'USD'){
                    rate = this.USD_rate;
                }else if(sale.product.currency === 'EUR'){
                    rate = this.EUR_rate;
                }
                let totalCosts   =  this.getTotalCost(sale) + ( sale.product.buy_price * rate * sale.products_quantity);
                let totalIncome  =  (sale.sell_price * sale.products_quantity ) ;
                let percentage   =  this.client.percentage / 100 ;
                if(sale.for_new_client){
                    percentage   =  this.client.percentage_new_client / 100 ;
                }
                let bonus        =  (totalIncome - totalCosts) * percentage ;

                return Math.round(bonus * 100) / 100;
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
            },
            getCurrenciesRate(){
                axios.get('/api/currency/rate').then( (response) => {
                    this.USD_rate = response.data.usd_rate;
                    this.EUR_rate = response.data.eur_rate;
                    this.ready++;
                });
            }
        },
        mounted() {
            this.getCurrenciesRate();
            this.getSalesList();
            this.getProducts();
            this.getCurrentClient();
        }
    }
</script>

<style scoped lang="scss">
    .moneybox {
        border: 1px solid lightgray;
        border-radius: 10px;
        padding:20px;
        margin-top:30px;
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
        z-index:2 ;

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

    .btn-checkbox{
        background: lightgrey;
        border:grey;
        color:white;
        white-space: nowrap;
        outline: none!important;
    }
    .btn-checkbox:hover{
        background: lightgrey;
        outline: none!important;
    }
    .btn-checkbox:focus{
        outline: none!important;
    }

    .btn-checkbox.active{
        background: lightgreen;
        outline: none!important;
    }

    .btn-checkbox:active {
        outline: none!important;
    }

    .green-circle{
        height: 15px;
        width: 15px;
        background-color: greenyellow;
        border-radius: 50%;
        display: inline-block;
    }

    .orange-circle{
        height: 15px;
        width: 15px;
        background-color: lightgrey;
        border-radius: 50%;
        display: inline-block;
    }

    .changesSavedText {
        border-radius: 5px 5px 0 0 !important;
        position: fixed;
        height: 38px;
        bottom: 38px;
        left: 32px;
        width: 173px;
    }

</style>
