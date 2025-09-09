<template>
    <div class="docs-container">
        <h2 class="account-title">Мои документы</h2>

        <div v-if="docs.length" class="saved-docs">
            <h5 class="saved-tittle">Сохранённые</h5>
            <ul class="row">
                <li class="saved-docs-mini col" v-for="doc in docs" :key="doc.id" @click="openModal(doc)">
                    <div class="saved-docs-mini-type">{{ doc.translated_type }}</div>
                    <div class="saved-docs-mini-name">{{ doc.firstname }} {{ doc.lastname ? doc.lastname[0] + '.' : '' }}</div>
                </li>
            </ul>
        </div>

        <div class="add-doc">
            <h5 class="add-doc-tittle">Новый документ</h5>
            <form @submit.prevent="addDocument">
                <div class="row mb-3">
                    <div class="new-doc-type col">
                        <label :for="newDoc.type" class="mr-2 font-weight-bold">Документ:</label>
                        <select v-model="newDoc.type" class="document-type" required>
                            <option value="passport">Паспорт</option>
                            <option value="birth_certificate">Свидетельство о рождении</option>
                        </select>
                    </div>

                    <div class="new-doc-gender  col">
                        <label :for="newDoc.gender" class="mr-2 align-self-center font-weight-bold">Пол:</label>
                        <div class="radio_group">
                            <label class="radio-label" data-qa="male-radioItem">
                                <input
                                    type="radio"
                                    :id="`male-radio[${newDoc}]`"
                                    autocomplete="off"
                                    class="radio-input"
                                    :name="newDoc.gender"
                                    value="male"
                                    v-model="newDoc.gender"
                                />
                                <span class="radio-text" data-qa="male-radioItem-text">Мужской</span>
                            </label>
                            <label class="radio-label" data-qa="female-radioItem">
                                <input
                                    type="radio"
                                    :id="`female-radio[${newDoc}]`"
                                    autocomplete="off"
                                    class="radio-input"
                                    :name="newDoc.gender"
                                    value="female"
                                    v-model="newDoc.gender"
                                />
                                <span class="radio-text" data-qa="female-radioItem-text">Женский</span>
                            </label>
                        </div>
                        <div>

                        </div>
                        <div class="switcher" :class="{'switcher-male': newDoc.gender === 'male', 'switcher-female': newDoc.gender === 'female'}" aria-hidden="true"></div>
                    </div>

                </div>

                <h6 class="doc-info-tittle">Данные документа</h6>
                <div class="row mb-3">
                    <div class="col">
                        <input v-model="newDoc.lastname" placeholder="Фамилия" class="name-input" required />
                    </div>
                    <div class="col">
                        <input v-model="newDoc.firstname" placeholder="Имя" class="name-input" required />
                    </div>
                    <div class="col">
                        <input v-model="newDoc.patronymic" placeholder="Отчество" class="name-input"/>
                    </div>



                </div>
                <div class="row">
                    <div class="col">
                        <input v-model="newDoc.number" class="number-input" placeholder="Номер документа" required />
                    </div>
                    <div class="col">
                        <label :for="newDoc.birthday" class="mr-2">Дата рождения:</label>
                        <input v-model="newDoc.birthday" class="birthday-input" type="date" placeholder="Дата рождения" required />
                    </div>

                </div>


                <button class="btn btn-success mt-3" type="submit">Добавить</button>
            </form>
        </div>
                <!-- Модальное окно -->
        <div v-if="selectedDoc" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content">
                <h5 class="mb-3"><b>{{ selectedDoc.firstname }} {{ selectedDoc.lastname ? selectedDoc.lastname[0] + '.' : '' }}</b></h5>
                <div class="modal-doc-container">
                    <h5 class="mb-3"><b>Документ</b></h5>
                    <select v-model="editedDoc.type" class="edited-doc-type mb-3">
                        <option value="passport">Паспорт</option>
                        <option value="birth_certificate">Свидетельство о рождении</option>
                    </select>

                    <div class="radio_group_modal mb-3">
                        <label class="radio-label-modal">
                            <input type="radio" class="radio-input" value="male" v-model="editedDoc.gender" />
                            <span class="radio-text">Мужской</span>
                        </label>
                        <label class="radio-label-modal">
                            <input type="radio" class="radio-input" value="female" v-model="editedDoc.gender" />
                            <span class="radio-text">Женский</span>
                        </label>
                        <div class="switcher-modal" :class="{'switcher-male-modal': editedDoc.gender === 'male', 'switcher-female-modal': editedDoc.gender === 'female'}" aria-hidden="true"></div>
                    </div>
                    <div class="input-modal-group">
                        <label v-if="editedDoc.lastname" class="label-modal" :for="editedDoc.lastname">Фамилия</label>
                        <input v-model="editedDoc.lastname" placeholder="Фамилия" class="name-input input-modal" required />
                    </div>
                    <div class="input-modal-group">
                        <label v-if="editedDoc.firstname" class="label-modal" :for="editedDoc.firstname">Имя</label>
                        <input v-model="editedDoc.firstname" placeholder="Имя" class="name-input input-modal" required />
                    </div>
                    <div class="input-modal-group">
                        <label v-if="editedDoc.patronymic" class="label-modal" :for="editedDoc.patronymic">Отчество</label>
                        <input v-model="editedDoc.patronymic" placeholder="Отчество" class="name-input input-modal"/>
                    </div>
                    <div class="input-modal-group">
                        <label v-if="editedDoc.number" class="label-modal" :for="editedDoc.number">Номер документа</label>
                        <input v-model="editedDoc.number" class="number-input input-modal" placeholder="Номер документа" required />
                    </div>
                    <div class="input-modal-group">
                        <label class="label-modal" :for="editedDoc.birthday">Дата рождения</label>
                        <input v-model="editedDoc.birthday" class="birthday-input input-modal" type="date" placeholder="Дата рождения" required />
                    </div>
                    <button @click="deleteDocument" class="btn btn-outline-danger mb-3 w-100">Удалить</button>
                    <button @click="saveChanges" class="btn btn-success w-100">Сохранить</button>
                </div>

                <button @click="closeModal" class="close-modal-btn">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="s__idyythExEVp8VQdGMhYN" data-test-id="icon" style="display: inline-block;">
                        <path d="M13.625 12.375 9.25 8l4.375-4.375-1.25-1.25L8 6.75 3.625 2.375l-1.25 1.25L6.75 8l-4.375 4.375 1.25 1.25L8 9.25l4.375 4.375 1.25-1.25Z">

                        </path>
                    </svg>
                </button>


            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { toast } from 'vue3-toastify';
