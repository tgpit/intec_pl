BX.Vue.createApp ({
    el: '#app',
    data: {
        link: '/bitrix/admin/acrit_core_orders_tab_ajax.php',
    },
    methods: {
        executeBase(data ) {
            return $.ajax({
                type: 'POST',
                method: 'POST',
                url: this.link,
                dataType: 'json',
                data: data,
                timeout: 10000000
            }).success(function (data) {
                // console.log(data);
                return data;
            }).error(function (x, t, e) {
                if( t === 'timeout') {
                    console.log("Timeout");
                    alert ('Error - Timeout' );
                } else {
                    console.log('Error++: ', e);
                    alert('Error: '+ e );
                }
            });
        }
    },


    computed: {

    },
    mounted() {
        // console.log("Привет")
    },

    created() {},

});
