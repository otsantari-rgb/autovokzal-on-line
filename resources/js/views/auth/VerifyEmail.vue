<template>
    <div class="router-view">
        <div class="container">
            <div class="verify-email">
                <h1>Почта не подтверждена</h1>
                <p>На ваш адрес электронной почты было отправлено письмо для подтверждения. Пожалуйста, проверьте свою почту.</p>
                <button @click="resendVerificationEmail" :disabled="loading" class="btn btn-primary btn-lg">
                    {{ loading ? 'Отправка...' : 'Отправить письмо повторно' }}
                </button>
                <Link href="/" class="btn btn-warning text-dark btn-lg">Вернуться на главную</Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';

const loading = ref(false);
const page = usePage();

// Проверяем, есть ли toast сообщение от сервера
onMounted(() => {
    if (page.props.toast) {
        toast[page.props.toast.type](page.props.toast.message);
    }
});

const resendVerificationEmail = () => {
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
</script>

<style scoped>
.container{
    padding-top: 50px;
    padding-bottom: 50px;
    max-width: 800px;
}
.verify-email {
    display: flex;
    flex-direction: column;
    text-align: center;
    background: #FFFFFF;
    border-radius: 20px;
    padding: 50px 20px;
}
.verify-email h1 {
    font-size: 30px;
}
.verify-email p {
    font-size: 18px;
}
.verify-email a {
    display: inline-block;
    margin-top: 20px;
    font-size: 16px;
    color: #FFFFFF;
}
.btn {
    margin-top: 20px;
}
</style>
