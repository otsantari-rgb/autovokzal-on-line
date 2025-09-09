<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-md d-flex flex-wrap">
            <!-- Логотип и другие элементы навигации -->
            <Link href="/" class="custom-logo-link" aria-current="page">
                <img width="52" height="52" src="/img/logo_ru.png" class="custom-logo" alt="АВТОВОКЗАЛ" decoding="async" />
            </Link>
            <Link href="/" class="navbar-brand">АВТОВОКЗАЛ УЛАН-УДЭ</Link>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <!-- Ссылка на админ-панель -->
                    <li v-if="isAdmin" class="nav-item">
                        <Link href="/admin" class="nav-link">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="currentColor"
                                class="bi bi-keyboard-fill"
                                viewBox="0 0 16 16"
                            >
                                <path d="M0 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm13 .25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25h-.5a.25.25 0 0 0-.25.25M2.25 8a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5A.25.25 0 0 0 3 8.75v-.5A.25.25 0 0 0 2.75 8zM4 8.25v.5c0 .138.112.25.25.25h.5A.25.25 0 0 0 5 8.75v-.5A.25.25 0 0 0 4.75 8h-.5a.25.25 0 0 0-.25.25M6.25 8a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5A.25.25 0 0 0 7 8.75v-.5A.25.25 0 0 0 6.75 8zM8 8.25v.5c0 .138.112.25.25.25h.5A.25.25 0 0 0 9 8.75v-.5A.25.25 0 0 0 8.75 8h-.5a.25.25 0 0 0-.25.25M13.25 8a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25zm0 2a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25zm-3-2a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h1.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25zm.75 2.25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25h-.5a.25.25 0 0 0-.25.25M11.25 6a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25zM9 6.25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5A.25.25 0 0 0 9.75 6h-.5a.25.25 0 0 0-.25.25M7.25 6a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5A.25.25 0 0 0 8 6.75v-.5A.25.25 0 0 0 7.75 6zM5 6.25v.5c0 .138.112.25.25.25h.5A.25.25 0 0 0 6 6.75v-.5A.25.25 0 0 0 5.75 6h-.5a.25.25 0 0 0-.25.25M2.25 6a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h1.5A.25.25 0 0 0 4 6.75v-.5A.25.25 0 0 0 3.75 6zM2 10.25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25h-.5a.25.25 0 0 0-.25.25M4.25 10a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h5.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25z"/>
                            </svg>
                            Панель управления
                        </Link>
                    </li>
                    <!-- Ссылки для неавторизованных -->
                    <li v-if="!user" class="nav-item">
                        <Link href="/login" class="nav-link">Войти</Link>
                    </li>
                    <li v-if="!user" class="nav-item">
                        <Link href="/register" class="nav-link">Регистрация</Link>
                    </li>
                    <li v-if="!user" class="nav-item">
                        <Link href="/contacts" class="nav-link">Контакты</Link>
                    </li>
                    <!-- Профиль -->
                    <li v-if="user" class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            id="navbarDropdown"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="currentColor"
                                class="bi bi-person-fill"
                                viewBox="0 0 16 16"
                            >
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                            </svg>
                            Профиль
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <Link href="/account/profile" class="dropdown-item">Мой профиль</Link>
                            <Link href="/account/orders" class="dropdown-item">Мои заказы</Link>
                            <hr class="dropdown-divider" />
                            <a class="dropdown-item" href="#" @click.prevent="logout">Выйти</a>
                        </div>
                    </li>
                    <!-- Поддержка -->
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            id="navbarSupportDropdown"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="bi bi-question-circle-fill"
                                viewBox="0 0 16 16"
                            >
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                            </svg>
                            Поддержка
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarSupportDropdown">
                            <a class="dropdown-item" href="mailto:info@biletavto.ru">info@biletavto.ru</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <section class="hero-section text-center">
        <div class="container d-flex flex-column justify-content-center">
            <h2 class="display-5 font-weight-bold animated fadeInDown">Найдите свои автобусные билеты</h2>
            <p class="lead animated fadeIn">Бронируйте автобусные билеты легко и просто.</p>
            <form @submit.prevent="submitSearchForm" class="search-form mt-4">
                <div class="input-group row justify-content-center gap-3">
                    <div class="mb-3 position-relative">
                        <input
                            v-model="form.from"
                            type="text"
                            class="form-from"
                            id="from"
                            placeholder="Откуда"
                            @input="handleFromInput"
                            @focus="showSuggestions = 'from'"
                            @blur="handleBlur"
                            autocomplete="off"
                            required
                        />
                        <ul
                            v-if="showSuggestions === 'from' && suggestions.from.length > 0"
                            class="suggestions-list"
                        >
                            <li
                                v-for="suggestion in suggestions.from"
                                :key="suggestion.id"
                                @mousedown.prevent="selectSuggestion('from', suggestion)"
                            >
                                {{ suggestion.city }}
                            </li>
                        </ul>
                    </div>
                    <div class="mb-3 position-relative">
                        <input
                            v-model="form.to"
                            type="text"
                            class="form-to"
                            id="to"
                            placeholder="Куда"
                            @input="handleToInput"
                            @focus="showSuggestions = 'to'"
                            @blur="handleBlur"
                            autocomplete="off"
                            required
                        />
                        <ul
                            v-if="showSuggestions === 'to' && suggestions.to.length > 0"
                            class="suggestions-list"
                        >
                            <li
                                v-for="suggestion in suggestions.to"
                                :key="suggestion.id"
                                @mousedown.prevent="selectSuggestion('to', suggestion)"
                            >
                                {{ suggestion.city }}
                            </li>
                        </ul>
                    </div>
                    <button
                        type="button"
                        class="round-button round-button--active button-icon-container"
                        @click="swapValues"
                    >
                        <svg
                            class="arrow-icon arrow-icon--up"
                            width="13"
                            height="6"
                            viewBox="0 0 13 6"
                            fill="currentColor"
                        >
                            <path d="M3 4V6L0 3L3 0V2H13V4H3Z" fill="currentColor" />
                        </svg>
                        <svg
                            class="arrow-icon arrow-icon--down"
                            width="13"
                            height="6"
                            viewBox="0 0 13 6"
                            fill="currentColor"
                        >
                            <path d="M3 4V6L0 3L3 0V2H13V4H3Z" fill="currentColor" />
                        </svg>
                    </button>
                    <div class="date mb-3">
                        <flat-pickr
                            v-model="form.date"
                            :config="flatpickrConfig"
                            class="form-date"
                            id="date"
                            placeholder="Выберите дату"
                            required
                        />
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                            style="display: inline-block; pointer-events: none"
                        >
                            <path d="M8 10h3v3H8v-3Z" />
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M9 3H7v1a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3V3h-2v1H9V3ZM6 17V8h12v9a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1Z"
                            />
                        </svg>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="button-search">Найти билеты</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    
