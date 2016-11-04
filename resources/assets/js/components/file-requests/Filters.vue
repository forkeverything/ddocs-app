<template>
    <ul class="dropdown-menu filters-menu">
        <li @click.stop="">
            <p class="text-muted">Show where</p>
            <select class="form-control select-filter"
                    v-model="name"
                    placeholder="Select one..."
            >
                <option value="" disabled>Select filter</option>
                <option v-for="option in filterOptions" :value="option.value">{{ option.label }}
                </option>
            </select>

            <!-- Filter: Version -->
            <span class="text-muted" v-show="name === 'version'">is between</span>
            <div class="filter-fields version" v-show="name === 'version'">
                <integer-range-field v-model="range"></integer-range-field>
            </div>

            <!-- Filter: Due (Date) -->
            <span class="text-muted" v-show="name === 'due'">is between</span>
            <div class="filter-fields due" v-show="name === 'due'">
                <date-range-field v-model="range"></date-range-field>
            </div>

            <!-- Filter: Status -->
            <div class="filter-fields status form-group" v-show="name === 'status'">
                <p class="text-muted">is</p>
                <select v-model="value" class="form-control">
                    <option value="" disabled>Pick one</option>
                    <option value="received">Received</option>
                    <option value="waiting">Waiting</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <!-- Filter Button -->
            <button class="button-add-filter btn btn-primary"
                    v-show="name && (value || range)"
                    @click.stop.prevent="addFilter">Add Filter
            </button>
        </li>
    </ul>
</template>
<script>
    export default {
        data: function () {
            return {
                name: '',
                value: '',
                range: ''
            }
        },
        props: ['filter-options'],
        methods: {
            addFilter(){
                this.$emit('add-filter', {
                    name: this.name,
                    value: this.value,
                    minValue: this.range.minValue,
                    maxValue: this.range.maxValue
                });
            }
        }
    }
</script>