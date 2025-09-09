<template>
  <div class="station-page">
    <Head>
      <title>{{ title || "Автовокзал в Улан-Удэ «Байкал»" }}</title>
      <meta
        name="description"
        :content="description || 'Автовокзал в Улан-Удэ «Байкал» — расписание, популярные рейсы, онлайн-билеты'"
      />
    </Head>

    <h1>{{ title || "Автовокзал в Улан-Удэ «Байкал»" }}</h1>

    <img
      src="/img/stations/2018-01-09_11-21-47_1-540x340.jpg"
      alt="Автовокзал Улан-Удэ Байкал"
    />

    <div class="station-info">
      <p><strong>Адрес:</strong> {{ station.address }}</p>
      <p><strong>Телефон диспетчера:</strong> {{ station.dispatcherPhone }}</p>
      <p><strong>Время работы:</strong> {{ station.openingHours }}</p>
      <p v-if="station.lunch"><strong>Обед:</strong> {{ station.lunch }}</p>
    </div>

    <section class="features">
      <h2>Удобства для пассажиров</h2>
      <ul>
        <li>Терминал <strong>Сбербанка</strong> для снятия наличных прямо перед дорогой</li>
        <li>Изобилие <strong>точек питания</strong> с блюдами кухни народов Сибири</li>
        <li><strong>Сувенирные лавки</strong> — возьмите с собой частицу Бурятии</li>
        <li><strong>Магазины</strong> с продуктами и предметами первой необходимости</li>
        <li><strong>Санитарные зоны</strong> с повышенным комфортом</li>
        <li>Информационные стойки для <strong>поддержки пассажиров</strong></li>
        <li><strong>Комфортный зал ожидания</strong> с мягкими креслами</li>
      </ul>
    </section>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { Head } from '@inertiajs/vue3'

defineProps({
  title: String,
  description: String,
})

const station = reactive({
  address: 'г. Улан-Удэ, ул. Советская 1б',
  dispatcherPhone: '+7 (3012) 717-717',
  openingHours: '07:30–19:00',
  lunch: '12:00–13:00',
})

const getTicketLink = (destination) => `/tickets/ulan-ude-baikal-${destination}`
</script>

<style scoped>
.station-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-top: 50px;
  padding: 0 15px;
}

.station-page h1 {
  margin-bottom: 20px;
}

.station-page img {
  width: 100%;
  max-width: 420px;
  margin-bottom: 20px;
  border-radius: 8px;
  object-fit: cover;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.station-info p {
  margin: 5px 0;
  font-size: 17px;
}

section {
  margin-top: 30px;
  max-width: 700px;
  text-align: left;
}

section h2 {
  margin-bottom: 10px;
  font-size: 20px;
}

section ul {
  list-style: none;
  padding: 0;
}

section li {
  margin: 8px 0;
  font-size: 16px;
}

section a {
  margin-left: 10px;
  color: #007bff;
  text-decoration: none;
}

section a:hover {
  text-decoration: underline;
}

.features h2{
  display: flex;
  justify-content: center;
}

</style>
