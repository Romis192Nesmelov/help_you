<template>
    <input type="hidden" name="latitude" :value="latitude">
    <input type="hidden" name="longitude" :value="longitude">

    <div class="panel panel-flat">
        <div class="panel-body">
            <OrderMapComponent
                :incoming_address="address"
                :yandex_api_key="yandex_api_key"
                :get_place_mark="true"
                ref="OrderMapComponent"
                @disabled-buttons="disableButton"
                @set-place-mark="setPlaceMark"
            ></OrderMapComponent>
            <ButtonComponent
                class="btn btn-secondary"
                text="Проверить адрес"
                :disabled="disabledButton"
                @click="checkAddress"
            ></ButtonComponent>
        </div>
    </div>
    <div class="panel panel-flat">
        <div class="panel-body">
            <div id="map-steps"></div>
        </div>
    </div>
</template>

<script>
import OrderMapComponent from "../../blocks/OrderMapComponent.vue";
import ButtonComponent from "../../blocks/ButtonComponent.vue";

export default {
    name: "EditOrderMapComponent",
    components: {
        OrderMapComponent,
        ButtonComponent
    },
    created() {
        this.address = this.incoming_address;
        this.latitude = parseFloat(this.incoming_latitude);
        this.longitude = parseFloat(this.incoming_longitude);
    },
    props: {
        'incoming_latitude': String|Number,
        'incoming_longitude': String|Number,
        'incoming_address': String,
        'yandex_api_key': String,
    },
    emits: ['setPlaceMark'],
    data() {
        return {
            address: String,
            disabledButton: false,
            latitude: 0,
            longitude: 0
        }
    },
    methods: {
        checkAddress() {
            if (!this.$refs.OrderMapComponent.address) this.$refs.OrderMapComponent.addressError = 'Укажите адрес!';
            else this.$refs.OrderMapComponent.getApiPlaceMark();
        },
        disableButton(flag) {
            this.disabledButton = flag;
        },
        setPlaceMark(address) {
            this.latitude = window.singlePoint[0];
            this.longitude = window.singlePoint[1];
            this.address = address;
            this.disabledButton = false;
            this.$emit('setPlaceMark',{
                address: this.address,
                latitude: this.latitude,
                longitude: this.longitude
            });
        }
    }
}
</script>
