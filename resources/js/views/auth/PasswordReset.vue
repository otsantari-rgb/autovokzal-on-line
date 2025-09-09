<template>
    <div class="router-view">
        <div class="container">
            <h2 class="text-center">Введите новый пароль!</h2>
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <form @submit.prevent="resetPassword">
                                <input type="hidden" v-model="form.token" disabled />
                                <div class="form-group">
                                    <label for="email" class="col-form-label">Email</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        v-model="form.email"
                                        required
                                        readonly
                                    />
                                    <small v-if="errors.email" class="text-danger">{{ errors.email }}</small>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="col-form-label">Пароль</label>
                                    <div class="input-group">
                                        <input
                                            :type="showPassword ? 'text' : 'password'"
                                            class="form-control"
                                            id="password"
                                            v-model="form.password"
                                            required
                                        />
                                        <div class="input-group-append">
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                @click="togglePassword"
                                            >
                                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small v-if="errors.password" class="text-danger">{{ errors.password }}</small>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation" class="col-form-label">Подтверждение пароля</label>
                                    <div class="input-group mb-4">
                                        <input
                                            :type="showPasswordConfirmation ? 'text' : 'password'"
                                            class="form-control"
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            required
                                        />
                                        <div class="input-group-append">
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                @click="togglePasswordConfirmation"
                                            >
                                                <i
                                                    :class="showPasswordConfirmation ? 'fas fa-eye-slash' : 'fas fa-eye'"
                                                ></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small
                                        v-if="errors.password_confirmation"
                                        class="text-danger"
                                    >{{ errors.password_confirmation }}</small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-4">
                                    Сбросить пароль
                                </button>
                            </form>
                            <div class="text-center mt-3">
                                <Link href="/login">Уже есть аккаунт? Войдите!</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import {toast} from "vue3-toastify";
import { Link } from '@inertiajs/vue3';
import { useForm, router, usePage } from '@inertiajs/vue3';

const page = usePage();

const form = useForm({
        token: page.props.token || '',
        email: page.props.email || '',
        password: '',
        password_confirmation: '',
});

const errors = ref({});
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const resetPassword = () => {
    form.post(`/password/reset`, {
        onSuccess: () => {
            toast.success('Пароль успешно изменен!');
            router.visit('/login');
        },
        onError: (errors) => {
            Object.values(errors).forEach(error => {
                toast.error(error);
            });
        }
    });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmation = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};
</script>

<style scoped>
.container {
    max-width: 600px;
    margin: 0 auto;
    padding-top: 50px;
    padding-bottom: 50px;
}
.form-group {
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
}
label {
    width: 100%;
    margin-bottom: 5px;
}
</style>

