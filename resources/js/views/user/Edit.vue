<template>
    <div class="container-body">
        <h1>Редактировать профиль</h1>

        <div v-if="user">
            <form @submit.prevent="updateProfile">
                <div class="form-group">
                    <label for="name">Имя</label>
                    <input
                        type="text"
                        id="name"
                        v-model="user.name"
                        required
                    />
                    <small v-if="errors.name" class="text-danger">{{ errors.name }}</small>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        v-model="user.email"
                        required
                    />
                    <small v-if="errors.email" class="text-danger">{{ errors.email }}</small>
                </div>

                <!-- Кнопка для отображения/скрытия полей пароля -->
                <div class="form-group">
                    <button
                        type="button"
                        @click="togglePasswordFields"
                        class="btn-dark">
                        Изменить пароль
                    </button>
                </div>

                <!-- Поля для изменения пароля -->
                <div v-if="showPasswordFields">
                    <div class="form-group">
                        <label for="current_password">Текущий пароль</label>
                        <input
                            type="password"
                            id="current_password"
                            v-model="passwords.current_password"
                        />
                        <small v-if="errors.current_password" class="text-danger">{{ errors.current_password }}</small>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Новый пароль</label>
                        <input
                            type="password"
                            id="new_password"
                            v-model="passwords.new_password"
                        />
                        <small v-if="errors.new_password" class="text-danger">{{ errors.new_password }}</small>
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">Подтверждение пароля</label>
                        <input
                            type="password"
                            id="new_password_confirmation"
                            v-model="passwords.new_password_confirmation"
                        />
                        <small v-if="errors.new_password_confirmation" class="text-danger">{{ errors.new_password_confirmation }}</small>
                    </div>
                </div>

                <div class="button-wrapper">
                    <button type="submit" class="btn-success" :disabled="isUpdating">
                        {{ isUpdating ? "Сохраняю..." : "Сохранить изменения" }}
                    </button>
                    <button type="button" class="btn-primary" @click="goBack">Назад</button>
                </div>
            </form>
        </div>

        <div v-else>
            <p>Пользователь не найден.</p>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted, computed} from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { toast } from "vue3-toastify";
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const user = computed(() => authStore.user);
const errors = ref({});
const isUpdating = ref(false);
const passwords = ref({
    current_password: "",
    new_password: "",
    new_password_confirmation: "",
});
const showPasswordFields = ref(false);
const router = useRouter();

const togglePasswordFields = () => {
    showPasswordFields.value = !showPasswordFields.value; // Переключаем видимость полей
};

const updateProfile = async () => {
    try {
        isUpdating.value = true;

        // Делаем запрос для обновления профиля с паролем
        const formData = {
            name: user.value.name,
            email: user.value.email,
            current_password: passwords.value.current_password, // Если меняется пароль
            new_password: passwords.value.new_password,
            new_password_confirmation: passwords.value.new_password_confirmation
        };
        console.log(formData)
        const response = await axios.put("/api/user", formData, {
            headers: {
                Authorization: `Bearer ${authStore.getToken}`
            }
        });

        // Проверка, изменился ли email, и нужно ли отправить подтверждение
        if (response.data.email_changed) {
            toast.success('Письмо с подтверждением нового email отправлено. Пожалуйста, подтвердите новый адрес электронной почты.');
        } else {
            toast.success(response.data.message);
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            if (error.response.data.message) {
                toast.error(error.response.data.message);
            } else {
                const validationErrors = error.response.data.errors;

                // Перебираем каждое поле с ошибками
                for (const field in validationErrors) {
                    if (Object.hasOwn(validationErrors, field)) {
                        // Перебираем все сообщения об ошибках для этого поля
                        validationErrors[field].forEach((message) => {
                            toast.error(message); // Выводим каждое сообщение через toast.error
                        });
                    }
                }
            }
        } else {
            toast.error("Ошибка при обновлении профиля.");
        }
    } finally {
        isUpdating.value = false;
    }
};

const goBack = () => {
    router.push("/account/profile");
};

onMounted(authStore.fetchUser);
</script>

<style scoped>
.container-body {
    max-width: 1000px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="text"],
input[type="password"],
input[type="email"] {
    border: 1px solid #ced4da;
    border-radius: 5px;
    width: 70%;
    padding: 10px;
}

input[type="text"]:focus,
input[type="password"],
input[type="email"]:focus {
    border-color: #80bdff;
    outline: none;
}

.button-wrapper {
    display: flex;
    justify-content: space-between;
}

button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.form-group {
    justify-content: space-between;
}
</style>
