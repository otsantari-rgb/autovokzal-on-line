<template>
  <div class="more-routes">
    <div class="more-routes-container py-4">
      <div class="row">
        <div
          v-for="(route, index) in moreRoutes"
          :key="index"
          v-if="moreRoutes.length"
          class="col d-flex justify-content-center"
        >
          <Link
            class="card"
            :href="`/${route.slug}`"
          >
            <div class="card-inner">
              <div class="card-front">{{ route.start }} - {{ route.end }}</div>
              <div class="card-back">{{ route.start }} - {{ route.end }}</div>
            </div>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';

const moreRoutes = ref([]);

onMounted(async () => {
  try {
    const res = await axios.get('/api/more-routes');
    moreRoutes.value = res.data;
  } catch (err) {
    console.error(err);
  }
});
</script>

<style scoped>
.more-routes {
    display: flex;
    justify-content: center;
}
.more-routes-container {
    font-family: "Inter", sans-serif;
    margin-top: 50px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    align-items: center; /* Вертикальное выравнивание */
    justify-content: center; /* Горизонтальное выравнивание */
    text-align: center; /* Центрирование текста внутри контейнера */
    max-width: 1200px;
    overflow: hidden;
}
.card {
    width: 230px;
    height: 80px;
    margin: 1rem auto;
    perspective: 1000px;
    cursor: pointer;
    background: none;
    border: none;
}

.card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.6s;
    transform-style: preserve-3d;
}

.card:hover .card-inner {
    transform: rotateY(180deg);
}

.card-front,
.card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
}

.card-front {
    background: white;
    border: 1px solid #e5e7eb;
}

.card-back {
    background: #007bff;
    color: white;
    transform: rotateY(180deg);
}
</style>
