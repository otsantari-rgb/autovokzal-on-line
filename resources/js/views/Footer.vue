<template>
  <footer class="footer bg-white text-dark py-4" style="font-family: 'Inter', sans-serif;">
    <div class="container">
      <div class="row">
        <div class="col-md-2">
          <ul class="list-unstyled">
            <li class="mb-2"><strong>ГОРОДА</strong></li>
            <li v-for="arrival in footerCities" :key="arrival">
              <a href="#" @click.prevent="scrollToTop('city', arrival)" class="footer-list">
                {{ arrival }}
              </a>
            </li>
          </ul>
        </div>
        <div class="col-md-5">
          <ul class="list-unstyled">
            <li class="mb-2"><strong>АВТОВОКЗАЛЫ И АВТОСТАНЦИИ БУРЯТИИ</strong></li>
            <li
              v-for="(station, key) in footerStations"
              :key="station.name"
              style="cursor: pointer;"
            >
              <Link :href="`/stations/${key}`" class="footer-list">{{ station.name }}</Link>
            </li>
          </ul>
        </div>
        <div class="col-md-3">
          <ul class="list-unstyled">
            <li class="mb-2"><strong>НАПРАВЛЕНИЯ</strong></li>
            <li v-for="(value, key) in footerRoutes" :key="key">
              <Link :href="`/${value.key}`" class="footer-list" @click="scrollToTopRoute">
                {{ value.name }}
              </Link>
            </li>
            <li class="mt-3">
              <Link href="/more-routes" style="text-decoration: none;" class="more-info">
                <span>Больше популярных направлений </span>
                <span>
                  <svg
                    width="16"
                    height="16"
                    fill="none"
                    viewBox="0 0 16 16"
                    xmlns="http://www.w3.org/2000/svg"
                    focusable="false"
                  >
                    <path
                      d="M14 9.5028C14.5523 9.5028 15 9.95051 15 10.5028V13.5028C15 14.3313 14.3284 15.0028 13.5 15.0028H2.5C1.67157 15.0028 1 14.3313 1 13.5028V2.5028C1 1.67437 1.67157 1.0028 2.5 1.0028H5.5C6.05228 1.0028 6.5 1.45051 6.5 2.0028C6.5 2.55508 6.05228 3.0028 5.5 3.0028H3V13.0028H13V10.5028C13 9.95051 13.4477 9.5028 14 9.5028ZM12.3 2.78694V2.46357L11.1503 2.77787C10.6125 2.92489 10.0575 2.99938 9.5 2.99938C8.9 2.99938 8.5 2.55166 8.5 1.99829C8.5 1.446 8.94772 0.998291 9.5 0.998291H14C14.5523 0.998291 15 1.446 15 1.99829V6.5028C15 7.05508 14.5523 7.5028 14 7.5028C13.4477 7.5028 13 7.10006 13 6.5028C13 5.94348 13.0747 5.38666 13.222 4.8471L13.5353 3.70006H13.2144L12.1366 5.13695C11.8524 5.51593 11.5415 5.87423 11.2065 6.20915L7.70697 9.70731C7.31637 10.0978 6.6832 10.0977 6.29276 9.70703C5.90231 9.31643 5.90243 8.68326 6.29303 8.29282L9.79386 4.79335C10.1265 4.46088 10.4821 4.15229 10.8581 3.86987L12.3 2.78694Z"
                      fill="currentColor"
                    ></path>
                  </svg>
                </span>
              </Link>
            </li>
          </ul>
        </div>
        <div class="col-md-2">
          <ul class="list-unstyled">
            <li class="mb-2"><strong>МЫ В СОЦИАЛЬНЫХ СЕТЯХ</strong></li>
            <li>
              <a href="https://vk.com/biletavto" target="_blank" rel="noopener">
                <img class="social-media-img" src="/img/vk-logo.svg" alt="vk-logo" />
              </a>
            </li>
            <li>
              <a href="https://ok.ru/biletavto" target="_blank" rel="noopener">
                <img class="social-media-img" src="/img/ok-logo.svg" alt="ok-logo" />
              </a>
            </li>
            <li>
              <a href="https://t.me/BiletAvto_bot" target="_blank" rel="noopener">
                <img class="social-media-img" src="/img/tg-logo.svg" alt="tg-logo" />
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="row mt-3">
        <div class="offset-md-7 footer-documents">
          <Link href="/policy" @click="scrollToTopRoute">Политика обработки персональных данных</Link>
          <Link href="/personal-data" @click="scrollToTopRoute">Согласие на обработку своих персональных данных</Link>
          <Link href="/oferta" @click="scrollToTopRoute">Пользовательское соглашение</Link>
        </div>
      </div>

      <div class="row">
        <div class="copyright">
          <p>&copy; {{ currentYear }} Система бронирования автобусных билетов. Все права защищены.</p>
        </div>
        
        <div class="col text-md-right">
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <Link href="/contacts" class="footer-list" >Контакты</Link>
            </li>
            <li class="list-inline-item">
              <Link href="/faq" class="footer-list" @click="scrollToTopRoute">FAQ</Link>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Модальные окна -->

    <div
      v-if="showContactModal"
      class="modal fade show"
      tabindex="-1"
      role="dialog"
      aria-labelledby="contactModalLabel"
      aria-hidden="true"
      style="display: block;"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="contactModalLabel">Контакты</h5>
            <button type="button" class="close" @click="closeContactModal" aria-label="Закрыть">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="anchor-contact">
              <h3>Контактная информация</h3>
              <div>
                <p><b>ООО «БИЛЕТТО»</b></p>
              </div>
              <div>
                <p>
                  <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> ОГРН
                  1250300003425
                </p>
                <p>
                  <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> ИНН/КПП:
                  0300032250 / 030001001
                </p>
              </div>
              <div>
                <p>
                  <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span> Адрес для
                  почтовых отправлений: 670002, г. Улан-Удэ, ул. Буйко, 20а, офис 1
                </p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeContactModal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>
    <AvtovokzalModal
      :station="selectedStation"
      :isVisible="showAvtovokzalModal"
      @close="closeAvtovokzalModal"
    />
