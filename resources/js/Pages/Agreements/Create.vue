<template>
    <Head title="Create Agreement" />
    <GuestLayout>
        <h1>Create Agreement</h1>
        <Form @submit.prevent="submit" v-slot="$form" :initial-values="form">
            <div class="create-agreement flex flex-row gap-4">
                <div class="border border-gray-200 rounded-lg p-4 w-2/5">
                    <div class="grid grid-cols-2 gap-1">
                        <span>Quotation No.</span>
                        <InputNumber name="quotation_no" :use-grouping="false"/>
                        <span>Agreement No.</span>
                        <InputNumber name="agreement_no" :use-grouping="false" />
                        <span>Date</span>
                        <DatePicker date-format="dd/mm/yy" name="date" showIcon :show-on-focus="false"/>
                        <span>Customer</span>
                        <InputText name="customer" />
                        <span>Address</span>
                        <InputText name="address" />
                        <span>Agreement doc</span>
                        <FileUpload name="agreement_doc" />
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 w-2/5">
                    <div class="grid grid-cols-2 gap-1">
                        <span class="col-span-2 text-xl mb-5">Agreement summary</span>
                        <span>Start date</span>
                        <DatePicker date-format="dd/mm/yy" name="start_date"/>
                        <span>End date</span>
                        <DatePicker date-format="dd/mm/yy" name="end_date"/>
                        <span>Agreement amount (exclude tax)</span>
                        <InputNumber name="agreement_amount" />
                        <span>Tax amount</span>
                        <InputNumber name="tax_amount" :min-fraction-digits="2" :max-fraction-digits="5"/>
                        <span>Payment schedule</span>
                        <Button label="Add payment schedule" />
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-1 gap-1">
                        <span>Attachment</span>
                        <FileUpload name="attachment[]" />
                    </div>
                </div>
            </div>
            <PaymentSchedule class="mt-2"/>
        </Form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import { Button, DatePicker, FileUpload, InputMask, InputNumber, InputText } from 'primevue';
import { Form } from '@primevue/forms';
import { useToast } from "primevue/usetoast";
import { reactive } from 'vue';
import PaymentSchedule from './PaymentSchedule.vue';
const toast = useToast();
const form = reactive({
    quotation_no: null,
    agreement_no: null,
    date: new Date('01/20/2025'),
    customer: null,
    address: null,
    agreement_doc: null,
    progress: null,
    start_date: '01/21/2025',
    end_date: '01/21/2025',
    agreement_amount: 0,
    tax_amount: 0,
    attachment: null,
    payment_schedule: [],
    attachment: null,
});
const submit = () => {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Agreement created successfully' });
}
</script>

<style lang="scss" scoped></style>