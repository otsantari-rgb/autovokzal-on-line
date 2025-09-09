<template>
    <div class="router-view">
        <div class="container">
            <div class="email-verification">
                <div v-if="isLoading">
                    <p>Проверяем почту, пожалуйста, подождите...</p>
                </div>
                <div v-else>
                    <div v-if="status === 'verified' || status === 'already_verified'" class="success-message">
                        <h1>{{ title }}</h1>
                        <p>{{ status === 'verified' ? 'Спасибо, ваша почта успешно подтверждена.' : 'Ваш email уже был подтвержден ранее.' }}</p>
                        <Link href="/" class="btn btn-primary">Перейти на главную</Link>
                    </div>

                    <div v-else class="error-message">
                        <h1>{{ title }}</h1>
                        <p>{{ errorMessage }}</p>
                        <button v-if="showResendButton" @click="resendVerification" class="btn btn-primary">
                            Отправить повторно
                        </button>
                        <Link href="/" class="btn btn-warning">Вернуться на главную</Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Link, router, usePage } from '@inertiajs/vue3';
import { toast } from "vue3-toastify";

const page = usePage();
const isLoading = ref(false);

// Получаем данные из пропсов Inertia
const title = page.props.title || 'Подтверждение почты';
const status = page.props.status || 'error';
const errorMessage = page.props.errorMessage || 'Ошибка подтверждения почты';
const showResendButton = page.props.showResendButton || false;

// Отображаем toast при загрузке страницы, если он есть
onMounted(() => {
    if (page.props.toast) {
        toast[page.props.toast.type](page.props.toast.message);
    }
});

const resendVerification = () => {
    isLoading.value = true;
    router.post('/email/resend', {}, {
        onSuccess: () => {
            toast.success('Ссылка для подтверждения отправлена повторно');
            isLoading.value = false;
        },
        onError: (errors) => {
            toast.error(errors.message || 'Ошибка при отправке ссылки');
            isLoading.value = false;
        }
    });
};
</script>


<style scoped>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding-top: 50px;
    padding-bottom: 50px;
}
.email-verification {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    background: #FFFFFF;
    padding-top: 50px;
    padding-bottom: 50px;
    border-radius: 20px;
    border: 1px solid #ddd;
}

.success-message h1 {
    color: green;
}

.error-message h1 {
    color: red;
}

.btn {
    margin-top: 20px;
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    text-decoration: none;
}

.btn-primary {
    background-color: #007bff;
    color: #fff;
    border: none;
}

.btn-warning {
    background-color: #ffc107;
    color: #212529;
    border: none;
}
</style>
