<template>
    <div class="router-view">
        <div class="container">
            <div class="verify-email">
                <h1 style="margin-bottom: 15px">Подтверждение почты</h1>
                <h5 style="margin-bottom: 15px">Письмо отправлено повторно</h5>
                <p>На ваш адрес электронной почты было отправлено повторно письмо для подтверждения. Пожалуйста, проверьте свою почту.</p>
                <button
                    @click="resendVerificationEmail"
                    class="btn btn-primary btn-lg"
                    :disabled="isSending"
                >
                    <span v-if="isSending">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Отправка...
                    </span>
                    <span v-else>Отправить письмо повторно</span>
                </button>
                <Link href="/" class="btn btn-warning text-dark btn-lg">Вернуться на главную</Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useLoadingStore } from '@/stores/loading';
import { toast } from "vue3-toastify";

const loadingStore = useLoadingStore();
const page = usePage();
const isSending = ref(false);

onMounted(() => {
    loadingStore.setLoading(false);

    if (page.props.toast) {
        toast[page.props.toast.type](page.props.toast.message);
    }
});

const resendVerificationEmail = async () => {
    isSending.value = true;
    try {
        await router.get('/email/resend-verify-email', {}, {
            preserveState: true,
            onSuccess: (page) => {
                if (page.props.toast) {
                    toast[page.props.toast.type](page.props.toast.message);
                }
            },
            onError: (errors) => {
                toast.error(errors.message || 'Произошла ошибка при отправке письма.');
            },
            onFinish: () => {
                isSending.value = false;
            }
        });
    } catch (error) {
        toast.error('Произошла ошибка при отправке письма.');
        isSending.value = false;
    }
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
