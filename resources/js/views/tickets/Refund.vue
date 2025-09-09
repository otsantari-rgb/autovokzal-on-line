<template>
    <div class="router-view">
        <div class="container">
            <h1>Оформление возврата</h1>
            <div v-if="ticket" class="col">
                <div class="col mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <p>В случае отмены поездки действуют следующие Правила возврата:</p>
                                <ul v-if="refundLimit">
                                    <li class="text-danger">
                                        На данный маршрут оформить возврат билета возможно не позднее, чем за {{ getRefundTimeText(refundLimit) }} до отправления.
                                        При оформлении возврата удержится 5% от стоимости проезда (сервисные сборы возврату не подлежат)
                                    </li>
                                </ul>
                                <ul v-else>
                                    <li>
                                        Возврат билета более чем за 2 часа до отправления — удерживается 5% от стоимости проезда (сервисные сборы возврату не подлежат);
                                    </li>
                                    <li>
                                        Возврат билета в течение 2 часов до отправления — удерживается 15% от стоимости проезда (сервисные сборы возврату не подлежат).<br>
                                        <b>Внимание!</b> Менее, чем за полчаса до отправления автобуса возврат билета оформляется только через подачу письменного заявления на кассах автовокзала;
                                    </li>
                                    <li>
                                        В случае отмены рейса возвращается 100% от стоимости билета.
                                    </li>
                                    <li>
                                        Для возврата средств, нажмите кнопку "Возврат билета".

                                        Обращаем внимание на то, что возврат средств производится в течение 2-5 рабочих дней.
                                    </li>
                                </ul>
                            </div>
                            <h5 class="card-title">Билет №{{ ticket.id }}</h5>
                            <p class="card-text"><strong>Статус:</strong> {{ ticket.translated_status }}</p>
                            <p class="card-text"><strong>Пассажир:</strong> {{ ticket.passenger_name }}</p>
                            <p class="card-text"><strong>Маршрут:</strong> {{ ticket.route_name }}</p>
                            <p class="card-text"><strong>Место:</strong> {{ ticket.place }}</p>
                            <p class="card-text"><strong>Дата и время поездки:</strong> {{ formatDateTime(ticket.departure_date, ticket.departure_time) }}</p>
                            <button class="btn btn-primary" @click="refundTicket()">Оформить возврат</button>
                        </div>
                    </div>
                </div>
            </div>
            <h1 v-else>У вас пока нет билетов.</h1>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import {toast} from "vue3-toastify";
import { useLoadingStore } from '@/stores/loading';
import { useAuthStore } from '@/stores/auth';
import dayjs from 'dayjs';
import duration from 'dayjs/plugin/duration';
import 'dayjs/locale/ru';
import { getRefundTimeText } from '../../utils/timeUtils.js'

dayjs.extend(duration);
dayjs.locale('ru');

const props = defineProps({
    ticket: Object,
    status: String,
    refund_limit: Number|null,
    error: String
});

const loadingStore = useLoadingStore();
const ticket = props.ticket;
const refundLimit = props.refund_limit;

const formatDateTime = (date, time) => {
    return dayjs(`${date} ${time}`, "DD.MM.YYYY HH:mm:ss").format("DD MMMM YYYYг. в HH:mm");
};

const extractErrorMessages = (obj) => {
    let messages = [];

    for (const key in obj) {
        if (typeof obj[key] === 'object' && obj[key] !== null) {
            messages = messages.concat(extractErrorMessages(obj[key])); // рекурсивный вызов для вложенных объектов
        } else {
            messages.push(obj[key]);
        }
    }

    return messages;
};

const refundTicket = async () => {
    loadingStore.setLoading(true);
    try {
        router.post('/ticket/refund', {
            uuid: props.ticket.uuid,
            type: 'fromPersonal',
        }, {
            preserveScroll: true,
            onSuccess: (page) => {
                const response = page.props.flash?.response
                if (response.status === 'success') {
                    toast.success(response.message);
                } else {
                    toast.info(response.message);
                }
            },
            onError: (errors) => {
                const errorMessages = extractErrorMessages(errors);
                toast.error(errorMessages.join(', ') || 'Произошла ошибка при оформлении возврата!');
            },
            onFinish: () => {
                loadingStore.setLoading(false);
            }
        });
    } catch (error) {
        toast.error('Неизвестная ошибка при возврате билета' + error);
        loadingStore.setLoading(false);
    }
};
</script>

<style scoped>
.container {
    max-width: 1000px;
    margin: 0 auto;
    padding-top: 50px;
    padding-bottom: 50px;
}
</style>
