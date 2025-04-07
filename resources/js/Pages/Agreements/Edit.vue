<template>
    <Head :title="`Edit Agreement`"></Head>
    <GuestLayout>
        <NavbarLayout />
        <!-- PrimeVue Breadcrumb -->
        <div class="py-3">
            <Breadcrumb :model="items" class="border-none bg-transparent p-0">
                <template #item="{ item }">
                    <Link
                        :href="item.to"
                        class="text-sm hover:text-primary flex items-start justify-center gap-1"
                    >
                        <i v-if="item.icon" :class="item.icon"></i>
                        {{ item.label }}
                    </Link>
                </template>
            </Breadcrumb>
        </div>
        <div class="edit-agreement pl-4 pr-4">
            <Toast />
            <Form @submit="submitForm" :initial-values="form" class="mt-6">
                <div class="flex flex-row justify-between gap-4">
                    <!-- Left Column - Record Agreement -->
                    <div class="border border-gray-200 rounded-lg p-4 w-1/2">
                        <div class="grid grid-cols-2 gap-2 items-center">
                            <span class="col-span-2 text-xl font-semibold mb-5"
                                >Record Agreement</span
                            >

                            <!-- Quotation No -->
                            <span class="text-sm">Quotation No.</span>
                            <InputText
                                v-model="form.quotation_no"
                                placeholder="Enter Quotation No."
                                class="w-full"
                                size="small"
                                readonly
                            />

                            <!-- Agreement No -->
                            <span class="text-sm required">Agreement No.</span>
                            <InputNumber
                                v-model="form.agreement_no"
                                name="agreement_no"
                                :use-grouping="false"
                                :readonly="true"
                                size="small"
                            />

                            <!-- Agreement Reference No -->
                            <span class="text-sm">Agreement reference No.</span>
                            <InputText
                                v-model="form.agreement_reference_no"
                                placeholder="Enter reference number"
                                class="w-full"
                                size="small"
                                @blur="checkDuplicateReference"
                            >
                                <template #suffix>
                                    <i
                                        v-if="showDuplicateAlert"
                                        class="pi pi-exclamation-triangle text-yellow-500"
                                        v-tooltip="
                                            'This reference number already exists!'
                                        "
                                    />
                                </template>
                            </InputText>
                            <Message
                                v-if="showDuplicateAlert"
                                severity="warn"
                                variant="simple"
                                class="col-span-2"
                                size="small"
                                >This reference number already exists in our
                                records</Message
                            >

                            <!-- Agreement Date -->
                            <span class="text-sm required">Date</span>
                            <DatePicker
                                date-format="yy/mm/dd"
                                name="agreement_date"
                                v-model="form.agreement_date"
                                showIcon
                                size="small"
                            />

                            <!-- Customer Name -->
                            <span class="text-sm required">Customer name</span>
                            <InputText
                                name="customer_name"
                                v-model="customerName"
                                size="small"
                                readonly
                            />

                            <!-- Address -->
                            <span class="text-sm">Address</span>
                            <InputText
                                name="address"
                                v-model="form.address"
                                size="small"
                                readonly
                            />

                            <!-- Agreement Doc Upload Section -->
                            <span class="text-sm required">Agreement doc</span>
                            <FileUpload
                                name="agreement_doc"
                                :url="route('agreements.upload')"
                                mode="basic"
                                auto
                                accept="application/pdf"
                                multiple
                                @before-upload="beforeUploadAgreementDoc"
                                @upload="onUploadAgreementDoc"
                                chooseLabel="Upload Agreement PDF"
                                class="custom-file-upload w-full h-9"
                            >
                                <template #chooseicon>
                                    <i class="pi pi-file-pdf"></i>
                                </template>
                            </FileUpload>

                            <!-- Agreement Docs List -->
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
                                            <i
                                                class="pi pi-file-pdf mr-2 text-red-500"
                                            ></i>
                                            <a
                                                class="hover:underline hover:text-blue-600 flex items-center text-blue-500"
                                                :href="item.path"
                                                target="_blank"
                                            >
                                                {{
                                                    item.name || "document.pdf"
                                                }}
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
                    <!-- Middle Column - Agreement Summary -->
                    <div class="border border-gray-200 rounded-lg p-4 w-1/2">
                        <div class="grid grid-cols-2 gap-2 items-center">
                            <span class="col-span-2 font-semibold text-xl mb-5"
                                >Agreement summary</span
                            >

                            <!-- Start Date -->
                            <span class="text-sm">Start date</span>
                            <DatePicker
                                date-format="yy/mm/dd"
                                name="start_date"
                                v-model="form.start_date"
                                showIcon
                                size="small"
                            />

                            <!-- End Date -->
                            <span class="text-sm">End date</span>
                            <DatePicker
                                date-format="yy/mm/dd"
                                name="end_date"
                                v-model="form.end_date"
                                showIcon
                                size="small"
                            />
                            <!-- Agreement Amount -->
                            <span class="text-sm">
                                Agreement amount ({{
                                    currencies[riels ? 1 : 0].name
                                }})
                            </span>
                            <InputGroup size="small">
                                <InputGroupAddon>
                                    <ToggleSwitch v-model="riels" />
                                </InputGroupAddon>
                                <InputNumber
                                    v-model="schedule.agreement_amount"
                                    :value="schedule.agreement_amount"
                                />
                            </InputGroup>

                            <!-- Short Description -->
                            <span class="text-sm">Short description</span>
                            <Textarea
                                name="short_description"
                                rows="2"
                                v-model="form.short_description"
                                size="small"
                            ></Textarea>

                            <!-- Payment Schedule -->
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
                    <!-- Right Column - Attachments -->
                    <div class="border border-gray-200 rounded-lg p-4 w-1/3">
                        <h3 class="font-semibold text-xl mb-4">Attachments</h3>
                        <div class="space-y-4">
                            <!-- Attachment Upload Section -->
                            <span class="text-sm required">Add Attachment</span>
                            <FileUpload
                                name="attachments"
                                :url="route('agreements.upload')"
                                mode="basic"
                                auto
                                multiple
                                accept="application/pdf"
                                @before-upload="beforeUploadAttachment"
                                @upload="onUploadAttachment"
                                chooseLabel="Upload Attachments"
                                class="custom-file-upload w-full h-9"
                            >
                                <template #chooseicon>
                                    <i class="pi pi-paperclip mr-2"></i>
                                </template>
                            </FileUpload>

                            <!-- Attachments List -->
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
                                            <span
                                                class="text-xs font-medium text-gray-700 mr-3"
                                            >
                                                Attachment:{{ index + 1 }}
                                            </span>
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <i
                                                    class="pi pi-file-pdf text-red-500"
                                                ></i>
                                                <a
                                                    class="text-sm font-medium text-blue-500 hover:underline"
                                                    :href="item.path"
                                                    target="_blank"
                                                >
                                                    {{ item.name }}
                                                </a>
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
                <!-- Payment Schedule Table -->
                <PaymentSchedule
                    class="mt-2"
                    v-model="form.payment_schedule"
                    :currency="form.currency"
                    :agreement_amount="schedule.agreement_amount"
                />
                <!-- Exchange Rate (if needed) -->
                <!-- <div
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
                </div> -->
                <!-- Save Button -->
                <div class="flex justify-end gap-2 mt-10">
                    <Button
                        label="Save Changes"
                        type="submit"
                        raised
                        class="w-full md:w-40"
                        :disabled="processing"
                        icon="pi pi-check"
                    ></Button>
                    <Button
                        label="Cancel"
                        severity="secondary"
                        raised
                        class="w-full md:w-28"
                        :disabled="processing"
                        icon="pi pi-times"
                        @click="cancelChanges"
                    />
                </div>
            </Form>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import { ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import {
    Button,
    DatePicker,
    FileUpload,
    InputNumber,
    InputText,
    Message,
    ToggleSwitch,
    InputGroup,
    InputGroupAddon,
    DataView,
    Toast,
    Textarea,
} from "primevue";
import { Form } from "@primevue/forms";
import { useToast } from "primevue/usetoast";
import Breadcrumb from "primevue/breadcrumb";
import PaymentSchedule from "./PaymentSchedule.vue";
import PopupAddPaymentSchedule from "./PopupAddPaymentSchedule.vue";
import { currencies } from "@/constants";
import moment from "moment";

const toast = useToast();
const page = usePage();

const props = defineProps({
    agreement: Object,
    customers: Array,
    quotations: Array,
    errors: Object,
});
// Breadcrumb items
const items = computed(() => [
    { label: "", to: "/", icon: "pi pi-home" },
    { label: "Agreements", to: route("agreements.index") },
    {
        label: `Edit Agreement ${props.agreement.agreement_no}`,
        to: route("agreements.edit", {
            agreement_no: props.agreement.agreement_no,
        }),
    },
]);
const getFileName = (path) => {
    if (!path) return "document.pdf";
    // Handle both URL-encoded and regular paths
    const decodedPath = decodeURIComponent(path);
    // Extract the file name (handles both forward and backward slashes)
    const filename = decodedPath.split(/[\\/]/).pop();
    return filename || "document.pdf";
};
const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};
// Form setup
const form = useForm({
    quotation_no: props.agreement.quotation_no,
    agreement_no: props.agreement.agreement_no,
    agreement_reference_no: props.agreement.agreement_reference_no,
    agreement_date: props.agreement.agreement_date
        ? moment(props.agreement.agreement_date, "DD/MM/YYYY").toDate()
        : new Date(),
    customer_id: props.agreement.customer_id,
    address: props.agreement.address,
    agreement_doc: props.agreement.agreement_doc
        ? JSON.parse(props.agreement.agreement_doc)
        : null,
    start_date: props.agreement.start_date
        ? moment(props.agreement.start_date, "DD/MM/YYYY").toDate()
        : new Date(),
    end_date: props.agreement.end_date
        ? moment(props.agreement.end_date, "DD/MM/YYYY").toDate()
        : new Date(),
    agreement_amount: props.agreement.amount,
    short_description: props.agreement.short_description,
    currency: props.agreement.currency || "USD",
    attachments: props.agreement.attachments
        ? JSON.parse(props.agreement.attachments)
        : [],
    payment_schedule:
        props.agreement.payment_schedules?.map((item) => ({
            id: item.id,
            due_date: item.due_date
                ? moment(item.due_date, "DD/MM/YYYY").toDate()
                : new Date(),
            short_description: item.short_description,
            percentage: item.percentage,
            currency: item.currency,
            remark: item.remark,
            amount: item.amount,
            status: item.status || "Pending",
            agreement_currency: props.agreement.currency,
            exchange_rate:
                item.exchange_rate || (item.currency === "KHR" ? 4100 : 1),
        })) || [],
});
// Reactive state
const processing = ref(false);
const customerName = ref(props.agreement.customer?.name || "");
const showDuplicateAlert = ref(false);
const riels = computed({
    get: () => form.currency === "KHR",
    set: (value) => {
        form.currency = value ? "KHR" : "USD";
        if (form.currency === "KHR") {
            schedule.value.exchange_rate = 4100;
        } else {
            schedule.value.exchange_rate = 1;
        }
    },
});
const schedule = ref({
    agreement_amount: props.agreement.amount,
    due_date: new Date(),
    short_description: "Item description",
    percentage: 100, // Initial static value
    remark: "Additional remark",
    amount: props.agreement.amount, // Initial static value
    currency: props.agreement.currency || "USD",
    agreement_currency: props.agreement.currency || "USD",
    exchange_rate: props.agreement.currency === "KHR" ? 4100 : 1,
});
const parsedAgreementDocs = Array.isArray(props.agreement.agreement_doc)
    ? props.agreement.agreement_doc
    : JSON.parse(props.agreement.agreement_doc || "[]");

