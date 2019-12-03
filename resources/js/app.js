/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('products-list', require('./components/admin/ProductsListComponent.vue').default);
Vue.component('managers-list', require('./components/admin/ManagersListComponent.vue').default);

Vue.component('sales-list', require('./components/SalesListComponent.vue').default);
Vue.component('add-sale', require('./components/AddSaleComponent.vue').default);
Vue.component('edit-client-portfolio', require('./components/client/editPortfolioComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

if ($("#productsList").length !== 0){
    let productsList = new Vue({
        el:'#productsList'
    });
}

if ($("#editPortfolioComponent").length !== 0){
    let editPortfolioComponent = new Vue({
        el:'#editPortfolioComponent'
    });
}

if ($("#managersList").length !== 0){
    let managersList = new Vue({
        el:'#managersList'
    });
}


if ($("#salesList").length !== 0){
    let salesList = new Vue({
        el:'#salesList'
    });
}
