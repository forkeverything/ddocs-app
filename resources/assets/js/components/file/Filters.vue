<template>
    <ul class="dropdown-menu filters-menu">
        <li @click.stop="">
            <p class="text-muted">Show where</p>
            <select class="form-control select-filter" v-model="filter"
                    placeholder="Select one...">
                <option value="" selected disabled>Select filter</option>
                <option v-for="option in filterOptions" :value="option.value">{{ option.label }}
                </option>
            </select>


            <!-- Filter: Version -->
            <p class="text-muted" v-show="filter === 'version'">is between</p>
            <div class="filter-fields version" v-show="filter === 'version'">
                <integer-range-field :min.sync="minFilterValue"
                                     :max.sync="maxFilterValue"></integer-range-field>
            </div>

            <!-- Filter: Due (Date) -->
            <p class="text-muted" v-show="filter === 'due'">is between</p>
            <div class="filter-fields due" v-show="filter === 'due'">
                <date-range-field :min.sync="minFilterValue"
                                  :max.sync="maxFilterValue"></date-range-field>
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
            <button class="button-add-filter btn btn-default"
                    v-show="filter && (filterValue || minFilterValue || maxFiltervalue)"
                    @click.stop.prevent="addFilter">Add Filter
            </button>
        </li>
    </ul>
</template>
<script>
export default {
   props: ['filter-options', 'min-filter-value', 'max-filter-value', 'filter', 'filter-value', 'add-filter']
}
</script>