<template>
    <Head title="Create Agreement" />
    <GuestLayout>
        <NavbarLayout />
        <div class="create-agreement p-4">
            <Toast />
            <h1 class="text-2xl">
                {{ props.edit ? "Update" : "Create" }} Agreement
            </h1>
            <Form
                @submit="submit"
                :action="route('agreements.store')"
                :initial-values="form"
                class="mt-6"
            >
                <div class="create-agreement flex flex-row gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 w-2/2">
                        <div class="grid grid-cols-2 gap-2 items-center">
                            <span class="col-span-2 text-xl font-semibold mb-5"
                                >Record Agreement</span
                            >
                            <span
                                v-tooltip="
                                    'must be approved and no agreement attached'
                                "
                                class="text-sm"
                                >Quotation No.</span
                            >
                            <InputNumber
                                v-model="form.quotation_no"
                                name="quotation_no"
                                :use-grouping="false"
                                class="w-full"
                                size="small"
                            />
                            <span class="text-sm required"
                                >Agreement No. {{ agreement_max }}</span
                            >
                            <InputNumber
                                v-model="form.agreement_no"
                                name="agreement_no"
                                :class="errors.agreement_no ? 'p-invalid' : ''"
                                :use-grouping="false"
                                :readonly="true"
                                size="small"
                            />
                            <Message
                                v-if="errors.agreement_no"
                                severity="error"
                                variant="simple"
                                class="col-span-2"
                                size="small"
                                >{{ errors.agreement_no }}</Message
                            >
                            <span class="text-sm required">Date</span>
                            <DatePicker
                                date-format="dd/mm/yy"
                                name="agreement_date"
                                v-model="form.agreement_date"
                                showIcon
                                size="small"
                            />
                            <Message
                                v-if="errors.agreement_date"
                                severity="error"
                                variant="simple"
                                class="col-span-2"
                                size="small"
                                >{{ errors.agreement_date }}</Message
                            >
                            <span class="text-sm required">Customer name</span>
                            <Select
                                filter
                                v-model="form.customer_id"
                                :options="customers"
                                option-value="id"
                                option-label="name"
                                placeholder="Customer / Organization name"
                                :virtualScrollerOptions="{ itemSize: 38 }"
                                size="small"
                            >
                                <template #option="slotProps">
                                    <div class="flex items-center">
                                        <span
                                            :class="{
                                                'text-gray-400':
                                                    !slotProps.option.active,
                                            }"
                                        >
                                            {{ slotProps.option.name }}
                                        </span>
                                        <span
                                            v-if="!slotProps.option.active"
                                            class="ml-2 text-xs text-gray-500"
                                        >
                                            (Inactive)
                                        </span>
                                    </div>
                                </template>
                            </Select>
                            <Message
                                v-if="errors.customer_id"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="col-span-2"
                                >{{ errors.customer_id }}</Message
                            >
                            <span class="text-sm">Address</span>
                            <InputText
                                name="address"
                                v-model="form.address"
                                size="small"
                            />
                            <Message
                                v-if="errors.address"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="col-span-2"
                                >{{ errors.address }}</Message
                            >
                            <span class="text-sm required">Agreement doc</span>
                            <!-- <FileUpload name="agreement_doc" auto customUpload @select="onFileSelect" mode="basic" :url="route('agreements.upload')" accept="image/*" :maxFileSize="1000000" @upload="onUpload"/> -->
                            <FileUpload
                                name="agreement_doc"
                                :url="route('agreements.upload')"
                                mode="basic"
                                auto
                                accept="application/pdf"
                                @before-upload="beforeUpload"
                                @upload="onUpload"
                                class="custom-file-upload w-full h-9"
                                chooseLabel="Upload Agreement PDF"
                            >
                                <template #chooseicon>
                                    <i class="pi pi-file-pdf"></i>
                                </template>
                            </FileUpload>
                            <Message
                                v-if="errors.agreement_doc"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="col-span-2"
                                >{{ errors.agreement_doc }}</Message
                            >
                            <DataView
                                :value="agreementDocs"
                                class="col-span-2 mt-2"
                            >
                                <template #list="slotProps">
                                    <div class="grid gap-3 w-full">
                                        <div
                                            v-for="(
                                                item, index
                                            ) in slotProps.items"
                                            :key="index"
                                            class="border border-gray-200 rounded-lg p-3 flex items-center hover:bg-gray-50 transition-colors"
                                        >
                                            <span
                                                class="text-sm font-medium text-gray-700 mr-3"
                                            >
                                                Agreement Doc {{ index + 1 }}:
                                            </span>
                                            <a
                                                class="underline hover:text-blue-600 flex items-center text-blue-500"
                                                :href="item.path"
                                                target="_blank"
                                            >
                                                <i
                                                    class="pi pi-file-pdf mr-2 text-red-500"
                                                ></i>
                                                {{ item.name }}
                                            </a>
                                            <Button
                                                @click="
                                                    removeAgreementDoc(index)
                                                "
                                                icon="pi pi-times"
                                                text
                                                rounded
                                                severity="danger"
                                                class="ml-auto hover:bg-red-50"
                                                v-tooltip="'Remove document'"
                                            />
                                        </div>
                                    </div>
                                </template>
                                <template #empty>
                                    <div
                                        class="text-center p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50"
                                    >
                                        <i
                                            class="pi pi-inbox text-2xl text-gray-400 mb-2"
                                        ></i>
                                        <p class="text-gray-500">
                                            No agreement document uploaded
                                        </p>
                                    </div>
                                </template>
                            </DataView>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 w-2/2">
                        <div class="grid grid-cols-2 gap-2 items-center">
                            <span class="col-span-2 font-semibold text-xl mb-5"
                                >Agreement summary</span
                            >
                            <span class="text-sm">Start date</span>
                            <DatePicker
                                date-format="dd/mm/yy"
                                name="start_date"
                                v-model="form.start_date"
                                showIcon
                                size="small"
                            />
                            <Message
                                v-if="errors.start_date"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="col-span-2"
                                >{{ errors.start_date }}</Message
                            >
                            <span class="text-sm">End date</span>
                            <DatePicker
                                date-format="dd/mm/yy"
                                name="end_date"
                                v-model="form.end_date"
                                showIcon
                                size="small"
                            />
                            <Message
                                v-if="errors.end_date"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="col-span-2"
                                >{{ errors.end_date }}</Message
                            >
                            <span class="text-sm"
                                >Agreement amount ({{
                                    currencies[riels == true ? 0 : 1].name
                                }})</span
                            >
                            <InputGroup size="small">
                                <InputGroupAddon>
                                    <ToggleSwitch v-model="riels" />
                                </InputGroupAddon>
                                <InputNumber
                                    v-model="schedule.agreement_amount"
                                />
                            </InputGroup>
                            <Message
                                v-if="errors.agreement_amount"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="col-span-2"
                                >{{ errors.agreement_amount }}</Message
                            >
                            <span class="text-sm">Short description</span>
                            <Textarea
                                name="short_description"
                                rows="2"
                                v-model="form.short_description"
                                size="small"
                            ></Textarea>
                            <span class="text-sm">Payment schedule</span>
                            <PopupAddPaymentSchedule
                                v-model="schedule"
                                @save="doSave"
                                @update="beforeUpdate"
                                size="small"
                                class="w-full"
                            />
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 w-1/4">
                        <h3 class="font-semibold text-xl mb-4">
                            Add Attachment
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1"
                                    >Attachment</label
                                >
                                <FileUpload
                                    name="attachments"
                                    :url="route('agreements.upload')"
                                    mode="basic"
                                    auto
                                    multiple
                                    accept="application/pdf"
                                    @before-upload="beforeUploadAttachment"
                                    @upload="onUploadAttachments"
                                    class="custom-file-upload w-full"
                                    chooseLabel="Attachment"
                                >
                                    <template #chooseicon>
                                        <i class="pi pi-paperclip mr-2"></i>
                                    </template>
                                </FileUpload>
                            </div>
                            <DataView :value="form.attachments" class="w-full">
                                <template #list="slotProps">
                                    <div class="space-y-2">
                                        <div
                                            v-for="(
                                                item, index
                                            ) in slotProps.items"
                                            :key="index"
                                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <i
                                                    class="pi pi-file-pdf text-red-500"
                                                ></i>
                                                <span
                                                    class="text-sm font-medium text-gray-700"
                                                >
                                                    {{ item.name }}
                                                </span>
                                                <!-- Uncomment if you want to show file size -->
                                                <!-- <span class="text-xs text-gray-500">
                        ({{ formatFileSize(item.size) }})
                    </span> -->
                                            </div>
                                            <button
                                                @click="removeAttachment(index)"
                                                class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50"
                                                v-tooltip="'Remove attachment'"
                                            >
                                                <i class="pi pi-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template #empty>
                                    <div
                                        class="text-center p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50"
                                    >
                                        <i
                                            class="pi pi-inbox text-2xl text-gray-400 mb-2"
                                        ></i>
                                        <p class="text-sm text-gray-500">
                                            No attachments added
                                        </p>
                                    </div>
                                </template>
                            </DataView>
                        </div>
                    </div>
                </div>
                <PaymentSchedule
                    class="mt-2"
                    v-model="form.payment_schedule"
                    :currency="form.currency"
                    :agreement_amount="schedule.agreement_amount"
                />
                <div
                    class="flex justify-end items-center gap-2 my-2 px-24"
                    v-if="hasManyCurrencies"
                >
                    <label for="agreement_exchange_rate" class="required"
                        >Exchange rate</label
                    >
                    <InputText
                        id="agreement_exchange_rate"
                        v-model="schedule.exchange_rate"
                    ></InputText>
                </div>
                <Button
                    label="Save"
                    type="submit"
                    raised
                    class="w-48 mt-5"
                    :disabled="isStoringAgreement"
                    icon="pi pi-check"
                ></Button>
            </Form>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import {
    Button,
    DatePicker,
    FileUpload,
    InputNumber,
    InputText,
    Message,
    Select,
    ToggleSwitch,
    InputGroup,
    InputGroupAddon,
    DataView,
    Toast,
    Calendar,
    Dropdown,
} from "primevue";
import { Form } from "@primevue/forms";
import { useToast } from "primevue/usetoast";
import { reactive, onMounted, ref, computed } from "vue";
import PaymentSchedule from "./PaymentSchedule.vue";
import Textarea from "primevue/textarea";
import PopupAddPaymentSchedule from "./PopupAddPaymentSchedule.vue";
import moment from "moment";
import { currencies } from "@/constants";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";

