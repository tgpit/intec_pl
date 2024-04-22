BX.Vue.component ('manage', {
    props: [
        'module',
        'profile_id',
    ],
    data(){
        return {
            module: {},
            profile: {},
            plugin : {},
            dates: {
                date_beg: '',
                date_end: '',
            },
           mess: {},
           menu: {},
        }
    },
    methods: {
        switch_marker(index) {
            for (let key in this.menu) {
                if ( key == index ) {
                    this.menu[key].active = true;
                }
                else {
                    this.menu[key].active = false;
                }
            }
        },

    },

    computed: {
    },

    mounted() {
        this.plugin = {
            'ModuleId': this.module,
            'ID': this.profile_id
        };
        // console.log(this.menu);
        let profile_req = {
            'action': 'get_props',
            'plugin': this.plugin,
        };
        //
        this.$parent.executeBase( profile_req ).success( data => {
            this.mess = data.mess;
            this.menu = data.menu;
            console.log(this.menu);
        });

    },

    template: `
        <div class="acrit_orders_wrapper"> 
            <div class="acrit_orders_menu adm-detail-tabs-block adm-detail-tabs-block-settings adm-detail-tabs-block-pin">       
                   <span v-for="(item, index) in this.menu" :key="index" class="adm-detail-tab" :data="index" @click="switch_marker(index)" :style="[ menu[index].active ? { background:'white' } : { background:'none' } ]">{{item.name}}</span>
            </div>            
            <div class="acrit_orders_wrapper" v-for="(item, index) in this.menu">
                   <component :is="index" :ref="index" v-show="item.active" />
            </div>
        </div>
`
});