<div v-if="showBanner && isHomePage" class="banner" id="banner">
  <button class="close-btn" @click="closeBanner">×</button>
  <p>Купи легко через телеграм</p>
  <a
    href="https://t.me/biletto_bot?start=site-aol"
    target="_blank"
    rel="nofollow noopener"
    class="tg-button"
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="16"
      height="16"
      fill="currentColor"
      class="bi bi-telegram"
      viewBox="0 0 16 16"
    >
      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09"/>
    </svg>
    Купить в Telegram
  </a>
</div>



  </footer>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useLoadingStore } from '@/stores/loading';
import axios from 'axios';
import AvtovokzalModal from './AvtovokzalModal.vue';
import { useSearchStore } from '@/stores/search';
import dayjs from 'dayjs';
import 'dayjs/locale/ru';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3'

const currentDate = dayjs().locale('ru').format('DD.MM.YYYY');

const loadingStore = useLoadingStore();
const loading = computed(() => loadingStore.loading);

const searchStore = useSearchStore();
const footerCities = ref([]);
const footerStations = ref([]);
const footerRoutes = ref([]);
const currentYear = new Date().getFullYear();

const showContactModal = ref(false);
const showFaqModal = ref(false);
const showAvtovokzalModal = ref(false);
const selectedStation = ref(null);

const fetchCities = async () => {
  try {
    const response = await axios.get('/api/cities');
    footerCities.value = response.data;
  } catch (error) {
    console.error('Ошибка при загрузке городов:', error);
  }
};

const fetchStations = async () => {
  loadingStore.setLoading(true);
  try {
    const response = await axios.get('/api/stations');
    footerStations.value = response.data;
  } catch (error) {
    console.error('Ошибка при загрузке автовокзалов:', error);
  } finally {
    loadingStore.setLoading(false);
  }
};

const fetchRoutes = async () => {
  loadingStore.setLoading(true);
  try {
    const response = await axios.get('/api/popular-routes');
    footerRoutes.value = response.data;
  } catch (error) {
    console.error('Ошибка при загрузке маршрутов:', error);
  } finally {
    loadingStore.setLoading(false);
  }
};

// Модальные окна
const openContactModal = () => {
  showContactModal.value = true;
};

const closeContactModal = () => {
  showContactModal.value = false;
};

const closeAvtovokzalModal = () => {
  showAvtovokzalModal.value = false;
};

const scrollToTopRoute = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const scrollToTop = (type, value) => {
    const toInput = document.getElementById('to');
    if (toInput) toInput.value = type === 'city' ? value : value.split(' - ')[1] || '';
    searchStore.setSearchQuery({ from: 'Улан-Удэ', to: toInput.value, date: currentDate });

    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    });
};

onMounted(() => {
  fetchCities();
  fetchStations();
  fetchRoutes();
});


const showBanner = ref(true)
const closeBanner = () => {
  showBanner.value = false
}

const page = usePage()
const isHomePage = computed(() => page.url === '/')
</script>


<style>
/* Фон модального окна */
.modal.fade .modal-dialog {
  transform: translate(0, 10%);
  transition: transform 0.3s ease-out;
}

/* Стили для контейнера модального окна */
.modal-dialog {
  max-width: 600px;
  margin: 30px auto;
}

/* Заголовок модального окна */
.modal-header {
  background-color: black;
  color: white;
  font-family: 'Inter', sans-serif;
  font-weight: 700;
}

/* Кнопка закрытия */
.modal-header .close {
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
}

/* Тело модального окна */
.modal-body {
  font-family: 'Inter', sans-serif;
  color: #333;
  padding: 20px 30px;
}

/* Кнопки */
.modal-footer .btn {
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 50px;
  transition: background-color 0.3s ease;
}

/* Затенение заднего фона модального окна */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1040;
}

/* Кнопка больше популярных направлений */
.more-info {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: 600;
  font-size: 14px;
  color: #000;
  cursor: pointer;
  text-decoration: none;
}

.more-info svg {
  fill: currentColor;
}

.footer-documents{
  display: flex;
  flex-direction: column;
  padding-left: 15px;
  padding-right: 15px;
  gap: 4px;
}

/* Социальные иконки */
.social-media-img {
  width: 24px;
  height: 24px;
  margin-right: 10px;
}

/* Ссылки в футере */
.footer-list {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

.footer-list:hover {
  text-decoration: underline;
}

.banner {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #262e3a;
  color: #fff;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
}
.banner a {
  color: #ffffff;
  text-decoration: none;
  font-weight: bold;
}
.close-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: #ff4d4d;
  border: none;
  border-radius: 2px;
  color: #fff;
  width: 20px;
  height: 20px;
  font-size: 14px;
  cursor: pointer;
  line-height: 20px;
  text-align: center;
}
.close-btn:hover {
  background-color: #e60000;
}
.tg-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background-color: #0088cc;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s;
}

.tg-button:hover {
  background-color: #0077b3;
}

.tg-button svg {
  fill: currentColor;
}
</style>