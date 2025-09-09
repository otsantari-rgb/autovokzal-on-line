<template>
  <div class="router-view">
    <div class="container">
        <div class="row">
            <div class="col options">
                <div class="options-card">
                    <Link class="options-card-item" href="/account/profile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="options-card-svg" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                        </svg> Мой профиль
                    </Link>
                    <Link class="options-card-item" href="/account/orders">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="options-card-svg" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" data-test-id="icon" style="display: inline-block;">
                            <path d="M5.193 6h13.868l-.596-1.638a3 3 0 0 0-3.845-1.793L5.193 6Z">
                            </path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5 8a3 3 0 0 0-3 3v6a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-1a2 2 0 1 1 0-4v-1a3 3 0 0 0-3-3H5Zm4.207 8.707 4.5-4.5-1.414-1.414L8.5 14.586l-1.793-1.793-1.414 1.414 2.5 2.5h1.414Z">
                            </path>
                        </svg> Мои заказы
                    </Link>
                    <Link class="options-card-item" href="/account/docs">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="options-card-svg" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" data-test-id="icon" style="display:inline-block">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20 5a3 3 0 0 0-3-3H4v20h13a3 3 0 0 0 3-3V5ZM7 11h10V9H7v2Zm10-4H7V5h10v2ZM9 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z">
                            </path>
                        </svg> Мои документы
                    </Link>
                </div>
            </div>
            <div class="col">
                <slot />
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.container {
  margin: 0 auto;
  padding-top: 50px;
  padding-bottom: 50px;
}


.options {
    max-width: 400px;
}

.options-card {
    background: #ffffff;
    display: flex;
    flex-direction: column;
    padding: 30px;
    gap: 5px;
    border-radius: 16px;
    position: sticky;
    top: 20px;
    box-shadow: 0 4px 12px -4px hsla(60, 4%, 60%, .35), 0 0 2px hsla(60, 4%, 60%, .3);
    min-width: 270px;
}
.options-card-item {
    color: black;
    font-size: 18px;
    font-weight: bold;
    text-decoration: none;
    padding: 10px;
    border-radius: 10px;
}

.options-card-item:hover {
    text-decoration: none;
    background: #ebebed;
    color: #0c73fe;
}

.options-card-svg {
    height: 32px;
    width: 32px;
    background: #ebebed;
    padding: 2px;
    border-radius: 10px;
    color: gray;
    margin-right: 5px;
}
.options-card-item:hover .options-card-svg {
    color: #0c73fe;
    background: #dbeafe; /* Немного светлее фон */
}
</style>
