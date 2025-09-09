<template>
    <div class="router-view">
        <div class="container">
            <div class="not-found">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 19l-4.35-4.35A7.9 7.9 0 0 0 18 9c0-4.41-3.59-8-8-8S2 4.59 2 9s3.59 8 8 8c2.1 0 4.07-.79 5.65-2.15L19 21l2-2zM9 14c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"></path>
                </svg>

                <h1>Заполните поля поиска</h1>
                <p>Чтобы узнать расписание и цены на рейс, пожалуйста, заполните поля "Откуда", "Куда" и выберите дату поездки.</p>
                <Link href="/" class="btn btn-primary btn-lg">Вернуться на главную</Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.container{
    padding-top: 50px;
    padding-bottom: 50px;
    max-width: 800px;
}
.not-found {
    text-align: center;
    background: #FFFFFF;
    border-radius: 20px;
    padding: 50px 20px;
}
.not-found h1 {
    font-size: 30px;
}
.not-found p {
    font-size: 18px;
}
.not-found a {
    display: inline-block;
    margin-top: 20px;
    font-size: 16px;
    color: #FFFFFF;
}
.btn {
    margin-top: 20px;
}
</style>