const agreementDocs = ref(parsedAgreementDocs);
form.agreement_doc = parsedAgreementDocs;

// Computed properties
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
const hasManyCurrencies = computed(() => {
    return form.payment_schedule.some((v) => v.currency != form.currency);
});

// Methods
const beforeUpload = (e) => {
    e.formData.enctype = "multipart/form-data";
    e.formData.append("agreement_doc_old", form.agreement_doc);
    e.formData.append("_token", page.props.csrf_token);
};
const beforeUploadAgreementDoc = (event) => {
    event.formData.append("_token", page.props.csrf_token); // ✅ Laravel needs this
};
const beforeUploadAttachment = (event) => {
    event.formData.append("_token", page.props.csrf_token);
};
const onUploadAgreementDoc = (e) => {
    try {
        const response = JSON.parse(e.xhr.responseText);
        const uploadedFiles = Array.isArray(response) ? response : [response];

        uploadedFiles.forEach((file) => {
            const newDoc = {
                path: file.path,
                name: file.name,
                size: file.size,
                mime_type: file.mime_type,
            };
            agreementDocs.value.push(newDoc);
        });
        form.agreement_doc = [...agreementDocs.value];

        toast.add({
            severity: "success",
            summary: "Uploaded",
            detail: `${uploadedFiles.length} agreement document(s) uploaded successfully`,
            life: 3000,
        });
    } catch (error) {
        console.error("Upload error:", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to upload agreement document(s)",
            life: 3000,
        });
    }
};

