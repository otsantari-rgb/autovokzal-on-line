<template>
    <div class="contacts-container">
        <Head>

            <title>Автовокзал Улан-Удэ | Контакты</title>
            <meta name="description" content="Автовокзал Улан-Удэ. Контактная информация. Бронируйте автобусные
билеты онлайн без очередей! Актуальное расписание, удобная оплата
и поддержка.">
        </Head>

        <h1>Контактная информация</h1>
        
        <div class="contact-details">
            
            <div class="contact-row">
                <span class="label">ОГРН:</span>
                <span>1250300003425</span>
            </div>
            
            <div class="contact-row">
                <span class="label">ИНН/КПП:</span>
                <span>0300032250 / 030001001</span>
            </div>
            
            <div class="contact-row">
                <span class="label">Адрес для почтовых отправлений:</span>
                <span>670002, г. Улан-Удэ, ул. Буйко, 20а, офис 1</span>
            </div>

            <div class="contact-row">
                <span class="label">Электронная почта:  </span>
                <span><a href="autovokzal@biletavto.ru"></a>autovokzal@biletavto.ru</span>
            </div>

             <div class="contact-row">
                <span class="label">Телефон: </span>
                <span><a href="tel:8 (9021) 66-80-03">8 (9021) 66-80-03</a></span>
            </div>

             <p><strong>ООО «БИЛЕТТО»</strong></p>

        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';

export default {
    components: {
        Head
    },
    props: {
        seo: Object
    }
}
</script>

<style scoped>
.contacts-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
}

.contact-details {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
}

.contact-row {
    margin-bottom: 12px;
    display: flex;
    flex-wrap: wrap;
}

.label {
    font-weight: bold;
    min-width: 220px;
    margin-right: 10px;
}

@media (max-width: 600px) {
    .contact-row {
        flex-direction: column;
    }
    
    .label {
        margin-bottom: 5px;
        min-width: auto;
    }
}
</style>