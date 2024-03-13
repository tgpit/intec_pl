BX.Vue.component ('confirm', {
    props: [
        // 'module',
        // 'profile',
    ],
    data(){
        return {
            module: {},
            profile: {},
            plugin : {},
            result: '',
            profileJs: {},
            dates: {
                date_beg: '',
                date_end: '',
            },
            orders: [],
            check_all: false,
            spinner: false,
            basket: {},
            confirmated_arr: {},
            mess: {},
        }
    },
    methods: {
        set_date(event) {
            let name =  $(event.target).attr('name');
            let date = $(event.target).val();
            this.dates[name] = date;
        },

        consList(index){
          // console.log(this.orders[index]);
          return false;
        },

        showDetail(index){
            let order_id = this.orders[index].ID_SALE;
            if ( !this.orders[index].SHOW ) {
                let orders_req = {
                    'action': 'get_basket',
                    'order_id': order_id,
                };
                this.$parent.$parent.executeBase(orders_req).success(data => {
                    this.basket[data.order_id] = data.basket;
                    // console.log(this.basket);
                    this.orders[index].SHOW = !this.orders[index].SHOW;
                });
            } else {
                this.orders[index].SHOW = !this.orders[index].SHOW;
            }
        },

        checkAll(){
            for (let i=0; i < this.orders.length; i++ ) {
                if (this.orders[i]['ID_SALE'] !== null ) {
                    this.orders[i]['CONF'] = this.check_all;
                    this.conf_order(i);
                } else  {
                    this.orders[i]['CONF'] = false;
                    this.conf_order(i);
                }
            }
        },

        getBasket(order_id) {
            let orders_req = {
                'action' : 'get_basket',
                'order_id' : order_id,
            };
            this.$parent.$parent.executeBase( orders_req  ).success( data => {
                this.basket[data.order_id] = data.basket;
                this.orders[index].SHOW = !this.orders[index].SHOW;
            });
        },

        conf_order(index){
            for (let i = 0; i < this.orders[index].ITEMS.length; i++ ) {
                this.orders[index].ITEMS[i].conf = this.orders[index].CONF;
            }
        },

        conf_item(index) {
            let flag = 0;
            for (let i = 0; i < this.orders[index].ITEMS.length; i++ ) {
                if ( this.orders[index].ITEMS[i].conf ) {
                    flag++;
                }
            }
            if ( flag > 0 && !this.orders[index].CONF ) {
                this.orders[index].CONF = true;
            }
            if ( flag == 0 && this.orders[index].CONF ) {
                this.orders[index].CONF = false;
            }
        },

        confirm_orders() {
            let conf_arr = [];
            for (let i = 0; i < this.orders.length; i++) {
                if (this.orders[i].CONF) {
                    conf_arr.push(this.orders[i]);
                }
            }
            if (conf_arr.length == 0 ) {
                alert('Ничего не отмечено');
                return false;
            }
            let orders_req = {
                'action' : 'confirm_orders',
                'conf_arr': conf_arr,
                'plugin': this.plugin,
            };
            this.$parent.$parent.executeBase( orders_req  ).success( data => {
                this.confirmated_arr = data;
                // console.log(this.confirmated_arr);
                this.get_orders();
            });
        },

        enterElement(event){
          // console.log(event.target);
          return false;
        },

        get_orders() {
            this.spinner = true;
            this.orders = [];
            let orders_req = {
                'action': 'get_orders_for_confirm',
                'plugin': this.plugin,
                'data': this.dates,
            };
            this.$parent.$parent.executeBase( orders_req  ).success( data => {
                // console.log(data);
                this.orders = [... data.orders ];
                this.spinner = false;
                this.check_all = false;

            });
        },
    },

    computed: {
    },
    mounted() {
        this.mess = this.$parent.mess;
        this.plugin = {
            'ModuleId': this.$parent.module,
            'ID': this.$parent.profile_id
        };

        let profile_req = {
            'action': 'get_props_confirm',
            'plugin': this.plugin,
        };

        this.$parent.$parent.executeBase( profile_req ).success( data => {
            this.dates.date_beg = data.date_beg;
            this.dates.date_end = data.date_end;
        });

    },

    template: `
        <div class="acrit_orders_wrapper">        
            <div class="acrit_orders_header">
                <div><button type="button" class="adm-btn"  @click="get_orders()">{{ this.mess.getOrders }}</button></div>   
                <div>{{ this.mess.begDate }}<input type="text" size="10" :value="this.dates.date_beg" name="date_beg" @change="set_date" onclick="BX.calendar({node: this, field: this, bTime: false, weekStart: true});"></div>          
                <div>{{ this.mess.endDate }}<input type="text" size="10" :value="this.dates.date_end" name="date_end" @change="set_date" onclick="BX.calendar({node: this, field: this, bTime: false, weekStart: true});"></div>
                 <div><button type="button" class="adm-btn"  @click="confirm_orders()">{{ this.mess.confirmOrders }}</button></div>             
            </div>
            <div class="acrit_table_header">                
                <div class="acrit_table_item">{{ this.mess.idBitrix }}</div>
                <div class="acrit_table_item">{{ this.mess.numBitrix }}</div>
                <div class="acrit_table_item">{{ this.mess.summBitrix }}</div>
                <div class="acrit_table_item">{{ this.mess.nuIm }}</div>
                <div class="acrit_table_item">{{ this.mess.dateIm }}</div>
                <div class="acrit_table_item">{{ this.mess.sumIm }}</div>
                <div class="acrit_table_item">
                    <div>{{ this.mess.confirm }}</div>
                    <div v-show="orders.length>0" >
                        <input :class="'adm-designed-checkbox'" type="checkbox" id="check_all" v-model="check_all" @change="checkAll">
                        <label class="adm-designed-checkbox-label" for="check_all"></label>
                    </div>
                </div>
                <div class="acrit_table_item_detail"></div>
            </div>
            <div v-show="this.spinner" class="acrit_orders_items">
                <div class="spinner"></div>
            </div>
            <div class="acrit_orders_items" :class="{'items_border' : orders[index1]['SHOW'] , '' : !orders[index1]['SHOW'] }" 
            v-for="(item1, index1) in this.orders" :key="index1">
                <div class="acrit_orders_items_top">              
                    <div class="acrit_table_item">{{ item1.ID_SALE }}</div>                    
<!--                    <div class="acrit_table_item">-->
<!--                        <input type="text" v-model="orders[index]['ID_SALE']" @change="consList" v-on:keydown.enter.prevent="enterElement">-->
<!--                    </div>-->
                    <div class="acrit_table_item">{{ item1.NUMBER_SALE }}</div>
                    <div class="acrit_table_item">{{ item1.PRICE_SALE }}</div>
                    <div class="acrit_table_item">{{ item1.ID_MARKET }}</div>
                    <div class="acrit_table_item">{{ item1.DATE_INSERT }}</div>
                    <div class="acrit_table_item">{{ item1.SUMM_MARKET }}</div>
                    <div class="acrit_table_item">
                        <input :class="'adm-designed-checkbox'" type="checkbox" :id="'check1-'+index1" v-model="orders[index1]['CONF']"  @change="conf_order(index1)" :disabled="item1.ID_SALE===null">
                        <label class="adm-designed-checkbox-label" :for="'check1-'+index1"></label>
                    </div>
                    <div class="acrit_table_item_detail" 
                    :class="{'show_up' : orders[index1]['SHOW'], 'show_down' : !orders[index1]['SHOW'] }" :data-num="index1" 
                    @click="showDetail(index1)"> > </div>
                </div>
                <div class="acrit_orders_items_bottom" v-show="orders[index1]['SHOW']">                 
                    <div class="acrit_orders_items_bottom_left">
                        <div class="acrit_orders_items_name">{{ mess.dataBitrix }}</div>
                         <div class="acrit_orders_items_in">
                            <div class="acrit_table_item_in">{{ mess.idProd }}</div>
                            <div class="acrit_table_item_in">{{ mess.nameProd }}</div>
                            <div class="acrit_table_item_in">{{ mess.quantProd }}</div>
                            <div class="acrit_table_item_in">{{ mess.priceProd }}</div>
                        </div>    
                        <div class="acrit_orders_items_in" v-for="(item3, index3) in basket[item1.ID_SALE]" :key="index3">
                             <div class="acrit_table_item_in">{{ item3.PRODUCT_ID }}</div>
                             <div class="acrit_table_item_in">{{ item3.NAME }}</div>
                             <div class="acrit_table_item_in">{{ item3.QUANTITY }}</div>
                             <div class="acrit_table_item_in">{{ item3.PRICE }}</div>
                        </div>   
                    </div>
                    <div class="acrit_orders_items_bottom_centr"></div>    
                    <div class="acrit_orders_items_bottom_left">
                        <div class="acrit_orders_items_name">{{ mess.dataMarket }}</div>
                        <div class="acrit_orders_items_in">
                            <div class="acrit_table_item_in">{{ mess.idProd }}</div>
                            <div class="acrit_table_item_in">{{ mess.nameProd }}</div>
                            <div class="acrit_table_item_in">{{ mess.quantProd }}</div>
                            <div class="acrit_table_item_in">{{ mess.priceProd }}</div>
                            <div class="acrit_table_item_in">{{ mess.confirm }}</div>
                        </div>                       
                        <div class="acrit_orders_items_in" v-for="(item2, index2) in item1['ITEMS']" :key="index2">
                             <div class="acrit_table_item_in">{{ item2.marketId }}</div>
                             <div class="acrit_table_item_in">{{ item2.name }}</div>
                             <div class="acrit_table_item_in">{{ item2.quantity }}</div>
                             <div class="acrit_table_item_in">{{ item2.price }}</div>
                             <div class="acrit_table_item_in">
                                <input :class="'adm-designed-checkbox'" type="checkbox" :id="'check2-'+index1+'-'+index2" v-model="item2['conf']"  @change="conf_item(index1), consList(index1)" :disabled="item1.ID_SALE===null">
                                <label class="adm-designed-checkbox-label" :for="'check2-'+index1+'-'+index2"></label>
                             </div>
                        </div>                        
                    </div>                    
                </div>                               
            </div>
        </div>
`
});