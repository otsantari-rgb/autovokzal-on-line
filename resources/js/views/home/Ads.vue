<template>
    <div class="ads">
        <div class="ad">
            <div id="uu-irkutsk">
                <!-- Inertia ссылка -->
                <Link 
                    class="uu-irkutsk-info-btn"
                    :href="`ulan-ude--irkutsk`"
                    method="get"
                    preserveState
                >
                    Улан-Удэ - Иркутск
                </Link>

                <!-- Кнопка выбора даты -->
                <button 
                    class="uu-irkutsk-choose-btn" 
                    @click="chooseDate('Улан-Удэ - Иркутск')"
                >
                    Выбрать дату
                </button>
            </div>
        </div>

        <div class="ad">
            <div id="maksimiha-uu">
                <!-- Inertia ссылка для Максимиха - Улан-Удэ -->
                <Link 
                    class="maksimiha-uu-info-btn"
                    :href="`/search?date=&from=Максимиха&to=Улан-Удэ`"
                    method="get"
                    preserveState
                >
                    Максимиха - Улан-Удэ
                </Link>

                <!-- Кнопка выбора даты -->
                <button 
                    class="maksimiha-uu-choose-btn" 
                    @click="chooseDate('Максимиха - Улан-Удэ')"
                >
                    Выбрать дату
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useSearchStore } from '@/stores/search';

const searchStore = useSearchStore();

const chooseDate = (route) => {
  const [from, to] = route.split(' - ');
  searchStore.setSearchQuery({ from, to });

  const searchPanel = document.getElementById('search-panel');
  if (searchPanel) searchPanel.scrollIntoView({ behavior: 'smooth' });
};
</script>


<style scoped>
.ads {
    display: flex;
    max-width: 1000px;
    align-items: center;
    justify-content: center;
    text-align: center;
    flex-wrap: wrap;
}

@media (min-width: 1200px) {
    .ads {
        min-width: 1000px;
        min-height: 418px;
    }
}

@media (max-width: 1200px) {
    .ads {
        min-height: 750px;
    }
}

.ad {
    flex: 0 0 50%;
    background: black;
    background-size: cover;
    max-width: 388px;
    aspect-ratio: 1 / 1;
    color: white;
    padding-bottom: 30px;
    text-align: center;
    margin: 15px;
    min-width: 345px;
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
}

#uu-irkutsk {
    position: relative;
    background: black url('/img/important_routes/uu-irkutsk.webp');
    background-size: cover;
    max-width: 388px;
    aspect-ratio: 1 / 1;
}
#maksimiha-uu {
    position: relative;
    background: black url('/img/important_routes/goryachinsk-uu2.webp');
    background-size: cover;
    max-width: 388px;
    aspect-ratio: 1 / 1;
}
.maksimiha-uu-info-btn {
    position: absolute;
    background: #e45c24;
    border: none;
    outline: none;
    color: black;
    top: 37%;
    padding: 8px 8px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: bold;
    right: 5%;
}
.maksimiha-uu-choose-btn {
    position: absolute;
    background: #fc292d;
    border: none;
    outline: none;
    color: white;
    bottom: 12%;
    padding: 4px 8px;
    border-radius: 10px;
    font-size: 13px;
    right: 9%;
}
.uu-irkutsk-info-btn {
    position: absolute;
    background: #e45c24;
    border: none;
    outline: none;
    color: black;
    top: 64%;
    padding: 8px 8px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: bold;
    left: 8%;
}
.uu-irkutsk-choose-btn {
    position: absolute;
    background: #fc292d;
    border: none;
    outline: none;
    color: white;
    bottom: 13%;
    padding: 4px 8px;
    border-radius: 10px;
    font-size: 13px;
    left: 10%;
}
</style>
