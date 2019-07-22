<template>
    <div class="container">
        <h2>Products list</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Buy price</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(product, index) in products" :key="index">
                    <td>{{index+1}}</td>
                    <td>{{product.name}}</td>
                    <td>{{product.buy_price}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        data(){
            return{
                products:[]
            }
        },
        methods:{
            getProducts(){
                axios.get('/api/get-products')
                    .then( (response) => {
                        this.products = response.data ;
                    })
                    .catch( (error) => {
                        console.log(error);
                    });
            }
        },
        mounted() {
            this.getProducts();
        }
    }
</script>
