<template>
    <ModalComponent id="order-respond-modal" head="Сообщение">
        <h3 class="bg-gray">Спасибо, что откликнулись<br>на заяку «{{ order_name }}» от {{ order_date }}</h3>
<!--        <hr>-->
<!--        <p class="small text-center"><b>Вас ждут вас по адресу:</b></p>-->
<!--        <h6 class="order-address text-center mt-0">{{ order_address }}</h6>-->
        <hr>
        <p class="w-100 text-center"><a :href="chat_url + '?id=' + order_id">Подробности вы можете обсудить в чате.</a></p>
        <hr>
        <h1 class="text-orange text-center">Спасибо!</h1>
    </ModalComponent>
</template>

<script>
import ModalComponent from "./ModalComponent.vue";
export default {
    name: "OrderRespondModalComponent",
    components: {
        ModalComponent
    },
    props: {
        'order_id': Number,
        'order_name': String,
        'order_date': String,
        'order_address': String,
        'chat_url': String
    }
}
</script>
