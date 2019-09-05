<template>
    <div class="container">
        <div class="d-flex justify-content-between">
            <h2 class="pb-3">Список товаров</h2>
            <div class="d-flex">
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
                    <th>Дата</th>
                    <th>Поставщик</th>
                    <th>Цена покупки</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
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
                <tr v-show="addNewProduct">
                    <td>
                        {{this.products.length + 1}}
                    </td>

                    <td>
                        <input type="text" v-model="newProduct.name" class="form-control">
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
                    <td class="d-flex">
                        <a href="javascript:void(0)" class="btn btn-primary mr-2 btn-sm" @click="addProduct">Add</a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" @click="addNewProduct = false ">Cancel</a>
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
                newProduct:{
                    'name':'',
                    'buy_price':'',
                    'date':'',
                    'supplier':'',
                },
                addNewProduct:false,
                editedProduct:{
                    'id':'',
                    'name':'',
                    'buy_price':'',
                    'date':'',
                    'supplier':''
                }
            }
        },
        methods:{
            getProducts(){
                axios.get('/api/get-products')
                    .then( (response) => {
                        this.products = response.data ;
                        $.each(products, (i) => {
                            products[i].edited = false ;
                        });
                    })
                    .catch( (error) => {
                        console.log(error);
                    });
            },
            addProduct(){
                axios.post('/products/add',this.newProduct)
                    .then( (response) => {
                        this.products.push(response.data);
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
                                products[i] = product;
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
            }
        },
        mounted() {
            this.getProducts();
        }
    }
</script>
