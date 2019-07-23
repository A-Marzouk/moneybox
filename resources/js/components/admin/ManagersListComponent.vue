<template>
    <div class="container pb-5">
        <div class="d-flex justify-content-between">
            <h2 class="pb-3">Managers list</h2>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Percentage</th>
                    <th>Total products sold</th>
                    <th>Plan</th>
                    <th>Actions</th>
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
                            <input type="number" min="0" max="999999" step="any" v-model="editedManager.percentage" class="form-control">
                        </div>
                    </td>
                    <td> COMING SOON </td>
                    <td>
                        <div  v-show="!isEdited(manager.id)">
                            {{manager.client.plan}}
                        </div>
                        <div  v-show="isEdited(manager.id)">
                            <input type="number" min="0" max="999999" step="any" v-model="editedManager.plan" class="form-control">
                        </div>
                    </td>
                    <td class="d-flex">
                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm mr-2" v-show="!isEdited(manager.id)" @click="deleteManager(manager.id)">
                            Del
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" v-show="!isEdited(manager.id)" @click="editManager(manager)">
                            Edit
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm mr-2" v-show="isEdited(manager.id)"  @click="clearEditedManager">
                            Cancel
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline-success btn-sm" v-show="isEdited(manager.id)"  @click="saveEditedManager(editedManager)">
                            Save
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
                managers:[],
                newManager:{},
                editedManager:{
                    'id': '',
                    'name': '',
                    'percentage': '',
                    'plan': '',
                }
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
                        console.log('Error : ' + error)
                    })
            },
            clearInputs(){
                this.newManager = {
                    'name':'',
                    'plan':'',
                    'percentage':''
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
            }
        },
        mounted() {
            this.getManagers();
        }
    }
</script>
