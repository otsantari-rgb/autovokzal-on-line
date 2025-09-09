<template>
  <div class="min-h-screen bg-gray-100 flex justify-center items-start py-12">
    <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-xl">
      <h1 class="text-2xl font-bold mb-4 text-center">{{ station.name }}</h1>
      <img :src="station.modalImageLink" alt="Станция" class="mb-4 mx-auto rounded-lg max-h-64 object-cover block" loading="lazy"/>
      
      <div class="station-info">
        <p><strong>Адрес:</strong> {{ station.address }}</p>
        <p><strong>Телефон диспетчера:</strong> {{ station.dispatcherPhone }}</p>
        <p><strong>Время работы:</strong> {{ station.openingHours }}</p>
        <p v-if="station.lunch"><strong>Обед:</strong> {{ station.lunch }}</p>
      </div>
      <Link href="/" class="main-page-link">
        Назад на главную
      </Link>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ station: Object, stationKey: String });
</script>

<style scoped>
.bg-white {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.text-center {
  text-align: center;
}

img {
  width: 100%;
  max-width: 420px;
}

 h1{
  padding-bottom: 35px;
  padding-top: 45px;
}

.station-info{
  text-align: center;
  font-size: 17px;
}

.main-page-link{
  color: #007bff;
  margin-top: 6px; 
  text-align: center;
  font-size: 17px;
  margin-bottom: 18px;
}

</style>
