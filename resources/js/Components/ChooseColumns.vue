
<template>
    <div class="card inline-flex justify-center mx-2">
        <Button type="button" :label="selectedMember ? selectedMember.name : 'Select Columns'" @click="toggle" icon="pi pi-plus" />

        <Popover ref="op">
            <div class="flex flex-col gap-4">
                <div>
                    <span class="font-medium block mb-2">Team Members</span>
                    <Listbox v-model="model" :options="columns" multiple optionLabel="header" class="w-full border-white" checkmark="" />
                    <Button label="Apply" class="mt-2" @click="selectColumns()"/>
                </div>
            </div>
        </Popover>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Popover, Button, Listbox } from 'primevue';

defineProps({
    columns: {
        type: Array,
    },
});
const model = defineModel({
    type: Array,
});
const emit = defineEmits(['apply']);
const op = ref();
const selectedMember = ref(null);
const members = ref([
    { name: 'Amy Elsner', image: 'amyelsner.png', email: 'amy@email.com', role: 'Owner' },
    { name: 'Bernardo Dominic', image: 'bernardodominic.png', email: 'bernardo@email.com', role: 'Editor' },
    { name: 'Ioni Bowcher', image: 'ionibowcher.png', email: 'ioni@email.com', role: 'Viewer' }
]);

const toggle = (event) => {
    op.value.toggle(event);
}

const selectedCity = ref();
const cities = ref([
{ field: 'id', header: 'ID' },
    { field: 'agreement_no', header: 'Agreement No' },
    { field: 'agreement_ref_no', header: 'Agreement Ref No' },
    { field: 'agreement_date', header: 'Agreement Date' },
    { field: 'address', header: 'Address' },
    { field: 'agreement_doc', header: 'Agreement Doc' },
    { field: 'start_date', header: 'Start Date' },
    { field: 'end_date', header: 'End Date' },
    { field: 'amount_no_tax', header: 'Amount' },
    { field: 'tax', header: 'Tax' },
    { field: 'status', header: 'Status' },
    { field: 'quotation_no', header: 'Quotation No.' },
    { field: 'customer_id', header: 'Customer ID' },
]);
const selectColumns = () => {
    members.value[0].name = `Showing ${model.value.length} Columns`;
    selectedMember.value = members.value[0];
    op.value.hide();
    emit('apply', model.value);
}
</script>
