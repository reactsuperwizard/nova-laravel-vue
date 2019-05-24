<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">Chenglu {{ filter.name }}</h3>
        <RangeFilter
        v-model="filter.currentValue"
        :options="filter.options"
        :minRange="filter.minRange"
        :maxRange="filter.maxRange"
        :intervalVal="filter.intervalVal"
        @change="handleChange"
        />
    </div>
</template>

<script>
import RangeFilter from './RangeFilter';
import vueSlider from 'vue-slider-component';

export default {
    components: { RangeFilter },
    props: {
        resourceName: {
            type: String,
            required: true,
        },
        filterKey: {
            type: String,
            required: true,
        },
    },

    methods: {
        handleChange(event) {
            console.log("handling");
            this.$store.commit(`${this.resourceName}/updateFilterState`, {
                filterClass: this.filterKey,
                value: event.target.value,
            })

            this.$emit('change')
        },
    },

    computed: {
        filter() {
            return this.$store.getters[`${this.resourceName}/getFilter`](this.filterKey)
        },

        value() {
            return this.filter.currentValue
        },
    },
}
</script>
