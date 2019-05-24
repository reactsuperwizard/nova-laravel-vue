<template>
    <div class="w-full form-control form-input pt-2 pl-2 pr-2">
        <vue-slider ref="slider" v-bind="chenglu" v-model="chenglu.value"></vue-slider>
    </div>
</template>

<script>
import vueSlider from 'vue-slider-component';
import _ from 'lodash';

export default {
    components: {
        vueSlider
    },
    props: {
        value: {
            type: Array,
            default: []
        },
        options: {
            type: Object,
            default: () => ({})
        },
        minRange: {
          type: Number,
          default: 0
        },
        maxRange: {
          type: Number,
          default: 100
        },
        intervalVal: {
          type: Number,
          default: 1
        },
    },

    data() {
      return {
        chenglu: {
          value: [0, 80],
            width: '100%',
            height: 8,
            dotSize: 16,
            min: this.minRange,
            max: this.maxRange,
            interval: this.intervalVal,
            disabled: false,
            show: true,
            useKeyboard: true,
            tooltip: 'always',
            formatter: '{value}',
            enableCross: false,
            mergeFormatter: '{value1} ~ {value2}',
        }
      }
    },

    computed: {
        width() {
            return this.getOption('width') || "100%";
        },
        height() {
            return this.getOption('height') || "8";
        },
        minimum(){
            return this.getOption('minimum') || 0;
        },
        maximum(){
            return this.getOption('maximum') || 100;
        },
        useKeyboard(){
            return this.getOption('useKeyboard') || true;
        },
        tooltip(){
            return this.getOption('tooltip') || "always";
        },
    },
    mounted() {
        // console.log("mounted");

        this.$nextTick( () => {
            this.value = [this.minimum, this.maximum];
        });
        const wrapper = document.querySelector('.dropdown-menu div');
        wrapper.classList.remove('overflow-hidden');
    },
    watch: {

        value: _.debounce(function(newValue){
            console.log("watch");
            this.getValue();

        }, 300)
    },
    methods: {
        getOption(name) {
        // console.log("getOption"+name);

            const option = this.options.find(o => o.name === name);
            if (!option) return false;
            return option.value;
        },
        getValue() {
        console.log("getValue");

            this.$emit('input', this.$refs['slider'].getValue());
        },
        setValue(min_value, max_value) {

        // console.log("setValue");

            // alert(this.$refs['slider'].value);
            // console.log(this.$refs['slider']);
            this.$refs['slider'].value = [min_value, max_value];
        }
    }
}
</script>

<style>
/* Scoped Styles */
.noUi-tooltip {
  display: none;
}
.noUi-active .noUi-tooltip {
  display: block;
}
</style>
