<template>

    <Head title="Create Agreement" />
    <GuestLayout>
        <h1>Create Agreement</h1>
        <Form @submit="submit" :action="route('agreements.store')" v-slot="$form" :initial-values="form">
            <div class="create-agreement flex flex-row gap-4">
                <div class="border border-gray-200 rounded-lg p-4 w-2/5">
                    <div class="grid grid-cols-2 gap-1 items-center">
                        <span v-tooltip="'must be approved and no agreement attached'">Quotation No.</span>
                        <InputNumber name="quotation_no" :use-grouping="false" class="w-full" />
                        <span>Agreement No. {{ agreement_max }}</span>
                        <InputNumber name="agreement_no" :class="(errors.agreement_no ? 'p-invalid' : '')"
                            :use-grouping="false" :readonly="true" />
                        <Message v-if="errors.agreement_no" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.agreement_no }}</Message>
                        <span>Date</span>
                        <DatePicker date-format="dd/mm/yy" name="date" showIcon :show-on-focus="false" />
                        <Message v-if="errors.date" severity="error" size="small" variant="simple" class="col-span-2">{{
                            errors.date }}</Message>
                        <span>Customer</span>
                        <Select filter v-model="form.customer_id" :options="customers" option-value="id"
                            option-label="name" :virtualScrollerOptions="{ itemSize: 38 }" />
                        <Message v-if="errors.customer_id" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.customer_id }}</Message>
                        <span>Address</span>
                        <InputText name="address" />
                        <Message v-if="errors.address" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.address }}</Message>
                        <span>Agreement doc</span>
                        <!-- <FileUpload name="agreement_doc" auto customUpload @select="onFileSelect" mode="basic" :url="route('agreements.upload')" accept="image/*" :maxFileSize="1000000" @upload="onUpload"/> -->
                        <FileUpload name="agreement_doc" auto @before-upload="beforeUpload" mode="basic"
                            :url="route('agreements.upload')" accept="application/pdf" :maxFileSize="1000000"
                            @upload="onUpload" />
                        <Message v-if="errors.agreement_doc" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.agreement_doc }}</Message>
                        <span alt="Agreement doc" class="w-full col-span-2">
                            <a class="underline hover:text-red-800" v-if="src" :href="src" target="_blank"><i
                                    class="pi pi-file-pdf"></i> {{ src.split('/').pop() }}</a>
                        </span>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 w-2/5">
                    <div class="grid grid-cols-2 gap-1 items-center">
                        <span class="col-span-2 text-xl mb-5">Agreement summary</span>
                        <span>Start date</span>
                        <DatePicker date-format="dd/mm/yy" name="start_date" />
                        <Message v-if="errors.start_date" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.start_date }}</Message>
                        <span>End date</span>
                        <DatePicker date-format="dd/mm/yy" name="end_date" />
                        <Message v-if="errors.end_date" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.end_date }}</Message>
                        <span>Agreement amount ({{ currencies[riels == true ? 0 : 1].name }})</span>
                        <InputGroup>
                            <InputGroupAddon>
                                <ToggleSwitch v-model="riels" onLabel="Riels" offLabel="USD" offIcon="pi pi-dollar"
                                    onIcon="pi pi-dollar" />
                            </InputGroupAddon>
                            <InputNumber v-model="schedule.agreement_amount" />
                        </InputGroup>
                        <Message v-if="errors.agreement_amount" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.agreement_amount }}</Message>
                        <span>Short description</span>
                        <Textarea name="short_description" rows="2" />
                        <span>Payment schedule</span>
                        <PopupAddPaymentSchedule v-model="schedule" @save="doSave" @update="beforeUpdate"/>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-1 gap-1">
                        <span>Attachment</span>
                        <FileUpload name="attachment[]" />
                    </div>
                </div>
            </div>
            <PaymentSchedule class="mt-2" v-model="form.payment_schedule"
                :currency="currencies[riels == true ? 0 : 1].sign" />
            <Button label="Save" type="submit" :disabled="isStoringAgreement" />
        </Form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Button, DatePicker, FileUpload, InputMask, InputNumber, InputText, Message, Select, ToggleSwitch, InputGroup, InputGroupAddon } from 'primevue';
import { Form } from '@primevue/forms';
import { useToast } from "primevue/usetoast";
import { reactive, onMounted, ref, computed } from 'vue';
import PaymentSchedule from './PaymentSchedule.vue';
import Textarea from 'primevue/textarea';
import PopupAddPaymentSchedule from './PopupAddPaymentSchedule.vue';
import moment from 'moment';

