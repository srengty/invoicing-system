<template>
    <Head :title="`Edit Agreement ${agreement.agreement_no}`"></Head>
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

                            <!-- Agreement Date -->
                            <span class="text-sm required">Date</span>
                            <DatePicker
                                date-format="dd/mm/yy"
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

                            <!-- Agreement Doc -->
                            <span class="text-sm required">Agreement doc</span>
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

                    <!-- Middle Column - Agreement Summary -->
                    <div class="border border-gray-200 rounded-lg p-4 w-1/2">
                        <div class="grid grid-cols-2 gap-2 items-center">
                            <span class="col-span-2 font-semibold text-xl mb-5"
                                >Agreement summary</span
                            >

                            <!-- Start Date -->
                            <span class="text-sm">Start date</span>
                            <DatePicker
                                date-format="dd/mm/yy"
                                name="start_date"
                                v-model="form.start_date"
                                showIcon
                                size="small"
                            />

                            <!-- End Date -->
                            <span class="text-sm">End date</span>
                            <DatePicker
                                date-format="dd/mm/yy"
                                name="end_date"
                                v-model="form.end_date"
                                showIcon
                                size="small"
                            />

                            <!-- Agreement Amount -->
                            <span class="text-sm">
                                Agreement amount ({{
                                    currencies[riels ? 0 : 1].name
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
                    <div class="border border-gray-200 rounded-lg p-4 w-1/4">
                        <h3 class="font-semibold text-xl mb-4">Attachments</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1"
                                    >Add Attachment</label
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

                <!-- Save Button -->
                <Button
                    label="Save Changes"
                    type="submit"
                    raised
                    class="w-48 mt-5"
                    :disabled="processing"
                    icon="pi pi-check"
                ></Button>
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

// Form setup
const form = useForm({
    quotation_no: props.agreement.quotation_no,
    agreement_no: props.agreement.agreement_no,
    agreement_date: props.agreement.agreement_date
        ? moment(props.agreement.agreement_date, "DD/MM/YYYY").toDate()
        : new Date(),
    customer_id: props.agreement.customer_id,
    address: props.agreement.address,
    agreement_doc: props.agreement.agreement_doc,
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
            ...item,
            due_date: item.due_date
                ? moment(item.due_date, "DD/MM/YYYY").toDate()
                : new Date(),
        })) || [],
});

// Reactive state
const processing = ref(false);
const customerName = ref(props.agreement.customer?.name || "");
const riels = computed({
    get: () => form.currency === "KHR",
    set: (value) => {
        form.currency = value ? "KHR" : "USD";
        if (form.currency === "KHR") {
            schedule.value.exchange_rate = form.exchange_rate || 4100;
        } else {
            schedule.value.exchange_rate = 1;
        }
    },
});

const schedule = ref({
    agreement_amount: props.agreement.amount,
    due_date: new Date(),
    short_description: "Item description",
    percentage: 100,
    remark: "Additional remark",
    amount: 2000,
    currency: props.agreement.currency || "USD",
    agreement_currency: props.agreement.currency || "USD",
    exchange_rate: props.agreement.currency === "KHR" ? 4100 : 1,
});

const agreementDocs = ref(
    props.agreement.agreement_doc
        ? [
              {
                  path: props.agreement.agreement_doc,
                  name: getFileName(props.agreement.agreement_doc),
                  size: 0,
                  type: "application/pdf",
              },
          ]
        : []
);

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
const getFileName = (path) => {
    if (!path) return "document.pdf";
    const filename = decodeURIComponent(path.split(/[\\/]/).pop());
    return filename || "document.pdf";
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

const onUpload = (e) => {
    try {
        const response = JSON.parse(e.xhr.responseText);
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
    });
};

const beforeUpdate = (e) => {
    schedule.value.amount = remainingAmount.value;
    schedule.value.percentage = remainingPercentage.value;
};

const submitForm = () => {
    processing.value = true;
    form.agreement_amount = schedule.value.agreement_amount;

    const data = {
        ...form,
        agreement_date: form.agreement_date
            ? moment(form.agreement_date).format("DD/MM/YYYY")
            : null,
        start_date: form.start_date
            ? moment(form.start_date).format("DD/MM/YYYY")
            : null,
        end_date: form.end_date
            ? moment(form.end_date).format("DD/MM/YYYY")
            : null,
        payment_schedule: form.payment_schedule.map((v) => ({
            ...v,
            due_date: v.due_date
                ? moment(v.due_date).format("DD/MM/YYYY")
                : null,
        })),
    };

    form.put(
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
                router.visit(route("agreements.index"));
            },
            onError: () => {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Failed to update agreement",
                    life: 3000,
                });
            },
            onFinish: () => {
                processing.value = false;
            },
        }
    );
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
