<template>
    <div class="tickets-container">
        <h2 class="title">Список билетов</h2>

        <div class="filters">
            <label>Выберите период:</label>
            <flat-pickr
                v-model="dateRange"
                :config="flatpickrConfig"
                @on-change="handleDateChange"
                @on-close="handleDateClose"
                placeholder="Выберите даты"
            />
        </div>

        <button @click="filterByPurchaseDate" class="search-button">Поиск по дате покупки</button>
        <button @click="filterByDepartureDate" class="search-button">Поиск по дате отправления</button>

        <input v-model="searchQuery" @input="onSearchInput" placeholder="Поиск по таблице..." class="search-input" />

        <div v-if="errorMessage" class="error">{{ errorMessage }}</div>

        <div class="table-container">
            <table v-if="paginatedTickets.length">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Билет</th>
                    <th>Билет в БА</th>
                    <th>Заказ</th>
                    <th>Заказ в БА</th>
                    <th>ФИО</th>
                    <th>Почта</th>
                    <th>Телефон</th>
                    <th>Место</th>
                    <th>Маршрут</th>
                    <th>Дата и время отправления</th>
                    <th>Дата и время покупки</th>
                    <th>Номинал</th>
                    <th>Цена</th>
                    <th>Опции</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(ticket, index) in paginatedTickets" :key="ticket.id">
                    <td>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                    <td>{{ ticket.id }}</td>
                    <td>{{ ticket.ba_ticket_id }}</td>
                    <td>{{ ticket.order }}</td>
                    <td>{{ ticket.ba_operation_id }}</td>
                    <td>{{ ticket.passenger_name }}</td>
                    <td>{{ ticket.email }}</td>
                    <td>{{ ticket.passenger_phone }}</td>
                    <td>{{ ticket.place }}</td>
                    <td>{{ ticket.departure_station }} -> {{ ticket.arrival_station }}</td>
                    <td>{{ ticket.departure_date }} в {{ ticket.departure_time }}</td>
                    <td>{{ dayjs(ticket.created_at).format("DD.MM.YYYY в H:mm:ss") }}</td>
                    <td>{{ ticket.nominal }}</td>
                    <td>{{ ticket.price }}</td>
                    <td v-if="ticket.status === 'confirmed' || ticket.status === 'pending'">
                        <button class="btn btn-danger" @click="openRefundModal(ticket)">
                            Возврат
                        </button>
                    </td>
                    <td v-else class="text-danger">
                        {{ ticket.translated_status }}
                    </td>
                </tr>
                </tbody>
            </table>

            <!-- Пагинация -->
            <div class="pagination">
                <button @click="prevPage" :disabled="currentPage === 1" class="btn btn-outline-info">Назад</button>
                <span class="page-info">Страница {{ currentPage }} из {{ totalPages }}</span>
                <button @click="nextPage" :disabled="currentPage >= totalPages" class="btn btn-outline-info">Вперед</button>
            </div>
        </div>
        <div v-if="isModalOpen" class="modal-overlay">
            <div class="modal">
                <h2>Возврат билета</h2>
                <p><strong>Максимальная сумма возврата:</strong> {{ maxRefundAmount }} ₽</p>
                <p><strong>Рекомендуемая сумма возврата:</strong> {{ calculatedRefundAmount }} ₽</p>

                <label>Введите сумму возврата:</label>
                <input type="number" v-model="refundAmount" :max="maxRefundAmount" />

                <label>Комментарий:</label>
                <textarea v-model="refundComment" @input="adjustTextareaHeight" ref="textareaRef" maxlength="200"></textarea>
                <p>{{ 200 - refundComment.length }} символов осталось</p>
                <div class="modal-actions">
                    <button @click="submitRefund" class="btn btn-success">Подтвердить</button>
                    <button @click="isModalOpen = false" class="btn btn-secondary">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import { Russian } from 'flatpickr/dist/l10n/ru.js';
import dayjs from 'dayjs';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import { useLoadingStore } from '@/stores/loading';
import { toast } from 'vue3-toastify';

dayjs.extend(customParseFormat);

const loadingStore = useLoadingStore();
const authStore = useAuthStore();
const router = useRouter();