//import src from 'tailwindcss-primeui';
const page = usePage();
const props = defineProps({ errors: Object, customers: Array, agreement_max: Number, agreement: Object, edit: Boolean });
const toast = useToast();
const riels = ref(true);
const currencies = ref([{ name: 'Riels', value: 'riel', sign: '៛' }, { name: 'USD', value: 'usd', sign: '$' }]);
const form = reactive({
    quotation_no: null,
    agreement_no: null,
    date: new Date(),
    customer_id: null,
    address: null,
    agreement_doc: null,
    progress: null,
    start_date: new Date(),
    end_date: new Date(),
    agreement_amount: 0,
    short_description: "",
    attachment: null,
    payment_schedule: [
        /* {
            id: 1,
            due_date: "28/01/2025",
            short_description: "Item description",
            percentage: 10,
            remark: "Additional remark",
            amount: 2000,
        },
        {
            id: 2,
            due_date: "04/02/2025",
            short_description: "Item description",
            percentage: 20,
            remark: "Additional remark",
            amount: 5000,
        },
        {
            id: 3,
            due_date: "11/02/2025",
            short_description: "Item description",
            percentage: 30,
            remark: "Additional remark",
            amount: 3000,
        }, */
    ],
    attachment: null,
});
const schedule = ref({
    agreement_amount: form.agreement_amount,
    due_date: new Date(),
    short_description: "Item description",
    percentage: 100,
    remark: "Additional remark",
    amount: 2000,
    currency: 'KHR'
});
const remainingAmount = computed(() => {
    return schedule.value.agreement_amount - form.payment_schedule.reduce((acc, item) => acc + item.amount, 0);
});
const remainingPercentage = computed(() => {
    return 100 - form.payment_schedule.reduce((acc, item) => acc + item.percentage, 0);
});
const isStoringAgreement = ref(false);
onMounted(() => {
    form.agreement_no = props.agreement_max;
    if(props.edit){
        form.quotation_no = props.agreement.quotation_no;
        form.agreement_no = props.agreement.agreement_no;
        form.date = moment(props.agreement.date).toDate();
        form.customer_id = props.agreement.customer_id;
        form.address = props.agreement.address;
        form.agreement_doc = props.agreement.agreement_doc;
        form.start_date = moment(props.agreement.start_date).toDate();
        form.end_date = moment(props.agreement.end_date).toDate();
        form.agreement_amount = props.agreement.agreement_amount;
        form.short_description = props.agreement.short_description;
        form.payment_schedule = props.agreement.payment_schedules;
        form.attachment = props.agreement.attachment;
    }
})
const submit = (e) => {
    isStoringAgreement.value = true;
    router.post(route('agreements.store'), form);
    isStoringAgreement.value = false;
    //form.post(route('agreements.store'));
    // if(e.valid){
    //     router.push(route('agreements.store'), form);
    //     toast.add({ severity: 'success', summary: 'Success', detail: 'Agreement created successfully' });
    // }else{
    //     toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill all required fields' });
    // }
}
const onUpload = (e) => {
    console.log(e);
    toast.add({ severity: 'success', summary: 'Success', detail: 'File Uploaded' + e.files[0], life: 3000 });
    form.agreement_doc = e.xhr.responseText;
    src.value = e.xhr.responseText;
}
const beforeUpload = (e) => {
    e.formData.enctype = 'multipart/form-data';
    console.log('page.props.csrf_token', form);
    e.formData.append('agreement_doc_old', form.agreement_doc);
    e.formData.append('_token', page.props.csrf_token);
}
const src = ref(null);
function onFileSelect(event) {
    const file = event.files[0];
    const reader = new FileReader();

    reader.onload = async (e) => {
        src.value = e.target.result;
    };

    reader.readAsDataURL(file);
    form.agreement_doc = file;
    const csrfToken = page.props.csrf_token;
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', csrfToken);

    fetch(route('agreements.upload'), {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toast.add({ severity: 'success', summary: 'Success', detail: 'File Uploaded', life: 3000 });
            } else {
                toast.add({ severity: 'error', summary: 'Error', detail: 'File Upload Failed', life: 3000 });
            }
        })
        .catch(error => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'File Upload Failed', life: 3000 });
        });
}
const doSave = (e) => {
    form.payment_schedule.push({
        id: form.payment_schedule.length + 1,
        due_date: e.due_date,
        short_description: e.short_description,
        percentage: e.percentage,
        remark: e.remark,
        amount: e.amount,
    });
}
const beforeUpdate = (e) => {
    schedule.value.amount = remainingAmount.value;
    schedule.value.percentage = remainingPercentage.value;
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

<style>
span.p-invalid input {
    border-color: var(--p-inputtext-invalid-border-color);
}

.p-inputnumber-input {
    width: 100%;
}
</style>