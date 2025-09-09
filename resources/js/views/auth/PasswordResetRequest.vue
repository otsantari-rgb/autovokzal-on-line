<template>
    <div class="router-view">
        <div class="container">
            <h2 class="text-center">Восстановление пароля</h2>
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <form @submit.prevent="sendResetLink">
                                <div class="form-group">
                                    <label for="email" class="col-form-label">Email</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        v-model="email"
                                        required
                                    />
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-4">
                                    Отправить ссылку
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';

const email = ref('');
const error = ref(null);

const sendResetLink = async () => {
    try {
        // Получение CSRF-токена
        await axios.get('/sanctum/csrf-cookie');

        // Отправка запроса на отправку ссылки для сброса пароля
        const response = await axios.post('/api/password/email', { email: email.value });

        // Успешное сообщение
        toast.success(response.data.message);
    } catch (err) {
        // Обработка ошибок
        if (err.response && err.response.data.message) {
            toast.error(err.response.data.message);
        } else {
            toast.error('Произошла ошибка. Попробуйте снова.');
        }
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