const removeAgreementDoc = (index) => {
    agreementDocs.value.splice(index, 1);
    form.agreement_doc = [...agreementDocs.value]; //
    //  form state

    toast.add({
        severity: "info",
        summary: "Removed",
        detail: "Agreement document has been removed",
        life: 3000,
    });
};

const onUploadAttachment = (e) => {
    try {
        const response = JSON.parse(e.xhr.responseText);
        form.attachments.push({
            path: response.path,
            name: response.name,
            size: response.size,
            type: response.mime_type,
        });
        toast.add({
            severity: "success",
            summary: "Uploaded",
            detail: `Attachment "${response.name}" added successfully`,
            life: 3000,
        });
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to upload attachment",
            life: 3000,
        });
    }
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
        agreement_currency: form.currency,
        exchange_rate: e.currency === "KHR" ? 4100 : 1,
    });
};

const beforeUpdate = (e) => {
    schedule.value.amount = remainingAmount.value;
    schedule.value.percentage = remainingPercentage.value;
};

const checkDuplicateReference = async () => {
    if (!form.agreement_reference_no) {
        showDuplicateAlert.value = false;
        return;
    }

    try {
        const response = await axios.get("/api/check-agreement-reference", {
            params: {
                reference_no: form.agreement_reference_no,
                exclude_id: props.agreement.id,
            },
        });

        showDuplicateAlert.value = response.data.exists;
    } catch (error) {
        console.error("Error checking reference:", error);
    }
};

