<template>
    <div class="router-view">
        <div class="container order-details">
            <div class="order-info">
                <h1 v-if="order">Заказ №{{ order.id }}</h1>
                <p v-if="order"><strong>Статус заказа:</strong> {{ order.translated_status }}</p>
                <p v-if="order"><strong>Дата создания заказа:</strong> {{ dayjs(order.created_at).format('DD.MM.YYYY HH:mm') }}</p>
                <button v-if="order?.status === 'confirmed'" class="btn btn-outline-primary" @click="openReceipt(order)">Показать чек оплаты</button>
            </div>
            <p><strong>Подробности о билетах:</strong></p>
            <div v-if="loading">Загрузка...</div>
            <div v-else-if="error">{{ error }}</div>
            <div v-else>
                <div v-if="order && order.tickets.length === 0">
                    <p>Нет купленных билетов для этого заказа.</p>
                </div>
                <div v-else>
                    <div v-if="order.tickets.length > 0" v-for="ticket in order.tickets" :key="ticket.id" class="col mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Билет №{{ ticket.id }}</h5>
                                <p class="card-text"><strong>Статус:</strong> {{ ticket.translated_status }}</p>
                                <p class="card-text"><strong>Пассажир:</strong> {{ ticket.passenger_name }}</p>
                                <p class="card-text"><strong>Маршрут:</strong> {{ ticket.route_name }}</p>
                                <p class="card-text"><strong>Место:</strong> {{ ticket.place }}</p>
                                <p class="card-text"><strong>Дата поездки:</strong> {{ dayjs(ticket.departure_date).format('DD.MM.YYYY') }}</p>
                                <p class="card-text"><strong>Время отправления:</strong> {{ ticket.departure_time }}</p>
                                <p class="card-text"><strong>Дата прибытия:</strong> {{ dayjs(ticket.arrival_date).format('DD.MM.YYYY') }}</p>
                                <p class="card-text"><strong>Время прибытия:</strong> {{ ticket.arrival_time }}</p>
                                <button v-if="ticket.status === 'confirmed'" @click="handleRefund(ticket.uuid)" class="btn btn-outline-danger mt-2 mr-2">
                                    Оформить возврат
                                </button>
                                <button @click="handleViewTicket(ticket.uuid, ticket.status, ticket.translated_status)" class="btn btn-primary mt-2">
                                    Посмотреть билеты
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <Link href="/account/orders" class="btn btn-secondary back-to-orders-button">Назад к заказам</Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { usePage, Link, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import 'dayjs/locale/ru';
import { toast } from 'vue3-toastify';
import AccountLayout from '../user/Account.vue'
import AppLayout from '@/App.vue'
defineOptions({
    layout: [AppLayout, AccountLayout]
})

const page = usePage();
const order = computed(() => page.props.order || null);
const error = computed(() => page.props.error || null);
const loading = ref(false);

const handleRefund = (ticketUUID) => {
    router.visit(`/refund/${ticketUUID}`);
};

const handleViewTicket = (ticketUUID, ticketStatus) => {
    if (ticketStatus === 'confirmed') {
        window.location.href = `/tickets/pdf/${ticketUUID}`;
    } else {
        toast.error('Ваш билет аннулирован');
    }
};

const openReceipt = (order) => {
    window.open(order.receipt.ofd_receipt_url, '_blank');
};
</script>

<style scoped>
.container {
    max-width: 1000px;
    margin: 0 auto;
    padding-top: 50px;
    padding-bottom: 50px;
}
.back-to-orders-button {
    margin-left: 1.25rem;
}
.order-details {
    padding: 20px;
}
.order-info {
    padding: 1.25rem;
}
</style>
