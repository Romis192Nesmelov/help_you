<template>
    <ModalComponent id="incentive-delete-confirm-modal" head="Закрытие заявки">
        <h4 class="text-center">Вы действительно хотите удалить эту акцию?</h4>
        <ModalPairButtonsComponent @click-yes="deleteIncentive"></ModalPairButtonsComponent>
    </ModalComponent>

    <div id="incentives" class="col-12 col-lg-8 right-block">
        <div class="rounded-block tall">
            <h2>Поощрения от партнеров</h2>
            <div class="h-100 without-tabs d-flex flex-column justify-content-between" v-if="incentives.length">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 mb-2" v-for="incentive in incentives" :key="incentive.id">
                        <div class="item-in-list action text-center p-3">
                            <div>
                                <div class="fw-bold fs-6">Акция</div>
                                <div class="lh-sm fs-5">{{ incentive.action.name }}</div>
                            </div>
                            <div class="fs-6 fw-bold">{{ getDate(incentive.action.start) + ' - ' + getDate(incentive.action.end) }}</div>
                            <div class="w-100 d-flex justify-content-center">
                                <a class="w-50 me-1" :href="incentive_url + '?id=' + incentive.action.id">
                                    <ButtonComponent
                                        class="btn btn-primary w-100 m-auto"
                                        text="Подробнее"
                                    ></ButtonComponent>
                                </a>
                                <ButtonComponent
                                    class="btn btn-primary w-50 m-auto"
                                    text="Удалить"
                                    @click="confirmDeleteIncentive(incentive.id)"
                                ></ButtonComponent>
                            </div>
                        </div>
                    </div>
                </div>
                <PaginatorComponent
                    :links="links"
                    tab_key="actions"
                    @paginate="changePage"
                ></PaginatorComponent>
            </div>
            <NoDataComponent v-else></NoDataComponent>
        </div>
    </div>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
import NoDataComponent from "./blocks/NoDataComponent.vue";
import OrdersTabsComponent from "./blocks/OrdersTabsComponent.vue";
import ModalPairButtonsComponent from "./blocks/ModalPairButtonsComponent.vue";
import PaginatorComponent from "./blocks/PaginatorComponent.vue";

export default {
    name: "IncentivesComponent",
    components: {
        ModalComponent,
        ButtonComponent,
        NoDataComponent,
        OrdersTabsComponent,
        ModalPairButtonsComponent,
        PaginatorComponent,
    },
    created() {
        this.userId = parseInt(this.user_id);
        this.getIncentives(this.get_incentives_url);

        window.Echo.private('incentive_' + this.userId).listen('.incentive', res => {
            if (res.notice === 'remove_incentive') {
                let index = null;
                $.each(this.incentives, function (key,incentive) {
                    if (incentive.id === res.incentive.id) {
                        index = key;
                        return false;
                    }
                });
                if (index !== null) this.getIncentives(this.get_incentives_url);
            } else if (res.notice === 'new_incentive') this.getIncentives(this.get_incentives_url);
        });
    },
    data() {
        return {
            incentives: [],
            links: [],
            userId: Number,
            deletingActionId: 0
        }
    },
    props: {
        'user_id': String,
        'get_incentives_url': String,
        'incentive_url': String,
        'delete_incentives_url': String
    },
    methods: {
        confirmDeleteIncentive(id) {
            $('#incentive-delete-confirm-modal').modal('show');
            this.deletingActionId = id;
        },
        deleteIncentive() {
            let self = this;
            axios.post(self.delete_incentives_url, {
                _token: window.tokenField,
                id: self.deletingActionId
            }).then(function (response) {
                // self.getIncentives(self.incentives_url);
            });
        },
        getIncentives(url) {
            let self = this;
            axios.get(url).then(function (response) {
                self.incentives = response.data.data;
                self.links = response.data.links;

                if (!self.incentives.length) {
                    window.emitter.emit('incentives', false);
                }
            });
        },
        changePage(params) {
            this.getIncentives(params.url);
        },
        getDate(date) {
            return new Date(date).toLocaleDateString('ru-RU');
        },
    }
}
</script>
