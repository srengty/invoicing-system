<template>
    <div>
        <h1>Add Payment</h1>
        <FloatLabel>
            <InputText id="total-agreement-amount" type="text" size="30" v-model="inputtext" />
            <label for="total-agreement-amount">Total agreement amount *</label>
        </FloatLabel>
Due date

Short description

Percentage

Amount

Currency
    </div>
</template>

<script setup>
import { FloatLabel } from 'primevue';
</script>

<style lang="scss" scoped>

</style>