</template>

<script setup>
import { computed, onMounted, watch, ref } from 'vue';
import { Link, useForm, router, usePage } from '@inertiajs/vue3';
import { useAuthStore } from '@/stores/auth';
import { useSearchStore } from '@/stores/search';
import { useLoadingStore } from '@/stores/loading';
import { useOrderDetailsStore} from "@/stores/orderDetails.js";
import axios from 'axios';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import { Russian } from 'flatpickr/dist/l10n/ru.js';
import { toast } from 'vue3-toastify';
import { storeToRefs } from 'pinia';

// Хранилища Pinia
const searchStore = useSearchStore();
const loadingStore = useLoadingStore();
const orderDetailsStore = useOrderDetailsStore();
const authStore = useAuthStore();
const page = usePage();

authStore.setUser(page.props.auth.user);

// Реактивные переменные для предложений
const suggestions = ref({ from: [], to: [] });
const showSuggestions = ref(null);

// Конфигурация для Flatpickr
const flatpickrConfig = {
    dateFormat: 'd.m.Y',
    locale: Russian,
    disableMobile: true,
    minDate: 'today',
};

// Форма для поиска через Inertia
const form = useForm({
    from: page?.props?.data?.from || '',
    to: page?.props?.data?.to || '',
    date: page?.props?.data?.date || ''
});
const { from: storeFrom, to: storeTo, date: storeDate } = storeToRefs(searchStore);
const user = computed(() => authStore.user);
const isAdmin = computed(() => user.value?.role === "admin");

