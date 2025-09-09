<template>
    <div>
        <div class="container">
            <div class="payment-info">
                <!-- Успешная оплата -->
                <div v-if="paymentStatus === 'succeeded'" class="status-card success">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M5.193 6h13.868l-.596-1.638a3 3 0 0 0-3.845-1.793L5.193 6Z"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5 8a3 3 0 0 0-3 3v6a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-1a2 2 0 1 1 0-4v-1a3 3 0 0 0-3-3H5Zm4.207 8.707 4.5-4.5-1.414-1.414L8.5 14.586l-1.793-1.793-1.414 1.414 2.5 2.5h1.414Z"></path>
                    </svg>
                    <h2>Вы оплатили билет! 🎉</h2>
                    <p>Ваш билет придёт вам на почту <strong>{{ email }}</strong>.</p>
                    <p>Если он не пришёл, проверьте папку <strong>«Спам»</strong> или напишите нам на
                        <a href="mailto:autovokzal@biletavto.ru">autovokzal@biletavto.ru</a>.
                    </p>
                </div>

                <!-- Ожидание подтверждения -->
                <div v-else-if="paymentStatus === 'waiting_for_capture'" class="status-card pending">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z"></path>
                    </svg>
                    <h2>Оплата принята, ожидает подтверждения</h2>
                    <p>Ваш платёж принят! Он находится в процессе подтверждения.</p>
                    <p>Пожалуйста, подождите немного – скоро всё будет готово.</p>
                </div>

                <!-- В процессе оплаты -->
                <div v-else-if="paymentStatus === 'pending'" class="status-card processing">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm5 11h-5V7h2v4h3Z"></path>
                    </svg>
                    <h2>Оплата в процессе...</h2>
                    <p>Мы ждём подтверждения вашего платежа.</p>
                    <div class="mb-3">
                        <button @click="goToPay(paymentUrl)" class="go-to-pay-button">Перейти на страницу оплаты</button>
                    </div>
                </div>

                <!-- Отмена платежа -->
                <div v-else-if="paymentStatus === 'canceled'" class="status-card error">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm5.707 14.707-1.414 1.414L12 13.414l-4.293 4.293-1.414-1.414L10.586 12 6.293 7.707l1.414-1.414L12 10.586l4.293-4.293 1.414 1.414L13.414 12Z"></path>
                    </svg>
                    <h2>Оплата отменена ❌</h2>
                    <p>К сожалению, платёж не был завершён.</p>
                    <p>Попробуйте снова или свяжитесь с поддержкой.</p>
                </div>

                <!-- Ошибка -->
                <div v-else class="status-card error">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z"></path>
                    </svg>
                    <h2>Ошибка получения статуса платежа ❌</h2>
                    <p>Не удалось получить статус оплаты. Попробуйте позже.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import {toast} from "vue3-toastify";

const props = defineProps({
    uuid: String, // Определяем prop для uuid
});

const paymentStatus = ref('pending');
const email = ref('');
const translatedStatus = ref('')
const paymentUrl = ref('');

const fetchPaymentStatus = async () => {
    try {
        const response = await axios.get(`/api/payment-status/${props.uuid}`);
        paymentStatus.value = response.data.status;
        paymentUrl.value = response.data.payment_url;
        email.value = response.data.email;
        translatedStatus.value = response.data.translated_status
        toast.info('Статус платежа: '+ translatedStatus.value)
        console.log('Статус платежа:'+ response.data.status);
    } catch (error) {
        paymentStatus.value = 'error';
        console.error('Ошибка получения статуса платежа:', error);
        console.error('Ошибка получения статуса платежа:'+ error);
        toast.error('Ошибка получения статуса платежа:'+ error);
    }
};

const goToPay = (paymentUrl) => {
    window.location.href = paymentUrl
};

onMounted(fetchPaymentStatus);
</script>

<style scoped>
.container {
    max-width: 600px;
    margin: 0 auto;
    padding-top: 50px;
    padding-bottom: 50px;
}

.payment-info {
    background: #ffffff;
    padding: 40px;
    border-radius: 20px;
    text-align: center;
}

.status-card {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.status-card.success {
    color: #007bff;
}

.status-card.processing {
    color: #856404;
}

.status-card.pending {
    color: #0c5460;
}

.status-card.error {
    color: #721c24;
}

.status-card svg {
    margin-bottom: 15px;
}

.go-to-pay-button {
    height: 60px;
    padding: 9px 20px 9px 20px;
    border: none;
    border-radius: 16px;
    background: #fa742d;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    color: #FFFFFF;
    line-height: 25px;
    font-weight: 600;
    font-family: -apple-system, BlinkMacSystemFont, Inter, Roboto, Helvetica, Arial, sans-serif;
}

.go-to-pay-button:hover {
    background: #e45c24;
}
</style>
