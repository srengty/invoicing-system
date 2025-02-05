<template>
    <div class="flex flex-col gap-3 py-2">
        <FloatLabel variant="on">
            <InputText id="total-agreement-amount" v-model="model.agreement_amount" fluid readonly />
            <label for="total-agreement-amount required">Total agreement amount</label>
        </FloatLabel>

        <FloatLabel variant="on">
            <DatePicker id="due_date" size="30" v-model="model.due_date" fluid date-format="dd/mm/yy" />
            <label for="due_date" class="required">Due date</label>
        </FloatLabel>

        <FloatLabel variant="on">
            <Textarea id="short_description" v-model="model.short_description" fluid />
            <label for="short_description" class="required">Short description</label>
        </FloatLabel>

        <FloatLabel variant="on">
            <InputGroup id="percentage">
                <InputNumber v-model="model.percentage" fluid />
                <InputGroupAddon append>%</InputGroupAddon>
            </InputGroup>
            <label for="percentage" class="required z-10">Percentage</label>
        </FloatLabel>

        <FloatLabel variant="on">
            <InputGroup id="amount">
                <InputGroupAddon>{{ model.currency=='USD'?'$':'៛' }}</InputGroupAddon>
                <InputNumber v-model="model.amount" fluid />
            </InputGroup>
            <label for="amount" class="required z-10">Amount</label>
        </FloatLabel>
        <div class="flex items-center gap-2">
            <label for="currency" class="required">Currency</label>
            <RadioButtonGroup id="currency" v-model="model.currency" class="flex flex-wrap gap-4 w-100">
                <div class="flex items-center gap-2">
                    <RadioButton inputId="KHR" value="KHR" />
                    <label for="cheese">KHR</label>
                </div>
                <div class="flex items-center gap-2">
                    <RadioButton inputId="USD" value="USD" />
                    <label for="mushroom">USD</label>
                </div>
            </RadioButtonGroup>
        </div>
        <div class="flex justify-end gap-2">
            <Button label="Save" class="grow" @click="emit('save')"/>
            <Button label="Cancel" severity="secondary" @click="emit('cancel')" />
        </div>
    </div>
</template>

<script setup>
import { FloatLabel, InputText, Button, DatePicker, Textarea, RadioButtonGroup, RadioButton, InputGroup, InputGroupAddon, InputNumber } from 'primevue';
const emit = defineEmits(['cancel', 'save']);
const model = defineModel({
    type: Object,
});
</script>

<style lang="scss" scoped></style>