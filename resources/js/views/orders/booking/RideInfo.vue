<template>
    <div class="ride" id="result-item">

        <div class="row center-info">
            <p class="carrier text-dark"> {{ sheet.name }}</p>
        </div>

        <div class="row header">
            <div class="column text-left">
                <p class="from-time">{{ sheet.departure_time }}</p>
                <p class="from-date">{{ formatDate(sheet.departure_date, 'D MMM') }}</p>
            </div>

            <div class="column separator"><hr /></div>
            <div class="column duration separator">{{ formattedDuration }}</div>
            <div class="column separator"><hr /></div>
            <div class="column text-right">
                <p class="to-time">{{ sheet.arrival_time }}</p>
                <p class="to-date">{{ formatDate(sheet.arrival_date, 'D MMM') }}</p>
            </div>
        </div>
        <div class="row station">
            <div class="column station-half-left">
                <p class="city">{{ sheet.departure_station }}</p>
                <p class="address">{{ sheet.departure_address.replace("Билеты приобрести по адресу", "") }}</p>
            </div>

            <div class="column station-half-right">
                <p class="city">{{ sheet.arrival_station }}</p>
                <p class="address">{{ sheet.arrival_address.replace("Билеты приобрести по адресу", "") }}</p>
            </div>
        </div>
        <div class="row center-info">
            <p class="carrier" > {{ sheet.carrier }}</p>
        </div>

        
    </div>
</template>

<script setup>
import {computed} from 'vue';
import dayjs from 'dayjs';

import customParseFormat from 'dayjs/plugin/customParseFormat';

dayjs.extend(customParseFormat);

// Получаем данные пропса 'sheet'
const props = defineProps({
    sheet: {
        type: Object,
        required: true
    },
    refundLimit: {
        type: [Number, null],
        required: true
    }
});

// Функция для форматирования даты
const formatDate = (date, format) => {
    return dayjs(date, 'DD.MM.YYYY').format(format)
};

// Функция для вычисления продолжительности
const calculateDuration = (departureDate, departureTime, arrivalDate, arrivalTime) => {
    const departure = dayjs(`${departureDate} ${departureTime}`, 'DD.MM.YYYY HH:mm');
    const arrival = dayjs(`${arrivalDate} ${arrivalTime}`, 'DD.MM.YYYY HH:mm');
    const diff = dayjs.duration(arrival.diff(departure));
    return `${diff.hours()} ч ${diff.minutes()} м`;
};

// Вычисляем продолжительность
const formattedDuration = computed(() =>
    calculateDuration(
        props.sheet.departure_date,
        props.sheet.departure_time,
        props.sheet.arrival_date,
        props.sheet.arrival_time
    )
);
</script>

<style scoped>
.ride {
    border: 1px solid #ddd; /* рамка */
    border-radius: 16px;
    padding: 28px 42px 32px;
    background-color: #f9f9f9; /* цвет фона */
    box-shadow: 0 4px 12px -4px hsla(60,4%,60%,.35),0 0 2px hsla(60,4%,60%,.3);
    width: 100%;
    align-items: center; /* Вертикальное выравнивание */
    justify-content: center; /* Горизонтальное выравнивание */
    text-align: center; /* Центрирование текста внутри контейнера */
}

.row{
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.duration {
    min-width: 53px;
}

.station {
    display: flex;
    justify-content: space-between;
    align-items: stretch; /* Растягивает колонки на всю высоту контейнера */
    border: 2px solid gray;
    border-radius: 10px;
}

.station-half-left, .station-half-right  {
    display: flex;
    flex-direction: column; /* Выстраиваем текст в столбец */
    width: 50%;
    padding: 8px; /* Отступы для красоты */
}

.station-half-right {
    border-left: 2px solid gray; /* Четкая разделяющая линия */
}

.city {
    font-size: 20px; /* Увеличенный размер для города */
    font-weight: 600;
    margin-bottom: 2px; /* Минимальный отступ между строками */
}

.address {
    font-size: 12px; /* Размер адреса намного меньше */
    font-weight: 400;
    color: #666; /* Дополнительное визуальное различие */
}

.from-time, .to-time {
    margin-bottom: 0 !important;
    font-weight: 600;
    font-size: 24px;
}
.from-date, .to-date {
    color: #9c9ba2;
    margin-bottom: 0 !important;
    font-size: 14px;
    line-height: 14px;
}
.text-left, .text-right{
    line-height: 24px;
    margin-bottom: 4px;
}
.city, .address {
    margin-bottom: 0 !important;
    font-weight: 600;
}
.header {
    font-weight: bold;
    font-size: 18px;
}

.separator {
    margin-top: -20px;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    flex: 1;
}
.carrier {
    margin-top: 10px;
    font-size: 20px;
    color: #9c9ba2;
    text-align: center;
    justify-content: center;
}
.center-info {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    display: flex;
    justify-content: center;
}
.text-right {
    margin-left: 5%;
}
.text-left {
    margin-right: 5%;
}
</style>