import AccountLayout from './Account.vue'
import AppLayout from '@/App.vue'
import { router, useForm } from '@inertiajs/vue3';
defineOptions({
    layout: [AppLayout, AccountLayout]
})

const props = defineProps({
    docs: Array,
});

const selectedDoc = ref(null);
const editedDoc = ref(null);
const newDoc = useForm({
    firstname: '',
    lastname: '',
    patronymic: '',
    number: '',
    type: 'passport',
    gender: 'male',
    birthday: '',
});

const openModal = (doc) => {
    selectedDoc.value = doc;
    editedDoc.value = { ...doc };
};

const closeModal = () => {
    selectedDoc.value = null;
    editedDoc.value = {};
};

const extractErrorMessages = (obj) => {
    let messages = [];

    for (const key in obj) {
        if (typeof obj[key] === 'object' && obj[key] !== null) {
            messages = messages.concat(extractErrorMessages(obj[key])); // рекурсивный вызов для вложенных объектов
        } else {
            messages.push(obj[key]);
        }
    }

    return messages;
};

const addDocument = () => {
    console.log(newDoc)
    newDoc.post('/account/docs', {
        onSuccess: () => {
            toast.success('Документ успешно добавлен!');
            newDoc.reset();
            router.reload({ only: ['docs'] });
        },
        onError: (error) => {
            console.log(error)
            const errorMessages = extractErrorMessages(error);

            if (errorMessages.length > 0) {
                // Объединяем сообщения в одну строку
                const errorText = errorMessages.join(', ');
                toast.error(errorText || 'Ошибка при добавлении документа');
            } else {
                toast.error('Неизвестная ошибка');
            }
        },
    });
};

const saveChanges = () => {
    router.put(`/account/docs/${editedDoc.value.id}`, editedDoc.value, {
        onSuccess: () => {
            toast.success('Документ успешно обновлён');
            closeModal();
            router.reload({ only: ['docs'] });
        },
        onError: () => {
            toast.error('Ошибка при сохранении');
        },
    });
};

const deleteDocument = () => {
    router.delete(`/account/docs/${editedDoc.value.id}`, {
        onSuccess: () => {
            toast.success('Документ успешно удалён');
            closeModal();
            router.reload({ only: ['docs'] });
        },
        onError: () => {
            toast.error('Ошибка при удалении');
        },
    });
};
</script>

<style scoped>
.input-modal-group {
    position: relative;
    width: 100%;
}
.label-modal {
    position: absolute;
    left: 12px;
    font-size: 12px;
    color: gray;
}
.input-modal {
    width: 100%;
    margin-bottom: 16px;
    padding-top: 12px;
}
.modal-doc-container {
    background: #ffffff;
    border-radius: 18px;
    display: flex;
    flex-direction: column;
    justify-content: start;
    align-items: start;
    padding: 20px;
}
.doc-info-tittle {
    font-size: 18px;
    font-weight: bold;
}

.edited-doc-type {
    font-size: 16px;
    height: 44px;
    border-radius: 8px;
    color: #0d0d0f;
    padding-right: 12px;
    padding-left: 12px;
    background-color: rgb(255, 255, 255);
    border: 1px solid rgb(227, 225, 220);
    width: 100%;
}

.document-type,
.name-input,
.number-input,
.birthday-input{
    font-size: 16px;
    height: 44px;
    border-radius: 8px;
    color: #0d0d0f;
    padding-right: 12px;
    padding-left: 12px;
    background-color: rgb(255, 255, 255);
    border: 1px solid rgb(227, 225, 220);
    margin-bottom: 10px;
}

