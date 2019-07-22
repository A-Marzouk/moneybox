<template>
    <div class="container">
        <h2>Managers list</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Percentage</th>
                    <th>Total products sold</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(manager, index) in managers" :key="index">
                    <td>{{index+1}}</td>
                    <td>{{manager.name}}</td>
                    <td>{{manager.client.percentage}} % </td>
                    <td> COMING SOON </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        data(){
            return{
                managers:[]
            }
        },
        methods:{
            getManagers(){
                axios.get('/api/get-managers')
                    .then( (response) => {
                        console.log(response.data);
                        this.managers = response.data ;
                    })
                    .catch( (error) => {
                        console.log(error);
                    });
            }
        },
        mounted() {
            this.getManagers();
        }
    }
</script>
