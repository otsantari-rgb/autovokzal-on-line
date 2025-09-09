<template>
   <div>
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <link rel="icon" type="image/x-icon" href="/img/logo_ru.png">
        </Head>
        <navbar></navbar>
        <LoadingSpinner v-if="loading" />
        <slot />
        <footer-component></footer-component>
    </div>
</template>

<script setup>
// Импорты
import { computed, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import Navbar from './views/Navbar.vue';
import FooterComponent from './views/Footer.vue';
import LoadingSpinner from './views/LoadingSpinner.vue';
import { useLoadingStore } from '@/stores/loading';
import { useAuthStore } from '@/stores/auth';

// Stores
const loadingStore = useLoadingStore();
const authStore = useAuthStore();

// Получаем данные страницы
const page = usePage();

// Вычисляемые значения
const loading = computed(() => loadingStore.isLoading);
const title = computed(() => page.props?.title ?? 'Default Title');
const description = computed(() => page.props?.description ?? 'Default Description');


// Обновление auth store при изменении данных авторизации
watch(
  () => page.props.auth,
  (newAuth) => {
    authStore.setUser(newAuth?.user || null);
  },
  { deep: true }
);
</script>

<style scoped>
/* Контейнер всей страницы */
.layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh; /* Обеспечивает футер внизу при малом контенте */
}

/* Основной контент растягивается на всю доступную высоту */
.main-content {
  flex: 1;
  background: #eff1f4;
  padding-bottom: 2rem; /* Запас под футер, если он фиксированный */
  padding-top: 1rem;
}

/* Базовые стили (при необходимости) */
#app {
  font-family: Arial, sans-serif;
}
</style>
