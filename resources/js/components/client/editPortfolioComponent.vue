<template>

    <div>
        <h2>Изменить пароль</h2>

        <div class="form-group">
            <label>Текущий пароль</label>
            <input type="password" class="form-control" placeholder="Текущий пароль"
                   v-model="formData.current_password">
            <div class="error" v-if="errors.current_password">
                {{errors.current_password[0]}}
            </div>
        </div>
        <div class="form-group">
            <label>Новый пароль</label>
            <input type="password" class="form-control" placeholder="Новый пароль" v-model="formData.password">
            <div class="error" v-if="errors.password">
                {{errors.password[0]}}
            </div>
        </div>
        <div class="form-group">
            <label>Повторить пароль</label>
            <input type="password" class="form-control" placeholder="Повторить пароль"
                   v-model="formData.password_confirmation">
        </div>
        <button class="btn btn-primary" @click="submitForm">Сохранить</button>

        <div class="changesSavedText d-none" id="changesSaved">
            <span class="alert alert-success">
                Пароль изменен
            </span>
        </div>

    </div>

</template>

<script>
    export default {
        name: "editPortfolioComponent",
        props: ['user'],
        data() {
            return {
                errors: [],
                formData: {
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                }
            }
        },
        methods: {
            submitForm() {
                this.clearErrors();
                axios.post('/edit/profile/submit', this.formData)
                    .then((response) => {
                        console.log(response.data)
                        if (response.data.status === 'success') {
                            $('#changesSaved').fadeIn('slow');
                            $('#changesSaved').removeClass('d-none');
                            setTimeout(function () {
                                $('#changesSaved').fadeOut();
                            },2000);
                        }
                    })
                    .catch((error) => {
                        this.isLoading = false;
                        if (typeof error.response.data === 'object') {
                            this.errors = error.response.data.errors;
                        } else {
                            this.errors = ['Something went wrong. Please try again.'];
                        }
                    })
            },
            clearErrors() {
                $.each(this.errors, (error) => {
                    this.errors[error] = '';
                });

            },

        },
        mounted() {
            console.log('mounted');
            console.log(this.user);
        }
    }
</script>

<style scoped>
    .error {
        color: red;
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