// Функция отправки формы поиска
const submitSearchForm = () => {
    loadingStore.setLoading(true)
    orderDetailsStore.clear()
    router.get('/search', form.data())
};

// Функция выхода
const logout = () => {
    useForm().post('/logout', {
        onSuccess: () => {
            authStore.logout();
            toast.success('Вы успешно вышли!');
        },
        onError: () => {
            toast.error('Ошибка при выходе.');
        },
    });
};

// Функция обмена значениями
const swapValues = () => {
    const temp = form.from;
    form.from = form.to;
    form.to = temp;
};

// Дебаунс-функция
const debounce = (fn, delay, leading = false) => {
    const timeouts = new Map();
    return (...args) => {
        const key = JSON.stringify(args);
        const shouldCallNow = leading && !timeouts.has(key);
        if (timeouts.has(key)) {
            clearTimeout(timeouts.get(key));
        }
        const timeoutId = setTimeout(() => {
            timeouts.delete(key);
            if (!leading) fn(...args);
        }, delay);
        timeouts.set(key, timeoutId);
        if (shouldCallNow) fn(...args);
    };
};

// Обработка ввода для поля "from"
const handleFromInput = debounce(async () => {
    if (form.from.length >= 2) {
        try {
            const response = await axios.get('/api/search', {
                params: { query: form.from, type: 'from' },
            });
            if (response.data.success) {
                suggestions.value.from = response.data.suggestions;
                showSuggestions.value = 'from';
            }
        } catch (error) {
            console.error('Error fetching from suggestions:', error);
        }
    } else {
        suggestions.value.from = [];
        showSuggestions.value = null;
    }
}, 300, true);

// Обработка ввода для поля "to"
const handleToInput = debounce(async () => {
    if (form.to.length >= 2) {
        try {
            const response = await axios.get('/api/search', {
                params: { query: form.to, type: 'to' },
            });
            if (response.data.success) {
                suggestions.value.to = response.data.suggestions;
                showSuggestions.value = 'to';
            }
        } catch (error) {
            console.error('Error fetching to suggestions:', error);
        }
    } else {
        suggestions.value.to = [];
        showSuggestions.value = null;
    }
}, 300, true);

// Обработка потери фокуса
const handleBlur = () => {
    setTimeout(() => {
        showSuggestions.value = null;
    }, 200);
};

// Выбор предложений
const selectSuggestion = (field, suggestion) => {
    if (field === 'from') {
        form.from = suggestion.city;
    } else {
        form.to = suggestion.city;
    }
    suggestions.value[field] = [];
    showSuggestions.value = null;
};

watch(
    () => page.props.auth,
    (newAuth) => {
        authStore.setUser(newAuth?.user);
    },
    { deep: true }
);
watch(
  () => page.props.data, // следим за нужным пропсом
  (newData, oldData) => {
    form.from = newData ? newData.from : null;
    form.to = newData ? newData.to : null;
    form.date = newData ? newData.date : null;
  },
  {
    deep:true
  }
);
</script>

<style scoped>
/* Стили остаются без изменений */
.suggestions-list {
    list-style: none;
    margin: 0;
    padding: 0;
    position: absolute;
    top: 100%;
    left: 0;
    color: #0c131d;
    width: 100%;
    background: white;
    border: 1px solid #ddd;
    z-index: 10;
}

.suggestions-list li {
    padding: 8px 12px;
    cursor: pointer;
    text-align: start;
}

.suggestions-list li:hover {
    background: #f0f0f0;
}

h2.display-5 {
    font-size: 2.5rem;
    color: #FFF;
    position: relative;
    z-index: 2;
    font-family: Montserrat, sans-serif;
    font-weight: 700;
    text-shadow: none;
    display: block; /* чтобы поведение было как у h1 */
    margin: 0; /* убрать лишние отступы, если нужно */
}
</style>
