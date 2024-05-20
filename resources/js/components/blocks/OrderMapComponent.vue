<template>
    <TextAreaComponent
        name="address"
        max="255"
        :value="address"
        v-model:value="address"
        :error="addressError"
        @keyup="changeAddressInInput"
    ></TextAreaComponent>
    <select
        id="addresses"
        class="form-select"
        v-model="featureMemberIndex"
        v-show="addresses.length"
        @change="changeAddressInSelect"
    >
        <option v-for="(address, k) in addresses" :key="'address-' + k" :value="k">{{ address }}</option>
    </select>
</template>

<script>
import TextAreaComponent from "./TextAreaComponent.vue";

export default {
    name: "OrderMapComponent",
    components: {
        TextAreaComponent,
    },
    created() {
        let self = this;
        this.address = this.incoming_address;
        if (this.address && this.get_place_mark) {
            setTimeout(() => {
                self.getApiPlaceMark();
            }, 500);
        }
    },
    props: {
        'incoming_address': String,
        'yandex_api_key': String,
        'get_place_mark': Boolean
    },
    emits: ['setPlaceMark','disabledButtons'],
    data() {
        return {
            geoObjectCollection: null,
            featureMemberIndex: 0,
            address: '',
            addresses: [],
            addressError: null,
        }
    },
    methods: {
        getApiPlaceMark() {
            let self = this;
            this.$emit('disabledButtons',true);
            this.address = this.address.indexOf('Москва') >= 0 ? this.address : 'Москва, ' + this.address;
            if (!this.geoObjectCollection) {
                // Getting placemark from api
                axios.get('https://geocode-maps.yandex.ru/1.x/?apikey=' + this.yandex_api_key + '&geocode=' + this.address + '&format=json')
                    .then(function (response) {
                        self.geoObjectCollection = response.data.response.GeoObjectCollection;
                        if (parseInt(self.geoObjectCollection.metaDataProperty.GeocoderResponseMetaData.found) === 1) {
                            self.showPlaceMark();
                            self.$emit('setPlaceMark',self.address);
                        } else {
                            self.addresses = [];
                            $.each(self.geoObjectCollection.featureMember, function (k,featureMember) {
                                self.addresses.push(featureMember.GeoObject.description + ' ' + featureMember.GeoObject.name);
                            });
                            $('#addresses')[0].size = self.addresses.length + 0.9;
                            self.$emit('disabledButtons',false);
                            self.changeAddressInSelect();
                        }
                    });
            } else {
                self.showPlaceMark();
                this.$emit('setPlaceMark',this.address);
            }
        },
        changeAddressInInput() {
            this.addressError = null;
            this.geoObjectCollection = null;
            this.addresses = [];
        },
        changeAddressInSelect() {
            this.addressError = null;
            let featureMember = this.geoObjectCollection.featureMember[this.featureMemberIndex];
            this.address = featureMember.GeoObject.description + ' ' + featureMember.GeoObject.name;
            this.showPlaceMark();
        },
        showPlaceMark() {
            if (window.placemark) window.myMap.geoObjects.remove(window.placemark);
            let coordinates = this.geoObjectCollection.featureMember[this.featureMemberIndex].GeoObject.Point.pos.split(' ');
            window.singlePoint = [parseFloat(coordinates[1]), parseFloat(coordinates[0])];
            let newPlaceMark = window.getPlaceMark(window.singlePoint, {});

            window.myMap.geoObjects.add(newPlaceMark)
            window.zoomAndCenterMap();
        },
    }
}
</script>
