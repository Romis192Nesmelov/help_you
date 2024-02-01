<template>
    <h2 class="order-name text-dark text-left mt-3 mb-4">{{ name }}</h2>
    <h2 class="order-type text-dark text-left mt-3 h5">{{ type }}</h2>
    <ul class="subtypes" v-if="subtype">
        <li>{{ subtype }}</li>
    </ul>
    <p class="mb-1 text-left"><b>Адрес:</b> {{ address }}</p>
    <p class="fw-bold text-left mt-2 mb-0" v-if="description_short">Описание:</p>
    <p class="text-left order-description mb-1" v-if="description_short">{{ description_short }}</p>
</template>

<script>
export default {
    name: "OrderPropertiesComponent",
    props: {
        'name':String,
        'type':String,
        'subtype':String,
        'address':String,
        'description_short':String
    }
}
</script>
