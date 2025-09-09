<template>
  <div class="results">
    <section class="results-section">
      <!-- Заголовок маршрута -->
      <h1 class="results-title">
        Автобусные билеты {{ from }} - {{ to }}
      </h1>

      <!-- Билеты -->
      <div v-if="hasActualSheets" class="results-list">
        <!-- Сообщение, если дата отличается от исходной -->
        <p v-if="actualSheetGroup.date !== originalDate" class="info-text">
          К сожалению, на {{ originalDate }} билеты не найдены.<br />
          Показываем рейсы на ближайшую дату — {{ actualDateFormatted }}
        </p>

        <div class="results-items">
          <ResultItem
            v-for="sheet in actualSheetGroup.sheets"
            :key="sheet.id"
            :sheet="sheet"
            :from="from"
            :to="to"
            @select="navigateToBooking"
          />
        </div>
      </div>

      <!-- Если вообще нет билетов -->
      <p v-else class="no-results">
        К сожалению, билетов на выбранную дату не найдено.
      </p>

      <p v-if="sheets.length === 0 && data.nearest.date" class="no-results">
  
  <strong class="nearest-label">Ближайшие доступные билеты на {{ data.nearest.date }}:</strong>
</p>

<div v-if="sheets.length === 0 && data.nearest.sheets.length > 0" class="results-items">
  <ResultItem
    v-for="sheet in data.nearest.sheets"
    :key="sheet.id"
    :sheet="sheet"
    :from="from"
    :to="to"
    @select="navigateToBooking"
  />
</div>

      <!-- Описание маршрута -->
      <div
        class="info"
        v-if="data.mapData && data.mapData.fromCoords && data.mapData.toCoords"
      >
        <h2 class="results-title">Описание маршрута {{ from }} - {{ to }}</h2>
        <p class="description-text">
          Расстояние по маршруту {{ from }} - {{ to }} по трассе составляет {{ road }} км,
          расстояние по прямой — {{ straight }} км.<br />
          Расчетное время преодоления расстояния:
          {{ hasActualSheets ? timeCalculation(
            actualSheetGroup.sheets[0].departure_date,
            actualSheetGroup.sheets[0].departure_time,
            actualSheetGroup.sheets[0].arrival_date,
            actualSheetGroup.sheets[0].arrival_time
          ) : 'неизвестно' }}.<br />
          Отправление с автовокзала: г. Улан-Удэ, ул. Советская, 1б.<br />
          Прибытие: {{ hasActualSheets ? actualSheetGroup.sheets[0].arrival_station : to }}.<br />
        </p>
      </div>

      <!-- Карта маршрута -->
      <div
        class="map-wrapper"
        v-if="data.mapData && data.mapData.fromCoords && data.mapData.toCoords"
      >
        <h2 class="results-title">Карта маршрута {{ from }} - {{ to }}</h2>
        <YandexRouteMap :mapData="data.mapData" @route-info="updateRouteInfo" />
      </div>

      <button @click="goToHomePage" class="btn btn-primary">Вернуться на главную</button>
    </section>
  </div>
</template>

<script setup>
import { computed, ref, nextTick, onMounted } from 'vue';
import dayjs from 'dayjs';
import 'dayjs/locale/ru';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import ResultItem from './ResultItem.vue';
import { useLoadingStore } from '@/stores/loading';
import { useBookingStore } from '@/stores/booking';
import { useOrderDetailsStore } from '@/stores/orderDetails';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { router } from '@inertiajs/vue3';
import YandexRouteMap from './YandexRouteMap.vue';
import timeCalculation from '@/utils/timeCalculation';

dayjs.extend(customParseFormat);
dayjs.locale('ru');

const props = defineProps({
  title: String,
  description: String,
  success: Boolean,
  data: Object,
});

const road = ref(null);
const straight = ref(null);

const sheets = computed(() => props.data?.sheets || []);
const allSheetsByDate = computed(() => props.data?.allSheetsByDate || []);
const from = computed(() => props.data?.from || '');
const to = computed(() => props.data?.to || '');
const originalDate = computed(() => props.data?.date || '');

