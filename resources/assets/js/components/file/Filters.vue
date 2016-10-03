<template>
    <ul class="dropdown-menu filters-menu">
        <li @click.stop="">
            <p class="text-muted">Show where</p>
            <select class="form-control select-filter"
                    v-model="name"
                    placeholder="Select one..."
            >
                <option value="" selected disabled>Select filter</option>
                <option v-for="option in filterOptions" :value="option.value">{{ option.label }}
                </option>
            </select>


            <!-- Filter: Version -->
            <p class="text-muted" v-show="filter === 'version'">is between</p>
            <div class="filter-fields version" v-show="filter === 'version'">
                <integer-range-field v-model="range"></integer-range-field>
            </div>

            <!-- Filter: Due (Date) -->
            <p class="text-muted" v-show="filter === 'due'">is between</p>
            <div class="filter-fields due" v-show="filter === 'due'">
                <date-range-field v-model="range"></date-range-field>
            </div>

            <!-- Filter: Status -->
            <div class="filter-fields status" v-show="filter === 'status'">
                <p class="text-muted">is</p>
                <select v-model="filterValue" class="form-control">
                    <option value="" selected disabled>Pick one</option>
                    <option value="received">Received</option>
                    <option value="waiting">Waiting</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <!-- Filter Button -->
            <button class="button-add-filter btn btn-primary"
                    v-show="filter && (filterValue || minFilterValue || maxFiltervalue)"
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
        props: ['filter-options', 'add-filter'],
        methods: {
            processFilter(){
                this.addFilter({
                    name: this.name,
                    value: this.value,
                    minValue: this.range.minValue,
                    maxValue: this.range.maxValue
                });
            }
        }
    }
</script>