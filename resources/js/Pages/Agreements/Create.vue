<template>

    <Head title="Create Agreement" />
    <GuestLayout>
        <Toast />
        <h1>{{props.edit?'Update':'Create'}} Agreement</h1>
        <Form @submit="submit" :action="route('agreements.store')" v-slot="$form" :initial-values="form">
            <div class="create-agreement flex flex-row gap-4">
                <div class="border border-gray-200 rounded-lg p-4 w-2/5">
                    <div class="grid grid-cols-2 gap-1 items-center">
                        <span v-tooltip="'must be approved and no agreement attached'">Quotation No.</span>
                        <InputNumber v-model="form.quotation_no" name="quotation_no" :use-grouping="false" class="w-full" />
                        <span>Agreement No. {{ agreement_max }}</span>
                        <InputNumber v-model="form.agreement_no" name="agreement_no" :class="(errors.agreement_no ? 'p-invalid' : '')"
                            :use-grouping="false" :readonly="true" />
                        <Message v-if="errors.agreement_no" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.agreement_no }}</Message>
                        <span>Date</span>
                        <DatePicker date-format="dd/mm/yy" v-model="form.agreement_date" name="agreement_date" showIcon />
                        <Message v-if="errors.date" severity="error" size="small" variant="simple" class="col-span-2">{{
                            errors.date }}</Message>
                        <span>Customer</span>
                        <Select filter v-model="form.customer_id" :options="customers" option-value="id"
                            option-label="name" :virtualScrollerOptions="{ itemSize: 38 }" />
                        <Message v-if="errors.customer_id" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.customer_id }}</Message>
                        <span>Address</span>
                        <InputText name="address" v-model="form.address" />
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
                        <DatePicker date-format="dd/mm/yy" name="start_date" v-model="form.start_date" showIcon />
                        <Message v-if="errors.start_date" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.start_date }}</Message>
                        <span>End date</span>
                        <DatePicker date-format="dd/mm/yy" name="end_date" v-model="form.end_date" showIcon />
                        <Message v-if="errors.end_date" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.end_date }}</Message>
                        <span>Agreement amount ({{ currencies[riels == true ? 0 : 1].name }})</span>
                        <InputGroup>
                            <InputGroupAddon>
                                <ToggleSwitch v-model="riels" />
                            </InputGroupAddon>
                            <InputNumber v-model="schedule.agreement_amount" />
                        </InputGroup>
                        <Message v-if="errors.agreement_amount" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.agreement_amount }}</Message>
                        <span>Short description</span>
                        <Textarea name="short_description" rows="2" v-model="form.short_description" ></Textarea>
                        <span>Payment schedule</span>
                        <PopupAddPaymentSchedule v-model="schedule" @save="doSave" @update="beforeUpdate"/>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-1 gap-1">
                        <span>Attachment</span>
                        <FileUpload name="attachments" :url="route('agreements.upload')" mode="basic"
                        auto accept="application/pdf" @before-upload="beforeUploadAttachment" @upload="onUploadAttachments" />
                        <Message v-if="errors.attachments" severity="error" size="small" variant="simple"
                            class="col-span-2">{{ errors.attachments }}</Message>
                        <DataView :value="form.attachments">
                            <template #list="slotProps">
                                <div class="flex flex-col">
                                    <div v-for="(item, index) in slotProps.items" :key="index">
                            <a class="underline hover:text-red-800" v-if="src" :href="src" target="_blank"><i
                                    class="pi pi-file-pdf"></i> {{ item.split('/').pop() }}</a>
                                    </div>
                                </div>
                            </template>
                        </DataView>
                    </div>
                </div>
            </div>
            <PaymentSchedule class="mt-2" v-model="form.payment_schedule" :currency="form.currency" :agreement_amount="schedule.agreement_amount" />
            <div class="flex justify-end items-center gap-2 my-2 px-24" v-if="hasManyCurrencies">
                <label for="agreement_exchange_rate" class="required">Exchange rate</label>
                <InputText id="agreement_exchange_rate" v-model="schedule.exchange_rate"></InputText>
            </div>
            <Button label="Save" type="submit" :disabled="isStoringAgreement"></Button>
        </Form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Button, DatePicker, FileUpload, InputNumber, InputText, Message, Select, ToggleSwitch, InputGroup, InputGroupAddon, DataView, Toast } from 'primevue';
import { Form } from '@primevue/forms';
import { useToast } from "primevue/usetoast";
import { reactive, onMounted, ref, computed } from 'vue';
import PaymentSchedule from './PaymentSchedule.vue';
import Textarea from 'primevue/textarea';
import PopupAddPaymentSchedule from './PopupAddPaymentSchedule.vue';
import moment from 'moment';
import { currencies } from '@/constants';