// Находим ближайшие билеты, если на выбранную дату их нет
const actualSheetGroup = computed(() => {
  if (sheets.value.length > 0) {
    return { date: originalDate.value, sheets: sheets.value };
  }
  return allSheetsByDate.value.find(group => group.sheets?.length > 0) || { date: '', sheets: [] };
});

const hasActualSheets = computed(() => actualSheetGroup.value.sheets.length > 0);
const actualDateFormatted = computed(() => {
  return actualSheetGroup.value.date
    ? dayjs(actualSheetGroup.value.date, 'DD.MM.YYYY').format('D MMMM YYYY')
    : '';
});

const loadingStore = useLoadingStore();
const bookingStore = useBookingStore();
const orderDetailsStore = useOrderDetailsStore();

const goToHomePage = () => {
  router.get('/');
};

const navigateToBooking = (sheet) => {
  loadingStore.setLoading(true);
  bookingStore.setNeedDocs(false);

  router.get('/booking', { sheet }, {
    onSuccess: async (page) => {
      bookingStore.setBookingData(page.props.bookingData);
      const needDocs = page.props.bookingData.data.need_docs === 'true';
      bookingStore.setNeedDocs(needDocs);
      orderDetailsStore.clear();
      toast.success('Данные для бронирования получены!');
      await nextTick();
    },
    onError: (errors) => {
      toast.error(errors.message || 'Ошибка при бронировании.');
    },
    onFinish: () => {
      loadingStore.setLoading(false);
    },
  });
};

const updateRouteInfo = ({ distanceMeters, straightLineMeters }) => {
  road.value = (distanceMeters / 1000).toFixed(1);
  straight.value = (straightLineMeters / 1000).toFixed(1);
};

onMounted(() => {
  loadingStore.setLoading(false);
});
</script>


<style scoped>
/* (твои стили без изменений) */
h1 {
    font-size: 34px;
    margin-bottom: 10px;
}

.results-title {
  margin-top: 2rem;
  font-size: 24px;
  font-weight: 600;
  padding: 1rem;
  text-align: center;
  text-shadow: 1px 1px 2px rgba(0,0,0,.2);
}

.results-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
  max-width: 1136px;
  width: 100%;
  margin-bottom: 30px; 
}

.results-items {
  display: flex;
  flex-direction: column;
  gap: 15px;
  width: 100%;
}

.info{
  width: 100%;
}

.map-wrapper {
  margin-bottom: 30px; 
  width: 100%;
}

.table th, .table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.description-text {
  font-size: large;
  background-color: white;
  border-radius: 10px;
  padding: 15px 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 30px; 
}

.table th {
    background-color: #007bff;
    color: white;
}

.table tr:hover {
    background-color: #f1f1f1;
}


@media (min-width: 992px) {
    .result-item-large {
        display: block;
    }

    .result-item-medium {
        display: none;
    }

    .results {
        text-align: start;
    }
}

@media (max-width: 991px) {
    .results {
        text-align: center;
    }
}

.no-results {
    font-size: 18px;
    color: #d9534f; /* Красный цвет для предупреждения */
    margin-top: 15px;
}
.btn {
    margin-top: 20px;
}

.results {
    display: flex;
    flex-direction: column;
    align-items: center; /* Вертикальное выравнивание */
    justify-content: center; /* Горизонтальное выравнивание */
    text-align: start; /* Центрирование текста внутри контейнера */
    padding-bottom: 40px;
    background: #eff1f4;
}

.results-section {
    max-width: 1200px;
    display: flex;
    width: 100%;
    flex-direction: column;
    align-items: center;
}

.results-list {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 20px;
    max-width: 1136px;
    width: 100%;
}

.nearest-label {
  color: black;
  font-size: 24px;
  font-weight: 600;
  text-shadow: 1px 1px 2px rgba(0,0,0,.2);
}
</style> 