import Vuex from 'vuex';
import Vue from 'vue';
import meetings from './modules/meetings'

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        meetings
    }
});
