<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>

        <div class="flex p-2">
            <input type="number"
                   v-model="min"
                   class="block w-full form-control-sm form-input form-input-bordered mr-2"
                   placeholder="min" 
                   name="min" 
            >
            <input type="number"
                   v-model="max"
                   ref="max_range"
                   class="block w-full form-control-sm form-input form-input-bordered mr-2"
                   placeholder="max" 
                   name="max" 
            >
            
            <button type="button" v-model="changeBtn" class="block w-full form-control-sm form-input form-input-bordered" v-on:click="handleChange" >Filter</button>
        </div>
    </div>
</template>

<script>
export default {
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

    data() {
        return {
            min: '',
            max: '',
        }
    },

    methods: {
        handleChange(event) {
            console.log("handleChange"+ this.resourceName);
            console.log(this.min);
            console.log(this.max);
            this.$store.commit(`${this.resourceName}/updateFilterState`, {
                filterClass: this.filterKey,
                value: [this.min, this.max],
            })

            this.$emit('change')
        },
        getValue() {
            this.$emit('input', [0, 30]);
        },
    },
    watch: {

        value: _.debounce(function(newValue){
            console.log("calling backend");
            this.getValue();

        }, 500)
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
