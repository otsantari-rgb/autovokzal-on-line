<template>
    <div class="router-view">
        <div class="container">
            <h2 class="text-center">Регистрация</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form @submit.prevent="register">
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Имя</label>
                                    <input
                                        type="text"
                                        class="form-control input-size"
                                        id="name"
                                        v-model="form.name"
                                        autocomplete="name"
                                        required
                                    />
                                    <small v-if="errors.name" class="text-danger" v-for="(error, index) in errors.name" :key="index">
                                        {{ error }}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-form-label">Email</label>
                                    <input
                                        type="email"
                                        class="form-control input-size"
                                        id="email"
                                        v-model="form.email"
                                        autocomplete="username"
                                        required
                                    />
                                    <small v-if="errors.email" class="text-danger" v-for="(error, index) in errors.email" :key="index">
                                        {{ error }}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="col-form-label">Пароль</label>
                                    <div class="input-group">
                                        <input
                                            :type="showPassword ? 'text' : 'password'"
                                            class="form-control input-size"
                                            id="password"
                                            v-model="form.password"
                                            autocomplete="new-password"
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
                                    <small v-if="errors.password" class="text-danger" v-for="(error, index) in errors.password" :key="index">
                                        {{ error }}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation" class="col-form-label">Подтверждение пароля</label>
                                    <div class="input-group mb-4">
                                        <input
                                            :type="showPasswordConfirmation ? 'text' : 'password'"
                                            class="form-control input-size"
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            autocomplete="new-password"
                                            required
                                        />
                                        <div class="input-group-append">
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                @click="togglePasswordConfirmation"
                                            >
                                                <i :class="showPasswordConfirmation ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small v-if="errors.password_confirmation" class="text-danger" v-for="(error, index) in errors.password_confirmation" :key="index">
                                        {{ error }}
                                    </small>
                                </div>
                                <div class="form-group form-check">
                                    <label class="form-check-label ml-2" for="privacyPolicy">
                                        <input type="checkbox" id="privacyPolicy" v-model="form.acceptPolicy">
                                        <span class="checkmark"></span>
                                        Я принимаю <Link href="/policy">Политику обработки персональных данных</Link>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
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
import { reactive, ref } from "vue";
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useLoadingStore } from '@/stores/loading';
import { useAuthStore } from '@/stores/auth';
import {toast} from "vue3-toastify";

const loadingStore = useLoadingStore();
const authStore = useAuthStore();
const page = usePage();

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    acceptPolicy: false,
});

const errors = reactive({});
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmation = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};

const register = async () => {
    if (!form.acceptPolicy) {
        toast.warning("Для продолжения необходимо принять политику обработки персональных данных.");
        return;
    }
    Object.keys(errors).forEach((key) => delete errors[key]); // Очистка предыдущих ошибок
    try {
        loadingStore.setLoading(true);

        form.post('/register', {
            onSuccess: async () => {
                toast.success("Регистрация успешна!");
                authStore.setUser(usePage().props.auth.user);
            },
            onError: (errors) => {
                Object.keys(errors).forEach(key => {
                    toast.error(errors[key]);
                });
            },
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    } catch (error) {
        toast.error("Произошла ошибка при регистрации. Попробуйте позже.");
    } finally {
        loadingStore.setLoading(false);
    }
};
</script>

<style scoped>
.container{
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
.text-danger {
    align-self: start;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-check {
    flex-direction: row;
    align-items: center;
    max-width: none;
}

.form-group.form-check input {
    margin-right: 10px;
}

.form-check-label {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.form-check-label input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
    border-radius: 6px;
}

.form-check-label:hover input ~ .checkmark {
    background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.form-check-label input:checked ~ .checkmark {
    background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

/* Show the checkmark when checked */
.form-check-label input:checked ~ .checkmark:after {
    display: block;
}

/* Style the checkmark/indicator */
.form-check-label .checkmark:after {
    left: 8px;
    top: 3px;
    width: 9px;
    height: 15px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
</style>
