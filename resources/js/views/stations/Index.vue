<template>
  <div class="stations-l">
    <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-3xl mb-8">
      <h1 class="text-2xl font-bold mb-6 text-center">Автовокзалы и автокассы Бурятии</h1>
      <!-- <ul class="space-y-3">
        <li
          v-for="(station, key) in stations"
          :key="key"
          class="text-center"
        >
          <Link
            :href="`/stations/${key}`"
            class="text-blue-600 hover:underline"
          >
            {{ station.name }}
          </Link>
        </li>
      </ul> -->
    </div>

    <Carousel :stations="stations" />
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Carousel from '@/views/home/Carousel.vue';

defineProps({
  stations: Array
});
</script>

<style>
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

.avtovokzal-container{
  position: relative;
  border: none;
  border-radius: 20px;
  max-width: 800px;
  min-width: 200px;
  padding: 30px;
  width: 100%;
  height: 420px;
  overflow: hidden;
  background: #fff;
  text-align: left;
}
</style>