const submitForm = () => {
    processing.value = true;
    // Helper function to format dates consistently
    const formatDate = (date) => {
        if (!date) return null;

        const d = date instanceof Date ? date : new Date(date);
        const day = String(d.getDate()).padStart(2, "0");
        const month = String(d.getMonth() + 1).padStart(2, "0");
        const year = d.getFullYear();
        return `${day}/${month}/${year}`;
    };
    const data = {
        ...form.data(),
        agreement_amount: schedule.value.agreement_amount,
        agreement_doc:
            agreementDocs.value.length > 0
                ? JSON.stringify(agreementDocs.value)
                : null,
        agreement_date: formatDate(form.agreement_date),
        start_date: formatDate(form.start_date),
        end_date: formatDate(form.end_date),
        payment_schedule: form.payment_schedule.map((item) => ({
            ...item,
            due_date: formatDate(item.due_date),
            amount: parseFloat(item.amount),
            percentage: parseFloat(item.percentage),
        })),
        attachments:
            form.attachments.length > 0
                ? JSON.stringify(form.attachments)
                : null,
    };

    console.log("Formatted data before submission:", data);

    form.transform(() => ({
        ...data,
        _method: "PUT",
    })).post(
        route("agreements.update", {
            agreement_no: props.agreement.agreement_no,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "Agreement updated successfully",
                    life: 3000,
                });
            },
            onError: (errors) => {
                console.error("Validation errors:", errors);
                // ...
            },
            onFinish: () => {
                processing.value = false;
            },
        }
    );
};
const cancelChanges = () => {
    router.visit(route("agreements.index"), {
        onStart: () => {
            toast.add({
                severity: "secondary",
                summary: "Cancelled",
                detail: "Changes were not saved",
                life: 3000,
            });
        },
    });
};
const formatNumber = (value, decimals = 2) => {
    if (isNaN(value)) return "0.00";
    return value.toLocaleString(undefined, {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
};
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
