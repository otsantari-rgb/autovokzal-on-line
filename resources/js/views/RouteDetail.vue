<template>
  <div class="route-detail p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">
      Маршрут: {{ routeData?.start }} → {{ routeData?.end }}
    </h1>

    <div v-if="routeData" class="space-y-4">
      <p><strong>Англ. название:</strong> {{ routeData.start_translated }} → {{ routeData.end_translated }}</p>
      <p><strong>Название маршрута:</strong> {{ routeData.route_name }}</p>
      <p><strong>Цена:</strong> {{ routeData.price }} руб.</p>

      <div>
        <strong>Координаты:</strong>
        <ul>
          <li>Отправление: {{ routeData.coordinates_from_lat }}, {{ routeData.coordinates_from_long }}</li>
          <li>Прибытие: {{ routeData.coordinates_to_lat }}, {{ routeData.coordinates_to_long }}</li>
        </ul>
      </div>

      <!-- Здесь можно отобразить карту -->
      <!-- Например, встроить компонент Яндекс или Leaflet -->
      <!-- <YandexMap :coords-from="..." :coords-to="..." /> -->
    </div>

    <div v-else>
      <p>Загрузка маршрута...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const slug = page.props.slug;
const routeData = ref(null);

onMounted(async () => {
  try {
    const response = await axios.get(`/api/more-routes/${routeName}`);
    routeData.value = response.data;
  } catch (err) {
    console.error('Ошибка загрузки маршрута:', err);
  }
});
</script>

<style scoped>
.route-detail {
  font-family: "Inter", sans-serif;
}
</style>
