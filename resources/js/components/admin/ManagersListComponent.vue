<template>
    <div class="container pb-5">
        <div class="d-flex justify-content-between">
            <h2 class="pb-3">Managers list</h2>
            <div>
                <a href="javascript:void(0)" @click="addNewManager = true" class="btn btn-outline-dark mr-3">Добавить менеджер</a>
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
                    <th>Имя</th>
                    <th>Процент</th>
                    <th>План</th>
                    <th>Эл. адрес</th>
                    <th>Пароль</th>
                    <th>Действия</th>
                </tr>

            </thead>
            <tbody>
                <tr v-for="(manager, index) in managers" :key="index">
                    <td>{{index+1}}</td>
                    <td>
                        <div  v-show="!isEdited(manager.id)">
                            {{manager.name}}
                        </div>
                        <div  v-show="isEdited(manager.id)">
                            <input type="text" v-model="editedManager.name" class="form-control">
                        </div>
                    </td>
                    <td>
                        <div  v-show="!isEdited(manager.id)">
                            {{manager.client.percentage}} %
                        </div>
                        <div  v-show="isEdited(manager.id)">
                            <input type="number" min="0" max="99" step="any" v-model="editedManager.percentage" class="form-control">
                        </div>
                    </td>
                    <td>
                        <div  v-show="!isEdited(manager.id)">
                            {{manager.client.plan}}
                        </div>
                        <div  v-show="isEdited(manager.id)">
                            <input type="number" min="0" max="999999" step="any" v-model="editedManager.plan" class="form-control">
                        </div>
                    </td>
                    <td>
                        <div  v-show="!isEdited(manager.id)">
                            {{manager.email}}
                        </div>
                    </td>
                    <td>
                        <div  v-show="!isEdited(manager.id)">
                            <span v-if="isShown(manager.id)">
                                {{manager.plain_password}}
                            </span>
                            <span v-else>
                                <a href="javascript:void(0)" @click="showPassword(manager.id)">
                                    ******
                                </a>
                            </span>
                        </div>
                    </td>
                    <td class="d-flex">
                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm mr-2" v-show="!isEdited(manager.id)" @click="deleteManager(manager.id)">
                            Удалять
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" v-show="!isEdited(manager.id)" @click="editManager(manager)">
                            Редактировать
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm mr-2" v-show="isEdited(manager.id)"  @click="clearEditedManager">
                            Отменить
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline-success btn-sm" v-show="isEdited(manager.id)"  @click="saveEditedManager(editedManager)">
                            Сохранить
                        </a>
                    </td>
                </tr>
                <tr v-show="addNewManager">
                    <td>
                        {{this.managers.length + 1}}
                    </td>

                    <td>
                        <input type="text" v-model="newManager.name" class="form-control">
                    </td>

                    <td>
                        <input type="number" min="0" max="99" step="any" v-model="newManager.percentage" class="form-control">
                    </td>
                    <td>
                        <input type="number" min="0" max="999999" step="any" v-model="newManager.plan" class="form-control">
                    </td>
                    <td>
                        <input type="email" name="newEmail" v-model="newManager.email" class="form-control">
                    </td>
                    <td>
                        <input type="password" name="newPassword" v-model="newManager.password" class="form-control">
                    </td>
                    <td class="d-flex">
                        <a href="javascript:void(0)" class="btn btn-primary mr-2 btn-sm" @click="addManager">Add</a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" @click="addNewManager = false ">Cancel</a>
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
                managers:[],
                newManager:{
                    'name': '',
                    'email': '',
                    'password': '',
                    'percentage': '',
                    'plan': '',
                },
                editedManager:{
                    'id': '',
                    'name': '',
                    'percentage': '',
                    'plan': '',
                },
                addNewManager:false,
                shownPasswordManagerID:'',
                errors: {},
            }
        },
        methods:{
            getManagers(){
                axios.get('/api/get-managers')
                    .then( (response) => {
                        this.managers = response.data ;
                    })
                    .catch( (error) => {
                        console.log(error);
                    });
            },
            deleteManager(manager_id){
                if (!confirm('Are you sure you want to delete this manager ?!')) {
                    return;
                }
                axios.post('/managers/delete',{'manager_id' : manager_id})
                    .then( (response) => {
                        let managers = this.managers;
                        $.each(managers, (i) => {
                            if (managers[i].id === manager_id) {
                                managers.splice(i, 1);
                                return false;
                            }
                        });
                    })
                    .catch( (error) => {
                        console.log('Error :' + error);
                    });
            },
            editManager(manager){
                this.editedManager.id         = manager.id;
                this.editedManager.name       = manager.name;
                this.editedManager.plan       = manager.client.plan;
                this.editedManager.percentage = manager.client.percentage;
            },
            saveEditedManager(manager){
                axios.post('/managers/update',manager)
                    .then( (response) => {
                        let managers = this.managers ;
                        $.each(managers, (i) => {
                            if (managers[i].id === manager.id) {
                                managers[i] = response.data;
                                return false;
                            }
                        });
                        this.clearEditedManager();
                    })
                    .catch( (error) => {
                        console.log('Error : ' + error.response.data.errors)
                    })
            },
            addManager(){
                this.errors = {} ;
                axios.post('/managers/add',this.newManager)
                    .then( (response) => {
                        this.managers.push(response.data);
                        this.addNewManager = false;
                        this.clearInputs();
                    })
                    .catch( (error) => {
                        this.errors = error.response.data.errors;
                    })
            },
            clearInputs(){
                this.newManager = {
                    'name':'',
                    'plan':'',
                    'percentage':'',
                    'password':'',
                    'email':''
                };
            },
            clearEditedManager(){
                this.editedManager = {
                    'id': '',
                    'name': '',
                    'percentage': '',
                    'plan': '',
                };
            },
            isEdited(manager_id){
                return this.editedManager.id === manager_id ;
            },
            isShown(manager_id){
                return this.shownPasswordManagerID === manager_id ;
            },
            showPassword(manager_id){
                this.shownPasswordManagerID = manager_id;
                setTimeout(() => {
                    this.shownPasswordManagerID = '' ;
                },3000);
            }
        },
        mounted() {
            this.getManagers();
        }
    }
</script>