const page = usePage();
const props = defineProps({
    errors: Object,
    customers: Array,
    agreement_max: Number,
    agreement: Object,
    edit: Boolean,
});
const toast = useToast();
const riels = computed({
    get: () => form.currency == "KHR",
    set: (value) => {
        form.currency = value ? "KHR" : "USD";
        schedule.value.agreement_currency = form.currency;
    },
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
    payment_schedule: [],
    attachments: null,
    currency: "KHR",
});
const schedule = ref({
    agreement_amount: form.agreement_amount,
    due_date: new Date(),
    short_description: "Item description",
    percentage: 100,
    remark: "Additional remark",
    amount: 2000,
    currency: "KHR",
    agreement_currency: "KHR",
    exchange_rate: 4200,
});

// Form data
const totalAgreement = ref(10000); // Set your default total amount
const dueDate = ref();
const shortDescription = ref("");
const percentage = ref();
const amount = ref();
const currency = ref("USD");
const locale = ref("en-US");
const agreementDocs = ref([]);
const currencyOptions = ref([
    { name: "US Dollar", code: "USD" },
    { name: "Euro", code: "EUR" },
    { name: "British Pound", code: "GBP" },
]);
const calculateAmount = () => {
    if (percentage.value) {
        amount.value = (totalAgreement.value * percentage.value) / 100;
    } else {
        amount.value = null;
    }
};

