<template>
    <div class="avtovokzal-container">
        <h3 class="collection-title">Автокассы</h3>
        <Carousel ref="carouselRef" v-bind="config">
            <Slide v-for="(station, index) in stations" :key="index">
                <div class="station-slide" @click="openModal(station)">
                    <img :src="station.backgroundLink" :alt="station.name" />
                    <div class="item-info">
                        <span class="station-name">{{ station.name }}</span>
                        <br>
                        <span class="address">{{ station.address }}</span>
                    </div>
                </div>
            </Slide>
        </Carousel>
        <div class="navigation">
            <button @click="prev" class="nav-prev">
                <svg
                    viewBox="0 0 16 16"
                    fill=".phpcurrentColor"
                    xmlns="http://www.w3.org/2000/svg"
                    class="button-icon" data-test-id="icon"
                    aria-hidden="true"
                >
                <path d="M4 7.5 9.5 2l1.25 1.25L6 8l4.75 4.75L9.5 14 4 8.5v-1Z"></path>
                </svg>
            </button>
            <button @click="next" class="nav-next">
                <svg
                    viewBox="0 0 16 16"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                    class="button-icon" data-test-id="icon"
                    aria-hidden="true"
                >
                    <path d="M4 7.5 9.5 2l1.25 1.25L6 8l4.75 4.75L9.5 14 4 8.5v-1Z"></path>
                </svg>
            </button>
        </div>
    </div>
    <!-- Модальное окно автовокзалов-->
    <AvtovokzalModal :station="selectedStation" :isVisible="showModal" @close="closeModal" />
</template>

<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue';
import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide} from 'vue3-carousel'
import AvtovokzalModal from '../AvtovokzalModal.vue';
import axios from 'axios';
const carouselRef = ref();
const stations = ref([]);
const infoPhone = ref(null);
const showModal = ref(false);
const selectedStation = ref(null);

const next = () => carouselRef.value.next();
const prev = () => carouselRef.value.prev();

const getSlidesToShow = () => {
    // Проверяем, что window доступен (т.е. не SSR)
    if (typeof window === 'undefined') {
        return 1; // Дефолтное значение для SSR
    }
    if (window.innerWidth >= 1200) return 4; // Десктоп
    if (window.innerWidth >= 768) return 2;  // Планшет
    return 1; // Мобильный
};

// Реактивное свойство, зависящее от ширины экрана
const itemsToShow = ref(getSlidesToShow());

// Обработчик изменения размера окна
const updateSlides = () => {
    itemsToShow.value = getSlidesToShow();
};

onUnmounted(() => {
    window.removeEventListener('resize', updateSlides);
});

const openModal = (station) => {
    selectedStation.value = station;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedStation.value = null;
};

onMounted(async () => {
    if (typeof window !== 'undefined') {
        window.addEventListener('resize', updateSlides);
    }

   try {
    const response = await axios.get('/api/stations');

    // response.data - объект с ключами станций
    stations.value = Object.values(response.data);
    // Если есть телефон, например, в другом ключе — нужно получить его отдельно

    // Например, если infoPhone в отдельном запросе или другом месте, то:
    infoPhone.value = null; // или получай как надо
} catch (error) {
    console.error('Ошибка загрузки данных:', error);
}
});

onUnmounted(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('resize', updateSlides);
    }
});

const config = computed(() => ({
    itemsToShow: itemsToShow.value,
    itemsToScroll: 1,
    autoplay: 2000,
    pauseAutoplayOnHover: true,
    wrapAround: true,
    modelValue: 3
}));
</script>
<style>
.avtovokzal-container {
    position: relative;
    border: none;
    border-radius: 20px;
    max-width: 800px;
    min-width: 200px;
    padding: 30px;
    width: 100%;
    height: 420px;
    overflow: hidden; /* Избегаем переполнение */
    background: #FFFFFF;
    text-align: left;
}
.collection-title {
    font-family: "Robotto", sans-serif !important;
    font-size: 34px;
    font-weight: 700;
    display: flex;
    color: #0c131d;
    padding: 10px 16px 12px;
    line-height: 30px;
}
.carousel__slide img{
    border-radius: 16px !important;
}
.navigation {
    position: absolute;
    top: 7%;
    right: 5%;
}
.button-icon {
    width: 16px;
    height: 16px;
    display: inline-block;
}
.nav-next {
    transform: rotate(-180deg);
}
.nav-prev, .nav-next {
    border: none #f0f2f3; /* Убираем рамку */
    border-radius: 50% !important; /* Закругляем углы */
    height: 36px;
    width: 36px;
    margin: 5px;
}
.nav-prev:focus,
.nav-prev:active,
.nav-next:focus,
.nav-next:active {
    outline: none; /* Убираем обводку при фокусе */
}
.station-name {
    font-weight: bold;
}
.address {
    color: #6a6476;
}
.item-info {
    max-height: 120px;
    overflow: hidden;           /* Скрыть переполнение */
    text-overflow: ellipsis;
}
.station-slide {
    display: flex;
    flex-direction: column;
    align-self: baseline;
    align-items: center;
}
@media (max-width: 767px) {
    .navigation {
        top: auto;
        bottom: 7%;
        right: 5%;
    }
}
</style>
