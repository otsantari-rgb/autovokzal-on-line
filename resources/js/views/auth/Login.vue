<template>
    <div class="router-view">
        <div class="container">
            <h2 class="text-center">Вход</h2>
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <!-- Общая ошибка -->
                            <div v-if="errors.general" class="alert alert-danger">
                                {{ errors.general }}
                            </div>

                            <form @submit.prevent="login">
                                <div class="form-group">
                                    <label for="email" class="col-form-label">Email</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        v-model="form.email"
                                        @input="clearError('email')"
                                        autocomplete="username"
                                        required
                                    />
                                    <small v-if="errors.email" class="text-danger" v-for="(error, index) in errors.email" :key="index">
                                        {{ error }}
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="password" class="col-form-label">Пароль</label>
                                    <div class="input-group mb-4">
                                        <input
                                            :type="showPassword ? 'text' : 'password'"
                                            class="form-control"
                                            id="password"
                                            v-model="form.password"
                                            @input="clearError('password')"
                                            autocomplete="current-password"
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
                                    <small v-if="errors.password" class="text-danger" v-for="(message, index) in errors.password" :key="index">
                                        {{ message }}
                                    </small>
                                </div>
                                <div class="form-group form-check">
                                    <label class="form-check-label ml-2" for="privacyPolicy">
                                        <input type="checkbox" id="privacyPolicy" v-model="form.acceptPolicy">
                                        <span class="checkmark"></span>
                                        Я принимаю <Link href="/policy">Политику обработки персональных данных</Link>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Войти</button>
                            </form>

                            <div class="text-center mt-3">
                                <Link href="/register">Нет аккаунта? Зарегистрируйтесь!</Link>
                            </div>
                            <div class="text-center mt-3">
                                <Link href="/password/reset">Забыли пароль?</Link>
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
import { Link } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import { useAuthStore } from '@/stores/auth';
import { useLoadingStore } from '@/stores/loading';
import {toast} from "vue3-toastify";

const authStore = useAuthStore();
const loadingStore = useLoadingStore();

const form = useForm({
    email: "",
    password: "",
    acceptPolicy: false,
});

const errors = reactive({});
const showPassword = ref(false);

const props = defineProps({
    failed: {
        type: String,
        required: false
    }
});

const login = async () => {
    if (!form.acceptPolicy) {
        toast.warning("Для продолжения необходимо принять политику обработки персональных данных.");
        return;
    }
    loadingStore.setLoading(true);
    Object.keys(errors).forEach((key) => delete errors[key]);

    form.post('/login', {
        onSuccess: async () => {
            toast.success('Вход выполнен успешно');

        },
        onError: (errors) => {
            Object.keys(errors).forEach(key => {
                toast.error(errors[key]);
            });
        },
        onFinish: () => {
            form.reset('password');
            loadingStore.setLoading(false);
        }
    });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const clearError = (field) => {
    if (errors[field]) {
        delete errors[field];
    }
};
</script>

<style scoped>
.container {
    max-width: 600px;
    margin: 0 auto;
    padding-top: 50px;
    padding-bottom: 50px;
}
label {
    width: 100%;
    margin-bottom: 5px;
}
.form-group {
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
}

.text-danger {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid #f5c6cb;
    border-radius: 0.25rem;
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