const tickets = ref([]);
const searchQuery = ref('');
const errorMessage = ref('');
const dateRange = ref('');
const selectedTicket = ref(null);
const refundAmount = ref('');
const refundComment = ref('');
const maxRefundAmount = ref(0);
const calculatedRefundAmount = ref(0);
const isModalOpen = ref(false);
const itemsPerPage = ref(10); // Количество билетов на странице
const currentPage = ref(1); // Текущая страница
const textareaRef = ref(null);
const activeFilter = ref(null);

const adjustTextareaHeight = () => {
    const textarea = textareaRef.value;
    if (textarea) {
        textarea.style.height = "auto"; // Сбрасываем высоту перед измерением
        textarea.style.height = textarea.scrollHeight + "px"; // Устанавливаем новую высоту
    }
};
const flatpickrConfig = {
    dateFormat: 'd.m.Y',
    locale: Russian,
    disableMobile: true,
    mode: 'range',
};

const handleDateChange = (selectedDates) => {
    console.log('Выбраны даты:', selectedDates);
};

const handleDateClose = () => {
    console.log('Календарь закрыт');
};

const fetchTickets = async (filterParams = {}) => {
    loadingStore.setLoading(true);
    errorMessage.value = '';
    console.log('Запрос билетов с параметрами:', filterParams);

    try {
        const response = await axios.get('/api/tickets', {
            params: filterParams,
            headers: {
                Authorization: `Bearer ${authStore.getToken}`
            },
        });
        tickets.value = response.data.tickets;
        currentPage.value = 1;
    } catch (error) {
        errorMessage.value = 'Ошибка загрузки билетов';
        toast.error('Ошибка загрузки билетов');
        console.error('Ошибка загрузки билетов:', error);
    } finally {
        loadingStore.setLoading(false);
    }
};

const openRefundModal = async (ticket) => {
    loadingStore.setLoading(true);
    selectedTicket.value = ticket;
    refundAmount.value = ''; // Сбрасываем данные
    refundComment.value = '';

    try {
        // Получаем максимальную и рассчитанную сумму возврата
        const response = await axios.post(`/api/ticket/refund-info`, {
                uuid: ticket.uuid
            },{
            headers: {
                Authorization: `Bearer ${authStore.getToken}`
            },
        });

        maxRefundAmount.value = response.data.max_refund_amount;
        calculatedRefundAmount.value = response.data.calculated_refund_amount;
    } catch (error) {
        toast.error('Ошибка получения данных о возврате');
        console.error('Ошибка получения данных о возврате:', error);
    } finally {
        loadingStore.setLoading(false);
    }

    isModalOpen.value = true;
};
const submitRefund = async () => {
    if (!selectedTicket.value) return;

    try {
        const response = await axios.post(`/api/ticket/refund`, {
            uuid: selectedTicket.value.uuid,
            refund_amount: refundAmount.value,
            comment: refundComment.value
        }, {
            headers: {
                Authorization: `Bearer ${authStore.getToken}`
            },
        });


        if (response.data.success) {
            toast.success(response.data.message || 'Билет успешно возвращен');
            console.log(activeFilter.value)
            if (activeFilter.value === 'purchase') {
                filterByPurchaseDate();
            } else if (activeFilter.value === 'departure') {
                filterByDepartureDate();
            } else {
                await fetchTickets(); // По умолчанию загружаем все билеты
            }
            isModalOpen.value = false; // Закрываем модалку
        } else {
            toast.error(response.data.message || 'Ошибка при возврате. Попробуйте позже.');
        }
        await fetchTickets(); // Обновляем список билетов
        isModalOpen.value = false; // Закрываем модалку
    } catch (error) {
        toast.error('Ошибка возврата билета');
        console.error('Ошибка возврата билета:', error);
    }
};

const formatDate = (date) => {
    const parsedDate = dayjs(date, 'DD.MM.YYYY').format('YYYY-MM-DD');
    console.log('Форматированная дата:', parsedDate);
    return parsedDate;
};