const remainingAmount = computed(() => {
    return (
        schedule.value.agreement_amount -
        form.payment_schedule.reduce((acc, item) => acc + item.amount, 0)
    );
});
const remainingPercentage = computed(() => {
    return (
        100 -
        form.payment_schedule.reduce((acc, item) => acc + item.percentage, 0)
    );
});
const isStoringAgreement = ref(false);
const hasManyCurrencies = computed(() => {
    return form.payment_schedule.some((v) => v.currency != form.currency);
});
onMounted(() => {
    form.agreement_no = props.agreement_max;
    if (props.edit) {
        console.log("edit", props.agreement);

        // Basic form data
        form.quotation_no = props.agreement.quotation_no;
        form.agreement_no = props.agreement.agreement_no;
        form.agreement_date = moment(
            props.agreement.agreement_date,
            "DD/MM/YYYY"
        ).toDate();
        form.customer_id = props.agreement.customer_id;
        form.address = props.agreement.address;
        form.start_date = moment(
            props.agreement.start_date,
            "DD/MM/YYYY"
        ).toDate();
        form.end_date = moment(props.agreement.end_date, "DD/MM/YYYY").toDate();
        form.agreement_amount = props.agreement.amount;
        form.short_description = props.agreement.short_description;
        form.currency = props.agreement.currency;

        // Initialize agreement document
        if (props.agreement.agreement_doc) {
            agreementDocs.value = [
                {
                    path: props.agreement.agreement_doc,
                    name: getFileName(props.agreement.agreement_doc),
                    size: 0, // You might want to get the actual size from your API if available
                    type: "application/pdf",
                },
            ];
            form.agreement_doc = props.agreement.agreement_doc;
        }

        // Initialize payment schedule
        form.payment_schedule = props.agreement.payment_schedules.map(
            (v, i) => {
                return {
                    id: v.id,
                    due_date: moment(v.due_date, "DD/MM/YYYY").toDate(),
                    short_description: v.short_description,
                    percentage: v.percentage,
                    currency: v.currency,
                    remark: v.remark,
                    amount: v.amount,
                    agreement_currency: props.agreement.currency,
                    exchange_rate: 4100,
                    status: v.status,
                };
            }
        );

        // Initialize attachments
        const parsedAttachments = JSON.parse(
            props.agreement.attachments ?? "[]"
        );
        form.attachments = parsedAttachments.map((att) => {
            // Handle both string paths and object attachments
            const path = typeof att === "string" ? att : att.path || att.url;
            return {
                path: path,
                name: getFileName(path),
                size: att.size || 0,
                type: att.type || "application/pdf",
            };
        });

        // Initialize other reactive values
        riels.value = props.agreement.currency == "KHR";
        schedule.value = {
            agreement_amount: props.agreement.amount,
            due_date: new Date(),
            short_description: "Item description",
            percentage: 100,
            remark: "Additional remark",
            amount: 2000,
            currency: props.agreement.currency,
            agreement_currency: props.agreement.currency,
            exchange_rate: 4100,
        };
        src.value = props.agreement.agreement_doc;
    }
});
const submit = ({ states, valid }) => {
    isStoringAgreement.value = true;
    form.agreement_amount = schedule.value.agreement_amount;
    const data = {
        ...form,
        agreement_date: form.agreement_date.toLocaleDateString("fr-FR"),
        start_date: form.start_date.toLocaleDateString("fr-FR"),
        end_date: form.end_date.toLocaleDateString("fr-FR"),
        payment_schedule: form.payment_schedule.map((v) => ({
            ...v,
            due_date: v.due_date.toLocaleDateString("fr-FR"),
        })),
    };
    console.log("submit", data, valid, form.agreement_date);
    if (props.edit) {
        form._method = "PUT";
        router.put(
            route("agreements.update", props.agreement.agreement_no),
            data
        );
    } else {
        router.post(route("agreements.store"), data);
    }
    isStoringAgreement.value = false;
    //form.post(route('agreements.store'));
    // if(e.valid){
    //     router.push(route('agreements.store'), form);
    //     toast.add({ severity: 'success', summary: 'Success', detail: 'Agreement created successfully' });
    // }else{
    //     toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill all required fields' });
    // }
};
const onUpload = (e) => {
    try {
        const response = JSON.parse(e.xhr.responseText);

        // Push to agreementDocs array instead of replacing
        agreementDocs.value.push({
            path: response.path,
            name: response.name,
            size: response.size,
            type: response.mime_type,
        });

        form.agreement_doc = response.path;

        toast.add({
            severity: "success",
            summary: "Uploaded",
            detail: `Agreement document "${response.name}" uploaded successfully`,
            life: 3000,
        });
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to upload agreement document",
            life: 3000,
        });
        console.error("Agreement doc upload error:", error);
    }
};

