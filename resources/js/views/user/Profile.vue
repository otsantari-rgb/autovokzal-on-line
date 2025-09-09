<template>
    <div class="router-view">
        <h1 class="account-title">Профиль пользователя</h1>

        <div v-if="user" class="user-info">
            <div class="card">
                <div class="card-body">
                    <p class="card-title">Информация о пользователе</p>
                    <p class="card-text">Имя: {{ user.name }}</p>
                    <p class="card-text">Email: {{ user.email }}</p>
                    <p class="card-text">Дата регистрации: {{ formatDate(user.created_at) }}</p>

                    <!-- Информация о подтверждении почты -->
                    <p class="card-text">
                        Статус подтверждения почты:
                        <span v-if="user.email_verified_at" class="text-success">Подтверждено</span>
                        <span v-else class="text-danger">Не подтверждено</span>
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <Link href="/account/edit" class="btn btn-primary">Редактировать профиль</Link>
            </div>

            <div v-if="!user.email_verified_at" class="alert alert-warning mt-3">
                <strong>Внимание!</strong> Проверьте свою почту и подтвердите адрес электронной почты!
                <form @submit.prevent="resendVerification" class="mt-2">
                    <button type="submit" class="btn btn-warning" :disabled="isResending">
                        {{ isResending ? 'Отправка...' : 'Повторно отправить письмо с подтверждением' }}
                    </button>
                </form>
            </div>
        </div>

        <div v-else>
            <p>Пользователь не найден.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { toast } from "vue3-toastify";
import { Link, router, usePage} from "@inertiajs/vue3";
import AccountLayout from './Account.vue'
import AppLayout from '@/App.vue'

defineOptions({
    layout: [AppLayout, AccountLayout]
})

const user = computed(() => page.props.user);
const isResending = ref(false);

const loading = ref(false);
const page = usePage();

// Проверяем, есть ли toast сообщение от сервера
onMounted(() => {
    if (page.props.toast) {
        toast[page.props.toast.type](page.props.toast.message);
    }
});

const resendVerification = () => {
    loading.value = true;

    // Используем Inertia для отправки запроса
    router.get('/email/resend-verify-email', {}, {
        onSuccess: (response) => {
            // toast.success('Письмо с подтверждением отправлено повторно. Проверьте свою почту.');
            loading.value = false;
        },
        onError: (error) => {
            toast.error(error.message || 'Ошибка при отправке письма. Попробуйте позже.');
            loading.value = false;
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};
// Форматируем дату в нужный формат
const formatDate = (dateString) => {
    const options = { year: "numeric", month: "2-digit", day: "2-digit" };
    return new Date(dateString).toLocaleDateString("ru-RU", options);
};
</script>

<style scoped>
.router-view{
    display: flex;
    flex-direction: column;
}
.account-title {
    align-self: start;
    padding-left: 15px;
    font-weight: bold;
}
.user-info {
    width: 90%;
}
.card {
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 12px -4px hsla(60, 4%, 60%, .35), 0 0 2px hsla(60, 4%, 60%, .3);
}
.card-body {
    padding: 16px;
}
.card-title {
    font-size: 1.75rem;
    margin-bottom: 8px;
    font-weight: bold;
}
</style>
