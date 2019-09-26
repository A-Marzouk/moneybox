<template>
    <div class="container">
        <div class="d-flex justify-content-between">
            <h2 class="pb-3">Список товаров</h2>
            <div class="d-flex">
                <div class="mr-2" style="padding-top: 10px;">
                    <b>USD: {{USD_rate}} |</b>
                </div>
                <div class="mr-2" style="padding-top: 10px;">
                    <b>EUR {{EUR_rate}}</b>
                </div>
                <div class="mr-2">
                    <select v-model="sortByName" class="form-control" @change="sortProductsByName">
                        <option value="">
                            Сорт. по имени
                        </option>
                        <option value="asc">
                            Сорт. по имени &darr;
                        </option>
                        <option value="desc">
                            Сорт. по имени &uarr;
                        </option>
                    </select>
                </div>
                <div class="mr-2">
                    <select v-model="sortByDate" class="form-control" @change="sortProductsByDate">
                        <option value="">
                            Сорт. по дате
                        </option>
                        <option value="asc">
                            Сорт. по дате &darr;
                        </option>
                        <option value="desc">
                            Сорт. по дате &uarr;
                        </option>
                    </select>
                </div>
                <div>
                    <a href="javascript:void(0)" @click="addNewProduct = true" class="btn btn-outline-dark mr-3">Добавить товар</a>
                </div>
                <div>
                    <a href="/admin/excel/actions" class="btn btn-outline-dark mr-3">Эксел</a>
                </div>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th>Количество</th>
                    <th>Дата</th>
                    <th style="width: 100px;">Поставщик</th>
                    <th style="width: 100px;">Цена</th>
                    <th>Валюта</th>
                    <th style="width: 100px;">Цена в грн</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr v-show="addNewProduct">
                <td>
                    {{this.products.length + 1}}
                </td>

                <td>
                    <input type="text" v-model="newProduct.name" class="form-control">
                </td>
                <td>
                    <input type="number" min="0" max="999999" step="any" v-model="newProduct.quantity" class="form-control">
                </td>
                <td>
                    <input type="date" v-model="newProduct.date" class="form-control">
                </td>
                <td>
                    <input type="text" v-model="newProduct.supplier" class="form-control">
                </td>
                <td>
                    <input type="number" min="0" max="999999" step="any" v-model="newProduct.buy_price" class="form-control">
                </td>
                <td>
                    <select  v-model="newProduct.currency" class="form-control" style="width:90px;">
                        <option value="UAH">
                            UAH
                        </option>
                        <option value="USD">
                            USD
                        </option>
                        <option value="EUR">
                            EUR
                        </option>
                    </select>

                </td>
                <td style="width: 100px;">Цена в грн</td>
                <td class="d-flex">
                    <a href="javascript:void(0)" class="btn btn-primary mr-2 btn-sm" @click="addProduct">Add</a>
                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" @click="addNewProduct = false ">Cancel</a>
                </td>
            </tr>
                <tr v-for="(product, index) in products" :key="index">
                    <td>{{index+1}}</td>
                    <td>
                        <div v-show="!isEdited(product.id)">
                            {{product.name}}
                        </div>
                        <div v-show="isEdited(product.id)">
                            <input type="text" v-model="editedProduct.name" class="form-control">
                        </div>
                    </td>

                    <td>
                        <div v-show="!isEdited(product.id)">
                            {{product.quantity}}
                        </div>
                        <div v-show="isEdited(product.id)">
                            <input type="number" min="0" max="999999" v-model="editedProduct.quantity" class="form-control">
                        </div>
                    </td>
                    <td>
                        <div v-show="!isEdited(product.id)">
                            {{product.date}}
                        </div>
                        <div v-show="isEdited(product.id)">
                            <input type="date" v-model="editedProduct.date" class="form-control">
                        </div>
                    </td>
                    <td>
                        <div v-show="!isEdited(product.id)">
                            {{product.supplier}}
                        </div>
                        <div v-show="isEdited(product.id)">
                            <input type="text" v-model="editedProduct.supplier" class="form-control">
                        </div>
                    </td>


                    <td>
                        <div v-show="!isEdited(product.id)">
                            {{product.buy_price}}
                        </div>
                        <div v-show="isEdited(product.id)">
                            <input type="number" min="0" max="999999" step="any" v-model="editedProduct.buy_price" class="form-control">
                        </div>
                    </td>


                    <td>
                        <div v-show="!isEdited(product.id)">
                            {{product.currency}}
                        </div>

                        <select v-show="isEdited(product.id)" v-model="editedProduct.currency" class="form-control" style="width:90px;">
                            <option value="UAH">
                                UAH
                            </option>
                            <option value="USD">
                                USD
                            </option>
                            <option value="EUR">
                                EUR
                            </option>
                        </select>
                    </td>

                    <td>
                        {{product.buy_price_uah}}
                    </td>


                    <td>
                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm" v-show="!isEdited(product.id)" @click="deleteProduct(product.id)">
                            Удалять
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" v-show="!isEdited(product.id)" @click="editProduct(product)">
                            Редактировать
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm" v-show="isEdited(product.id)"  @click="clearEditedProduct">
                            Отменить
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline-success btn-sm" v-show="isEdited(product.id)"  @click="saveEditedProduct(editedProduct)">
                            Сохранить
                        </a>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        data(){
            return{
                products:[],
                sortedProducts : [],
                newProduct:{
                    'name':'',
                    'quantity':'',
                    'currency':'UAH',
                    'buy_price':'',
                    'date':'',
                    'supplier':'',
                },
                addNewProduct:false,
                editedProduct:{
                    'id':'',
                    'name':'',
                    'quantity':'',
                    'currency':'',
                    'buy_price':'',
                    'date':'',
                    'supplier':''
                },
                sortByName:'',
                sortByDate:'',
                currency:'UAH',
                currentCurrency:'UAH',
                USD_rate:0,
                EUR_rate:0,
            }
        },
        computed:{

        },
        methods:{
            getProducts(){
                axios.get('/api/get-products')
                    .then( (response) => {
                        this.products = response.data ;
                        this.sortProductsByName();
                        $.each(products, (i) => {
                            products[i].edited = false ;
                            products[i].calculatedPrice = 0 ;
                        });
                    })
                    .catch( (error) => {
                        console.log(error);
                    });
            },
            addProduct(){
                axios.post('/products/add',this.newProduct)
                    .then( (response) => {
                        let product = response.data ;
                        this.products.unshift(response.data);
                        this.addNewProduct = false;
                        this.clearInputs();
                    })
                    .catch( (error) => {
                        console.log('Error : ' + error)
                    })
            },
            deleteProduct(product_id){
                if (!confirm('Are you sure you want to delete this product ?')) {
                    return;
                }
                axios.post('/products/delete',{'product_id' : product_id})
                    .then( (response) => {
                        let products = this.products;
                        $.each(products, (i) => {
                            if (products[i].id === product_id) {
                                products.splice(i, 1);
                                return false;
                            }
                        });
                    })
                    .catch( (error) => {
                        console.log('Error :' + error);
                    });
            },
            editProduct(product){
                this.editedProduct.id = product.id;
                this.editedProduct.name = product.name;
                this.editedProduct.quantity = product.quantity;
                this.editedProduct.currency = product.currency;
                this.editedProduct.buy_price = product.buy_price;
                this.editedProduct.date = product.date;
                this.editedProduct.supplier = product.supplier;
            },
            saveEditedProduct(product){
                axios.post('/products/update',product)
                    .then( (response) => {
                        let products = this.products ;
                        $.each(products, (i) => {
                            if (products[i].id === product.id) {
                                products[i] = response.data;
                                return false;
                            }
                        });
                        this.clearEditedProduct();
                    })
                    .catch( (error) => {
                        console.log('Error : ' + error)
                    })
            },
            clearInputs(){
                this.newProduct = {
                    'name':'',
                    'buy_price':''
                };
            },
            clearEditedProduct(){
                this.editedProduct = {
                    'id' : '',
                    'name':'',
                    'buy_price':'',
                    'date':'',
                    'supplier':'',
                };
            },
            isEdited(product_id){
                return this.editedProduct.id === product_id ;
            },
            sortProductsByName(){
                this.products = _.orderBy(this.products, ['name'] , [this.sortByName]);
            },
            sortProductsByDate(){
                this.products = _.orderBy(this.products, ['date'],[this.sortByDate]);
            },
            changeCurrency(){
                // get products by currency
                axios.get('api/products/' + this.currency).then( (response) => {
                    this.products = response.data ;
                    this.products = _.orderBy(this.products, ['name'] , [this.sortByName]);
                    this.currentCurrency = this.currency ;
                });
            },
            getCurrenciesRate(){
                axios.get('/api/currency/rate').then( (response) => {
                    console.log(response.data) ;
                    this.USD_rate = response.data.usd_rate;
                    this.EUR_rate = response.data.eur_rate;
                });
            }
        },
        mounted() {
            this.getProducts();
            this.getCurrenciesRate()
        }
    }
</script>
