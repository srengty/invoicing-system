<template>
    <Head title="Create Agreement" />
    <GuestLayout>
        <h1>Create Agreement</h1>
        <Form @submit="submit" :action="route('agreements.store')" v-slot="$form" :initial-values="form">
            <div class="create-agreement flex flex-row gap-4">
                <div class="border border-gray-200 rounded-lg p-4 w-2/5">
                    <div class="grid grid-cols-2 gap-1">
                        <span>Quotation No.</span>
                        <InputNumber name="quotation_no" :use-grouping="false"/>
                        <span>Agreement No. {{ agreement_max }}</span>
                        <InputNumber name="agreement_no" :class="(errors.agreement_no?'p-invalid':'')" :use-grouping="false" :readonly="true" />
                        <Message v-if="errors.agreement_no" severity="error" size="small" variant="simple" class="col-span-2">{{ errors.agreement_no }}</Message>
                        <span>Date</span>
                        <DatePicker date-format="dd/mm/yy" name="date" showIcon :show-on-focus="false"/>
                        <Message v-if="errors.date" severity="error" size="small" variant="simple" class="col-span-2">{{ errors.date.error }}</Message>
                        <span>Customer</span>
                        <Select filter name="customer_id" :options="customers" option-value="id" option-label="name" :virtualScrollerOptions="{ itemSize: 38 }"/>
                        <Message v-if="errors.customer" severity="error" size="small" variant="simple" class="col-span-2">{{ errors.customer.error }}</Message>
                        <span>Address</span>
                        <InputText name="address" />
                        <Message v-if="errors.address" severity="error" size="small" variant="simple" class="col-span-2">{{ errors.address.error }}</Message>
                        <span>Agreement doc</span>
                        <FileUpload name="agreement_doc" />
                        <Message v-if="errors.agreement_doc" severity="error" size="small" variant="simple" class="col-span-2">{{ errors.agreement_doc.error }}</Message>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 w-2/5">
                    <div class="grid grid-cols-2 gap-1">
                        <span class="col-span-2 text-xl mb-5">Agreement summary</span>
                        <span>Start date</span>
                        <DatePicker date-format="dd/mm/yy" name="start_date"/>
                        <Message v-if="errors.start_date" severity="error" size="small" variant="simple" class="col-span-2">{{ errors.start_date }}</Message>
                        <span>End date</span>
                        <DatePicker date-format="dd/mm/yy" name="end_date"/>
                        <Message v-if="errors.end_date" severity="error" size="small" variant="simple" class="col-span-2">{{ errors.end_date }}</Message>
                        <span>Agreement amount (exclude tax)</span>
                        <InputNumber name="agreement_amount" />
                        <Message v-if="errors.agreement_amount" severity="error" size="small" variant="simple" class="col-span-2">{{ errors.agreement_amount }}</Message>
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
            <PaymentSchedule class="mt-2" v-model="form.payment_schedule"/>
            <Button label="Submit" type="submit" />
        </Form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button, DatePicker, FileUpload, InputMask, InputNumber, InputText, Message, Select } from 'primevue';
import { Form } from '@primevue/forms';
import { useToast } from "primevue/usetoast";
import { reactive, onMounted } from 'vue';
import PaymentSchedule from './PaymentSchedule.vue';
const props = defineProps({ errors: Object, customers: Array, agreement_max: Number });
const toast = useToast();
const form = reactive({
    quotation_no: null,
    agreement_no: null,
    date: new Date('01/20/2025'),
    customer_id: null,
    address: null,
    agreement_doc: null,
    progress: null,
    start_date: '01/21/2025',
    end_date: '01/21/2025',
    agreement_amount: 0,
    tax_amount: 0,
    attachment: null,
    payment_schedule: [
    {
        id: 1,
        due_date: "28/01/2025",
        short_description: "Item description",
        remark: "Additional remark",
        amount: 2000,
    },
    {
        id: 2,
        due_date: "04/02/2025",
        short_description: "Item description",
        remark: "Additional remark",
        amount: 5000,
    },
    {
        id: 3,
        due_date: "11/02/2025",
        short_description: "Item description",
        remark: "Additional remark",
        amount: 3000,
    },
    ],
    attachment: null,
});
onMounted(() => {
    form.agreement_no = props.agreement_max;
})
const submit = (e) => {
    router.post(route('agreements.store'), form);
    //form.post(route('agreements.store'));
    // if(e.valid){
    //     router.push(route('agreements.store'), form);
    //     toast.add({ severity: 'success', summary: 'Success', detail: 'Agreement created successfully' });
    // }else{
    //     toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill all required fields' });
    // }
}
// const resolver = zodResolver(z.object({
//     agreement_no: z.number(),
//     date: z.date(),
//     customer: z.string().nonempty(),
//     address: z.string().nonempty(),
//     agreement_doc: z.string().nonempty(),
//     start_date: z.date(),
//     end_date: z.date(),
//     agreement_amount: z.number(),
//     tax_amount: z.number(),
//     attachment: z.string().nonempty(),
//     payment_schedule: z.array(z.object({
//         id: z.number(),
//         due_date: z.string().nonempty(),
//         short_description: z.string().nonempty(),
//         remark: z.string().nonempty(),
//         amount: z.number(),
//     })),
// }));
</script>

<style >
    span.p-invalid input {
        border-color: var(--p-inputtext-invalid-border-color);
    }
</style>