const removeAgreementDoc = (index) => {
    agreementDocs.value.splice(index, 1);
    if (agreementDocs.value.length === 0) {
        form.agreement_doc = null;
    }
    toast.add({
        severity: "info",
        summary: "Removed",
        detail: "Agreement document has been removed",
        life: 3000,
    });
};

const onUploadAttachments = (e) => {
    try {
        const response = JSON.parse(e.xhr.responseText);

        if (!Array.isArray(form.attachments)) {
            form.attachments = [];
        }

        form.attachments.push({
            path: response.path,
            name: response.name,
            size: response.size,
            type: response.mime_type,
        });

        toast.add({
            severity: "success",
            summary: "Uploaded",
            detail: `File "${response.name}" added successfully`,
            life: 3000,
        });
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to upload attachment",
            life: 3000,
        });
        console.error("Attachment upload error:", error);
    }
};
const getFileName = (path) => {
    if (!path) return "document.pdf";
    const filename = decodeURIComponent(path.split(/[\\/]/).pop());
    return filename || "document.pdf";
};
const removeAttachment = (index) => {
    form.attachments.splice(index, 1);
    toast.add({
        severity: "info",
        summary: "Removed",
        detail: "Attachment has been removed",
        life: 3000,
    });
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};
const beforeUpload = (e) => {
    e.formData.enctype = "multipart/form-data";
    e.formData.append("agreement_doc_old", form.agreement_doc);
    e.formData.append("_token", page.props.csrf_token);
};
const beforeUploadAttachment = (e) => {
    e.formData.enctype = "multipart/form-data";
    e.formData.append("_token", page.props.csrf_token);
};
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
        status: e.status ?? "Pending",
    });
};
const beforeUpdate = (e) => {
    schedule.value.amount = remainingAmount.value;
    schedule.value.percentage = remainingPercentage.value;
};
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
.custom-file-upload .p-button {
    width: 100%;
    justify-content: flex-start;
}
.custom-file-upload .p-button-label {
    font-size: 14px;
}
.p-dataview .p-dataview-content {
    background: transparent;
    border: none;
}
</style>