.document-type:hover,
.name-input:hover,
.number-input:hover,
.birthday-input:hover {
    border: 1px solid rgb(179, 179, 178);
}

.document-type:focus,
.document-type:active,
.name-input:focus,
.name-input:active,
.number-input:focus,
.number-input:active,
.birthday-input:focus,
.birthday-input:active {
    border: 1px solid #ffeb3b;
    outline: 1px solid #ffeb3b;
}

.new-doc-type {
    max-width: max-content;
}

.new-doc-gender {
    display: flex;
    max-width: max-content;
}

.docs-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.account-title {
    align-self: start;
    padding-left: 15px;
    font-weight: bold;
}

ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.saved-docs, .add-doc {
    background: #ffffff;
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0 4px 12px -4px hsla(60, 4%, 60%, .35), 0 0 2px hsla(60, 4%, 60%, .3);
}

.saved-tittle, .add-doc-tittle {
    font-weight: bold;
    font-size: 22px;
}

.saved-docs-mini {
    padding: 15px;
    background: #f8f8f9;
    margin: 5px;
    max-width: 270px;
    min-width: 180px;
    cursor: pointer;
    transition: background 0.3s;
    border-radius: 10px;
}

.saved-docs-mini:hover {
    background: #e0e0e0;
}

.saved-docs-mini-type {
    color: gray;
    font-size: 14px;
}

.saved-docs-mini-name {
    font-weight: bold;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2;
}

.modal-content {
    position: relative;
    background: #eff1f4;
    padding: 20px;
    border-radius: 20px;
    max-width: 600px;
    text-align: center;
}

.close-modal-btn {
    position: absolute;
    margin-top: 15px;
    border: none;
    background: rgba(225, 229, 223, 1);
    color: #5a6472;
    cursor: pointer;
    border-radius: 50%;
    transition: background 0.3s;
    height: 28px;
    width: 28px;
    top: 0;
    right: 20px;
}

.close-modal-btn:hover {
    background: rgb(185, 188, 184);
}

.radio_group_modal {
    position: relative;
    background: #efecec;
    border-radius: 8px;
    height: 44px;
    padding: 4px 2px;
    width: 100%;
}

.switcher-modal {
    top: 0;
    width: 50%;
    margin-top: 2px;
    height: 40px;
    position: absolute; /* Абсолютное позиционирование */
    background-color: #FFFFFF; /* Цвет фона переключателя */
    border-radius: 8px; /* Закругленные углы */
    box-shadow: 0 4px 12px -4px hsla(60, 4%, 60%, .35), 0 0 2px hsla(60, 4%, 60%, .3);
    transition: left 0.3s ease; /* Плавное смещение переключателя */
    z-index: 0;
    left: 1%; /* Начальная позиция переключателя для "Мужской" */
}
.switcher-male-modal {
    left: 1%;
}

/* Когда выбрано "Женский", переключатель двигается вправо */
.switcher-female-modal {
    left: 49%; /* Перемещается на 49% для женского */
}
.radio-label-modal {
    cursor: pointer; /* Курсор указателя при наведении */
    display: inline-block; /* Позволяет размещать метки рядом */
    padding: 6px 18px; /* Отступы по бокам */
    position: relative; /* Установить контекст позиционирования для переключателя */
    border-radius: 8px;
    width: 50%;
}
.radio_group {
    position: relative;
    background: #efecec;
    border-radius: 8px;
    height: 44px;
    padding: 4px 2px;
    width: max-content;
    min-width: 208px;
}
.radio-label {
    cursor: pointer; /* Курсор указателя при наведении */
    display: inline-block; /* Позволяет размещать метки рядом */
    padding: 6px 18px; /* Отступы по бокам */
    position: relative; /* Установить контекст позиционирования для переключателя */
    border-radius: 8px;
}

.radio-input {
    display: none; /* Скрыть стандартный стиль радио-кнопок */
}

/* Для выделенного текста радио-кнопки */
.radio-input:checked {
    font-weight: bold; /* Делает текст выделенным для выбранной радио-кнопки */
}

.radio-text {
    display: inline-block;
    position: relative;
    z-index: 1; /* Чтобы текст находился поверх переключателя */
    font-size: 16px;
    font-weight: 500;
}

.switcher {
    width: 35%;
    margin-top: 2px;
    height: 40px;
    position: absolute; /* Абсолютное позиционирование */
    background-color: #FFFFFF; /* Цвет фона переключателя */
    border-radius: 8px; /* Закругленные углы */
    box-shadow: 0 4px 12px -4px hsla(60, 4%, 60%, .35), 0 0 2px hsla(60, 4%, 60%, .3);
    transition: left 0.3s ease; /* Плавное смещение переключателя */
    z-index: 0;
    left: 1%; /* Начальная позиция переключателя для "Мужской" */
}

/* Когда выбрано "Мужской", переключатель двигается влево */
.switcher-male {
    left: 22%;
}

/* Когда выбрано "Женский", переключатель двигается вправо */
.switcher-female {
    left: 59%; /* Перемещается на 49% для женского */
}
</style>
