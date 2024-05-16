import '../bootstrap';
import {createApp} from "vue/dist/vue.esm-bundler";
import mitt from 'mitt';
import UsersComponent from "../components/admin/UsersComponent.vue";

const app = createApp({
    components: {
        UsersComponent,
    }
});

window.tokenField = $('input[name=_token]').val();
window.emitter = mitt();
window.showMessageModal = false;
app.config.globalProperties.emitter = window.emitter;
app.mount('#app');

// window.allMonths = ['Янв.', 'Фев.', 'Март', 'Апр.', 'Май', 'Июнь', 'Июль', 'Авг.', 'Сент.', 'Окт.', 'Нояб.', 'Декаб.'];
// window.statisticsData = [];
$(document).ready(function () {
    $(".radio-input").bootstrapSwitch();
    $('.styled').uniform();

    $.mask.definitions['c'] = "[1-2]";
    $('input[name=phone]').mask("+7(999)999-99-99");
    $('input[name=born]').mask("99-99-9999");

    // File input
    $(".file-styled").uniform({
        wrapperClass: 'bg-blue',
        fileButtonHtml: '<i class="icon-file-plus"></i>'
    });

    // Message modal
    $('#message-modal').modal('show');

    // Single picker
    // $('.daterange-single').daterangepicker({
    //     singleDatePicker: true,
    //     locale: {
    //         format: 'DD/MM/YYYY',
    //         monthNames : ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
    //         daysOfWeek : ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
    //         week: moment.locale('en', {
    //             week: { dow: 1 }
    //         })
    //     }
    // });

    // Preview upload image
    $('input[type=file]').change(function () {
        let input = $(this)[0],
            parent = $(this).parents('.edit-image-preview'),
            imagePreview = parent.find('img');

        if (input.files[0].type.match('image.*')) {
            let reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.attr('src', e.target.result);
                if (!imagePreview.is(':visible')) imagePreview.fadeIn();
            };
            reader.readAsDataURL(input.files[0]);
        } else if (parent.hasClass('file-advanced')) {
            imagePreview.attr('src', '');
            imagePreview.fadeOut();
        } else {
            imagePreview.attr('src', '/images/placeholder.jpg');
        }
    });

    $('.fancybox').fancybox({
        'autoScale': true,
        'touch': false,
        'transitionIn': 'elastic',
        'transitionOut': 'elastic',
        'speedIn': 500,
        'speedOut': 300,
        'autoDimensions': true,
        'centerOnScroll': true
    });
});
