<template>
    <div class="integer-range-field">
    <input type="number" class="form-control" v-model="min" min="0">
    <span class="dash">-</span>
    <input type="number" class="form-control" v-model="max" min="0">
    </div>
</template>
<script>
export default {
    name: 'integerRangeField',
    props: ['min', 'max']
}
</script>