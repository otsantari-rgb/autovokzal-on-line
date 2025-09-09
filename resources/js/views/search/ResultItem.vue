<template>
    <div class="result-item-large" :id="'result-item-' + sheet.id">

        <div class="row header">
            <div class="column text-left">
                <p class="from-time">{{ sheet.departure_time }}</p>
                <p class="from-date">{{ formatDate(sheet.departure_date) }}</p>
            </div>
            <div class="column separator"><hr /></div>
            <div class="column time-diff">{{ calculateDuration }}</div>
            <div class="column separator"><hr /></div>
            <div class="column text-right">
                <p class="to-time">{{ sheet.arrival_time }}</p>
                <p class="to-date">{{ formatDate(sheet.arrival_date) }}</p>
            </div>
        </div>

        <div class="row center-info">
            <p class="carrier"> {{ sheet.carrier }}</p>
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

        <form @submit.prevent="$emit('select', sheet)" class="form-class">
            <div class="row footer">
                <div class="column text-left">
                    <p class="places">Осталось {{ sheet.freePlaces }} {{ getPlaceWord(sheet.freePlaces) }}</p>
                </div>
                <div class="column text-right">
                    <p class="price">{{ sheet.price }} ₽</p>
                </div>
            </div>
            <div class="row mr-3">
                <button type="submit" class="button-choose">Выбрать</button>
            </div>
        </form>
        <div class="more-info">
            <button type="button" class="more-info-btn" @click="toggleMoreInfo">
                {{ showMoreInfo ? 'Скрыть' : 'Подробнее' }}
                <svg v-show="!showMoreInfo" id="caret-down" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                    <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                </svg>
                <svg v-show="showMoreInfo" id="caret-up" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-up-fill" viewBox="0 0 16 16">
                    <path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
                </svg>
            </button>
            <div id="more-info" v-show="showMoreInfo">
                <div class="text-info" style="margin-top: 10px;">
                    Маршрут: {{ sheet.name }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, ref} from 'vue';
import dayjs from 'dayjs';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import timeCalculation from '@/utils/timeCalculation';

dayjs.extend(customParseFormat);

// Определяем props для компонента
const props = defineProps({
    sheet: {
        type: Object,
        required: true,
    },
    from: {
        type: String,
        required: true,
    },
    to: {
        type: String,
        required: true,
    },
});

const showMoreInfo = ref(false)

const formatDate = (date) => {
    return dayjs(date, 'DD.MM.YYYY').format('D MMM');
};

const calculateDuration = computed(() => timeCalculation(
    props.sheet.departure_date,
    props.sheet.departure_time,
    props.sheet.arrival_date,
    props.sheet.arrival_time
));

const getPlaceWord = (count) => {
    if (count % 10 === 1 && count % 100 !== 11) {
        return 'место';
    } else if (count % 10 >= 2 && count % 10 <= 4 && (count % 100 < 10 || count % 100 >= 20)) {
        return 'места';
    } else {
        return 'мест';
    }
};

const toggleMoreInfo = () => {
    showMoreInfo.value = !showMoreInfo.value
}
</script>


<style scoped>
.result-item-large {
    border: 1px solid #ddd; /* рамка */
    border-radius: 16px;
    padding: 28px 32px 32px;
    background-color: #f9f9f9; /* цвет фона */
    width: 100%;
}

.from-time, .to-time {
    margin-bottom: 0 !important;
    margin-left: 16px;
    margin-right: 8px;
    font-weight: 600;
    font-size: 24px;
}

.from-date, .to-date {
    color: #9c9ba2;
    margin-bottom: 0 !important;
    font-size: 14px;
    line-height: 14px;
}

.time-diff {
    font-size: 16px;
    font-weight: 600;
}

.carrier {
    font-size: 16px;
    color: #9c9ba2;
}


.price {
    color: #0d0d0f;
    font-size: 24px;
    letter-spacing: -.24px;
    line-height: 24px;
    font-weight: 600;
}

.button-choose {
    display: flex;
    width: 207px;
    height: 36px;
    padding: 8px;
    border: none;
    border-radius: 8px;
    background: #fa742d;
    align-items: center;
    align-self: end;
    justify-content: center;
    font-size: 19px;
    color: #FFFFFF;
    line-height: 25px;
    font-weight: 600;
}

.button-choose:hover {
    background: #e45c24;
    cursor: pointer;
}

@media (min-width: 992px) {
    .result-item-large {
        display: block;
    }
}

.more-info-btn {
    display: flex;
    flex-direction: row;
    background: none;
    color: #9c9ba2;
    border: none;
    padding: 0;
    font-size: 18px;
    transition: background-color 0.3s;
    margin-top: 4px;
    border-radius: 8px;
    align-self: center;
    align-items: center;
}
.more-info-btn:hover {
    background: none;
    color: #5e5d65;
    border: none;
}
.more-info-btn:focus, .more-info-btn:active {
    outline: none;
}
.more-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.result-item-large {
    border: 1px solid #ddd; /* рамка */
    border-radius: 16px;
    padding: 28px 32px 32px;
    background-color: #f9f9f9; /* цвет фона */
    width: 100%;
}

.row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.station {
    display: flex;
    justify-content: space-between;
    align-items: stretch; /* Растягивает колонки на всю высоту контейнера */
    border: 2px solid gray;
    border-radius: 10px;
}

.station-half-left, .station-half-right {
    display: flex;
    flex-direction: column;
    width: 50%;
    padding: 8px; /* Отступы для красоты */
}

.station-half-right {
    border-left: 2px solid gray; /* Четкая разделяющая линия */
}

.city {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 2px;
}

.address {
    font-size: 14px;
    font-weight: 400;
    color: #666;
}

.header {
    font-weight: bold;
    font-size: 18px;
}

.from-time, .to-time {
    margin-bottom: 0 !important;
    font-weight: 600;
    font-size: 24px;
}

.from-date, .to-date {
    margin-bottom: 0 !important;
}
.text-left, .text-right{
    display: flex;
    line-height: 24px;
    margin-bottom: 4px;
    flex-direction: column;
}

.city, .address {
    margin-bottom: 0 !important;
}

.time-diff{
    padding: 10px;
}

.separator {
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    flex: 1;
}
.carrier{
    font-size: 20px;
    color: #5a6268;
}

.center-info {
    text-align: center;
    font-size: 10px;
    font-weight: bold;
    display: flex;
    justify-content: center;
}

.places, .price {
    font-size: 20px;
    font-weight: bold;
}

.places {
    color: #5a6268;
}
.footer {
    padding-top: 10px;
}

.text-left {
    text-align: left;
}

.text-right {
    text-align: right;
}


.button-choose {
    display: flex;
    width: 100%;
    height: 36px;
    padding: 8px;
    border: none;
    border-radius: 8px;
    background: #fa742d;
    align-items: center;
    align-self: end;
    justify-content: center;
    font-size: 19px;
    color: #FFFFFF;
    line-height: 25px;
    font-weight: 600;
}

.button-choose:hover {
    background: #e45c24;
    cursor: pointer;
}
.form-class {
    padding-left: 30px;
    display: flex;
    flex-direction: column;
    align-self: stretch;
}
</style>
