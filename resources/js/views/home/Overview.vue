<template>
  <!-- Блок "Как купить билет на автобус" -->
  <section class="instructions-section py-5">
    <div class="container">
      <header class="block__header text-center mb-4">
        <h2 class="block__title instructions__title">
          Как купить билет на автобус
        </h2>
      </header>
      <div class="instructions-steps">
        <div class="step" v-for="(step, index) in steps" :key="index">
          <img :src="step.img" :alt="`Шаг ${index + 1}`" />
          <div class="step-content">
            <h3 class="order-instruction__title">{{ index + 1 }}. {{ step.title }}</h3>
            <p class="order-instruction__entity">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const steps = [
  { img: '/img/steps/step1.png', title: 'Посмотрите расписание автобусов', desc: 'Выберите пункт отправления автобуса, назначения, а также дату поездки.' },
  { img: '/img/steps/step2.png', title: 'Выберите рейс', desc: 'Сравните цены на автобусные билеты, место и время отправления.' },
  { img: '/img/steps/step3.png', title: 'Купите билет на автобус', desc: 'Введите данные пассажира, оплатите билет и распечатайте его.' },
  { img: '/img/steps/step4.png', title: 'Все готово!', desc: 'Пожалуйста, после поездки оставьте отзыв — это улучшит качество обслуживания.' }
];
</script>

<style scoped>
.instructions-section {
  background-color: #fff;
  border-radius: 20px;
  max-width: 800px;
  width: 100%;
  height: 420px;
  padding: 30px;
  overflow: hidden;
}

.instructions-steps {
  display: flex;
  justify-content: space-between;
  align-items: stretch;
  height: 100%;
  gap: 15px;
}

.step {
  flex: 1;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.step img {
  width: 100px;
  height: 100px;
  object-fit: contain;
  margin-bottom: 10px;
  flex-shrink: 0;
}

.step-content {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  flex: 1;
}

.order-instruction__title {
  font-size: 1.1rem;
  font-weight: bold;
  margin: 10px 0 5px;
}

.order-instruction__entity {
  font-size: 0.95rem;
  color: #555;
  flex-grow: 1;
}
</style>