const page = usePage();
const props = defineProps({ errors: Object, customers: Array, agreement_max: Number, agreement: Object, edit: Boolean });
const toast = useToast();
const riels = computed({
    get: () => form.currency == 'KHR',
    set: (value) =>{
        form.currency = value ? 'KHR' : 'USD';
        schedule.value.agreement_currency = form.currency;
    }
});
const form = reactive({
    quotation_no: null,
    agreement_no: null,
    agreement_date: new Date(),
    customer_id: null,
    address: null,
    agreement_doc: null,
    progress: null,
    start_date: new Date(),
    end_date: new Date(),
    agreement_amount: 0,
    short_description: "",
    attachments: [],
    payment_schedule: [ ],
    attachments: null,
    currency: 'KHR'
});
const schedule = ref({
    agreement_amount: form.agreement_amount,
    due_date: new Date(),
    short_description: "Item description",
    percentage: 100,
    remark: "Additional remark",
    amount: 2000,
    currency: 'KHR',
    agreement_currency: 'KHR',
    exchange_rate: 4200,
});
const remainingAmount = computed(() => {
    return schedule.value.agreement_amount - form.payment_schedule.reduce((acc, item) => acc + item.amount, 0);
});
const remainingPercentage = computed(() => {
    return 100 - form.payment_schedule.reduce((acc, item) => acc + item.percentage, 0);
});
const isStoringAgreement = ref(false);
const hasManyCurrencies = computed(() => {
    return form.payment_schedule.some(v => v.currency != form.currency);
});
onMounted(() => {
    form.agreement_no = props.agreement_max;
    if(props.edit){
        console.log('edit', props.agreement);
        form.quotation_no = props.agreement.quotation_no;
        form.agreement_no = props.agreement.agreement_no;
        form.agreement_date = moment(props.agreement.agreement_date, 'DD/MM/YYYY').toDate();
        form.customer_id = props.agreement.customer_id;
        form.address = props.agreement.address;
        form.agreement_doc = props.agreement.agreement_doc;
        form.start_date = moment(props.agreement.start_date, 'DD/MM/YYYY').toDate();
        form.end_date = moment(props.agreement.end_date, 'DD/MM/YYYY').toDate();
        form.agreement_amount = props.agreement.amount;
        form.short_description = props.agreement.short_description;
        form.payment_schedule = props.agreement.payment_schedules.map((v, i) => {
            return {
                id: v.id,
                due_date: moment(v.due_date, 'DD/MM/YYYY').toDate(),
                short_description: v.short_description,
                percentage: v.percentage,
                currency: v.currency,
                remark: v.remark,
                amount: v.amount,
                agreement_currency: props.agreement.currency,
                exchange_rate: 4100,
                status: v.status
            }
        });
        form.attachments = JSON.parse(props.agreement.attachments??'[]');
        form.currency = props.agreement.currency;
        riels.value = (props.agreement.currency == 'KHR' ? true : false);
        schedule.value.agreement_amount = props.agreement.amount;
        src.value = props.agreement.agreement_doc;
    }
})
const submit = ({states,valid}) => {
    isStoringAgreement.value = true;
    form.agreement_amount = schedule.value.agreement_amount;
    const data = {...form, 
        agreement_date: form.agreement_date.toLocaleDateString('fr-FR'),
        start_date: form.start_date.toLocaleDateString('fr-FR'),
        end_date: form.end_date.toLocaleDateString('fr-FR'),
        payment_schedule: form.payment_schedule.map(v=>({
            ...v,
            due_date: v.due_date.toLocaleDateString('fr-FR'),
        }))
    }
    console.log('submit', data, valid,form.agreement_date);
    if(props.edit){
        form._method = 'PUT';
        router.put(route('agreements.update', props.agreement.agreement_no), data);
    }else{
        router.post(route('agreements.store'), data);
    }
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
    toast.add({ severity: 'success', summary: 'Success', detail: 'Document Uploaded ' + e.xhr.responseText, life: 3000 });
    form.agreement_doc = e.xhr.responseText;
    src.value = e.xhr.responseText;
}
const onUploadAttachments = (e) => {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Attachment Uploaded ' + e.xhr.responseText, life: 3000 });
    if(!form.attachments) form.attachments = [];
    form.attachments.push(e.xhr.responseText);
}
const beforeUpload = (e) => {
    e.formData.enctype = 'multipart/form-data';
    e.formData.append('agreement_doc_old', form.agreement_doc);
    e.formData.append('_token', page.props.csrf_token);
}
const beforeUploadAttachment = (e) => {
    e.formData.enctype = 'multipart/form-data';
    e.formData.append('_token', page.props.csrf_token);
}
const src = ref(null);
const attachments = ref([]);
const doSave = (e) => {
    schedule.value.currency = e.currency;
    form.payment_schedule.push({
        id: form.payment_schedule.length + 1,
        due_date: e.due_date,
        short_description: e.short_description,
        percentage: e.percentage,
        currency: e.currency,
        remark: e.remark,
        amount: e.amount,
        status: e.status??'Pending'
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