const filterByDate = (startKey, endKey) => {
    const dates = dateRange.value.split(' — ');
    console.log("dates:" + dates)
    if (dates.length === 1) {
        const startDate = formatDate(dates[0]);
        const endDate = formatDate(dates[0]);

        const filterParams = {
            [startKey]: startDate,
            [endKey]: endDate,
        };
        fetchTickets(filterParams);
    } else {
        const startDate = formatDate(dates[0]);
        const endDate = formatDate(dates[1]);

        const filterParams = {
            [startKey]: startDate,
            [endKey]: endDate,
        };
        fetchTickets(filterParams);
    }


};

const filterByPurchaseDate = () => {
    console.log('Фильтрация по дате покупки');
    activeFilter.value = 'purchase';
    filterByDate('purchase_start_date', 'purchase_end_date');
};

const filterByDepartureDate = () => {
    console.log('Фильтрация по дате отправления');
    activeFilter.value = 'departure';
    filterByDate('departure_start_date', 'departure_end_date');
};

// Логика поиска
const onSearchInput = () => {
    console.log('Текущий поисковый запрос:', searchQuery.value);
};

// Фильтрация билетов
const filteredTickets = computed(() => {
    const query = searchQuery.value.toLowerCase();
    return tickets.value.filter(ticket =>
        (ticket.id && ticket.id.toString().includes(query)) ||
        (ticket.ba_ticket_id && ticket.ba_ticket_id.toString().includes(query)) ||
        (ticket.order && ticket.order.toString().includes(query)) ||
        (ticket.ba_operation_id && ticket.ba_operation_id.toString().includes(query)) ||
        (ticket.passenger_name && ticket.passenger_name.toLowerCase().includes(query)) ||
        (ticket.email && ticket.email.toLowerCase().includes(query)) ||
        (ticket.passenger_phone && ticket.passenger_phone.toString().includes(query)) ||
        (ticket.place && ticket.place.toString().includes(query)) ||
        (ticket.departure_station && ticket.departure_station.toLowerCase().includes(query)) ||
        (ticket.arrival_station && ticket.arrival_station.toLowerCase().includes(query)) ||
        (ticket.departure_date && ticket.departure_date.toString().includes(query)) ||
        (ticket.departure_time && ticket.departure_time.toString().includes(query)) ||
        (ticket.price && ticket.price.toString().includes(query)) ||
        (ticket.created_at && dayjs(ticket.created_at).format("DD.MM.YYYY в H:mm:ss").includes(query))
    );
});

// Пагинация
const totalPages = computed(() => Math.ceil(filteredTickets.value.length / itemsPerPage.value));

const paginatedTickets = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return filteredTickets.value.slice(start, start + itemsPerPage.value);
});

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

onMounted(() => {
    if (!authStore.isAdmin) {
        router.push('/');
    }
});
</script>


<style scoped>
.flatpickr-input {
    max-width: 220px;
    min-width: 220px;
    font-size: 16px;
    height: 44px;
    border-radius: 8px;
    color: #0d0d0f;
    padding-right: 12px;
    padding-left: 12px;
    background-color: rgb(255, 255, 255);
    border: 1px solid rgb(227, 225, 220);
}

.search-input {
    padding: 8px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.tickets-container {
    margin: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.title {
    text-align: center;
}

.filters {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-bottom: 15px;
    align-items: center;
}
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 400px;
    min-height: 200px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
    height: max-content;
}

.modal-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
.table-container {
    margin-top: 20px;
    padding: 20px;
    background: white;
    overflow-x: auto;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f0f0f0;
    font-weight: 600;
    color: #333;
}

td {
    font-size: 14px;
    color: #555;
}

td button {
    background-color: #dc3545;
    padding: 5px 10px;
    border-radius: 4px;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

td button:hover {
    background-color: #c82333;
}

.no-tickets {
    color: #888;
    font-size: 18px;
}
tbody tr:nth-child(odd) {
    background-color: #f9f9f9; /* Светлый фон для нечетных строк */
}

tbody tr:nth-child(even) {
    background-color: #f1f1f1; /* Немного темнее фон для четных строк */
}
.search-button {
    margin: 10px;
    width: 300px;
    outline: none;
    border: none;
    background: #0c73fe;
    height: 35px;
    border-radius: 8px;
    color: white;
}
.search-button:hover {
    background: #3b93ff;
}
.pagination {
    margin-top: 10px;
    align-items: center;
}
.page-info {
    margin-left: 10px;
    margin-right: 10px;
}
</style>
