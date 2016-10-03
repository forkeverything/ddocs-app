<template>
    <div class="integer-range-field">
    <input type="number" class="form-control" v-model="value.minValue" min="0">
    <span class="dash">-</span>
    <input type="number" class="form-control" v-model="value.maxValue" min="0">
    </div>
</template>
<script>
export default {
    name: 'integerRangeField',
    props: ['value']
}
</script>