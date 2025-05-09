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
                <div class="flex flex-row justify-between gap-2">
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
                                v-model="form.agreement_ref_no"
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
                                v-if="errors.agreement_ref_no"
                                severity="error"
                                variant="simple"
                                class="col-span-2"
                                size="small"
                            >
                                {{ errors.agreement_ref_no }}
                            </Message>

                            <!-- Agreement Date -->
                            <span class="text-sm required">Date</span>
                            <DatePicker
                                date-format="yy/mm/dd"
                                name="agreement_date"
                                v-model="form.agreement_date"
                                showIcon
                                size="small"
                                :min-date="minDate"
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
                            <span class="text-sm required">Agreement doc</span>
                            <FileUpload
                                name="agreement_doc"
                                :url="route('agreements.upload')"
                                mode="basic"
                                auto
                                multiple
                                accept="application/pdf"
                                @before-upload="beforeUploadAgreementDoc"
                                @upload="onUploadAgreementDoc"
                                chooseLabel="Upload Agreement PDF"
                                class="custom-file-upload w-full h-9"
                            >
                                <template #chooseicon>
                                    <i class="pi pi-file-pdf"></i>
                                </template>
                            </FileUpload>
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
                                                class="text-sm font-medium text-gray-700 mr-5"
                                            >
                                                Agreement Doc {{ index + 1 }}:
                                            </span>
                                            <i
                                                class="pi pi-file-pdf mr-2 text-red-500"
                                            ></i>
                                            <a
                                                class="hover:underline text-sm hover:text-blue-600 flex items-center text-blue-500"
                                                :href="item.path"
                                                target="_blank"
                                            >
                                                {{
                                                    item.name
                                                        ? item.name.slice(
                                                              0,
                                                              15
                                                          ) +
                                                          (item.name.length > 15
                                                              ? "..."
                                                              : "")
                                                        : "document.pdf"
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
                                :min-date="minDate"
                            />
                            <!-- End Date -->
                            <span class="text-sm">End date</span>
                            <DatePicker
                                date-format="yy/mm/dd"
                                name="end_date"
                                v-model="form.end_date"
                                showIcon
                                size="small"
                                :min-date="minDate"
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
                                :start-date="form.start_date"
                                :end-date="form.end_date"
                            />
                        </div>
                    </div>
                    <!-- Right Column - Attachments -->
                    <div class="border border-gray-200 rounded-lg p-4 w-1/3">
                        <h3 class="font-semibold text-xl mb-4">
                            Add Attachments
                        </h3>
                        <div class="space-y-4">
                            <!-- Attachment Upload Section -->
                            <span class="text-sm">Attachment</span>
                            <FileUpload
                                name="attachments"
                                :url="route('agreements.upload')"
                                mode="basic"
                                auto
                                multiple
                                accept="application/pdf"
                                @before-upload="beforeUploadAttachment"
                                @upload="onUploadAttachment"
                                chooseLabel="Upload Attachment(s)"
                                class="custom-file-upload w-full h-9"
                            >
                                <template #chooseicon>
                                    <i class="pi pi-file-pdf"></i>
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
                                            <!-- Display "Attachment {{ index + 1 }}" -->
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
                                                    :href="item.path"
                                                    target="_blank"
                                                    class="text-sm font-medium text-blue-500 hover:underline"
                                                >
                                                    {{
                                                        item.name
                                                            ? item.name.slice(
                                                                  0,
                                                                  20
                                                              ) +
                                                              (item.name
                                                                  .length > 10
                                                                  ? "..."
                                                                  : "")
                                                            : "document.pdf"
                                                    }}
                                                </a>
                                            </div>
                                            <button
                                                @click="removeAttachment(index)"
                                                class="text-red-500 text-sm hover:text-red-700 p-1 rounded-full hover:bg-red-50"
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
                    :start-date="form.start_date"
                    :end-date="form.end_date"
                    :show-status="true"
                />
                <Message
                    v-if="
                        form.payment_schedule.length > 0 &&
                        !isPaymentScheduleValid
                    "
                    severity="error"
                    class="mt-2"
                >
                    Payment schedule must total exactly 100% (Current:
                    {{ totalPercentage }}%) and match agreement amount
                </Message>
                <!-- Save Button -->
                <div class="flex justify-end gap-2 mt-10">
                    <Button
                        label="Save Changes"
                        type="submit"
                        raised
                        class="w-full md:w-40"
                        :disabled="processing || !isPaymentScheduleValid"
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
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PaymentSchedule from "./PaymentSchedule.vue";
import PopupAddPaymentSchedule from "./PopupAddPaymentSchedule.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Form } from "@primevue/forms";
import { useToast } from "primevue/usetoast";
import { currencies } from "@/constants";
import moment from "moment";
import { route } from "ziggy-js";
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
    Breadcrumb,
} from "primevue";

const minDate = new Date();
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
        label: `Edit Agreement`,
        to: route("agreements.edit", {
            agreement_no: props.agreement.agreement_no,
        }),
    },
]);
const safeDate = (input) => {
    if (moment.isMoment(input)) return input.toDate();
    if (input instanceof Date) return input;
    if (typeof input === "string") {
        // Try multiple possible date formats
        const formats = [
            "YYYY/MM/DD",
            "YYYY-MM-DD",
            "DD/MM/YYYY",
            "MM/DD/YYYY",
        ];

        for (const format of formats) {
            if (moment(input, format, true).isValid()) {
                return moment(input, format).toDate();
            }
        }
    }
    return new Date(); // fallback
};
// Form setup
const form = useForm({
    quotation_no: props.agreement.quotation_no,
    agreement_no: props.agreement.agreement_no,
    agreement_ref_no: props.agreement.agreement_ref_no,
    // agreement_date: props.agreement.agreement_date
    //     ? moment(props.agreement.agreement_date, "YYYY/MM/DD").toDate()
    //     : new Date(),
    agreement_date: safeDate(props.agreement.agreement_date),
    customer_id: props.agreement.customer_id,
    address: props.agreement.address,
    agreement_doc: props.agreement.agreement_doc
        ? JSON.parse(props.agreement.agreement_doc)
        : null,
    // start_date: props.agreement.start_date
    //     ? moment(props.agreement.start_date, "YYYY/MM/DD").toDate()
    //     : new Date(),
    // end_date: props.agreement.end_date
    //     ? moment(props.agreement.end_date, "YYYY/MM/DD").toDate()
    //     : new Date(),
    start_date: safeDate(props.agreement.start_date),
    end_date: safeDate(props.agreement.end_date),
    agreement_amount: props.agreement.amount,
    short_description: props.agreement.short_description,
    currency: props.agreement.currency || "USD",
    attachments: props.agreement.attachments
        ? JSON.parse(props.agreement.attachments)
        : [],
    payment_schedule:
        props.agreement.payment_schedules?.map((item) => ({
            id: item.id,
            due_date: safeDate(item.due_date),
            short_description: item.short_description,
            // force these to be numbers
            percentage: parseFloat(item.percentage) || 0,
            amount: parseFloat(item.amount) || 0,
            currency: item.currency,
            remark: item.remark,
            status: item.status || "UPCOMING",
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
        form.payment_schedule.reduce((sum, item) => sum + item.percentage, 0)
    );
});

// Methods and event handlers
const beforeUpload = (e) => {
    e.formData.enctype = "multipart/form-data";
    e.formData.append("agreement_doc_old", form.agreement_doc);
    e.formData.append("_token", page.props.csrf_token);
};
const beforeUploadAgreementDoc = (event) => {
    event.formData.append("_token", page.props.csrf_token);
};
const beforeUploadAttachment = (event) => {
    event.formData.append("_token", page.props.csrf_token);
};
const onUploadAgreementDoc = (e) => {
    try {
        const response = JSON.parse(e.xhr.responseText);
        form.agreement_doc.push({
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

const removeAgreementDoc = (index) => {
    const removedDoc = form.agreement_doc[index];
    form.agreement_doc.splice(index, 1);
    toast.add({
        severity: "info",
        summary: "Removed",
        detail: `Document "${removedDoc.name}" has been removed`,
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
    const newTotalPercentage =
        totalPercentage.value - (e.originalPercentage || 0) + e.percentage;

    if (newTotalPercentage > 100) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: `Cannot add payment - would exceed 100% (current: ${totalPercentage.value}%)`,
            life: 3000,
        });
        return;
    }

    if (e.id) {
        // Update existing payment
        const index = form.payment_schedule.findIndex((p) => p.id === e.id);
        if (index !== -1) {
            form.payment_schedule[index] = {
                ...form.payment_schedule[index],
                ...e,
            };
        }
    } else {
        // Add new payment
        form.payment_schedule.push({
            id: form.payment_schedule.length + 1,
            due_date: e.due_date,
            short_description: e.short_description,
            percentage: e.percentage,
            currency: e.currency,
            remark: e.remark,
            amount: e.amount,
            status: e.status ?? "UPCOMING",
            agreement_currency: form.currency,
            exchange_rate: e.currency === "KHR" ? 4100 : 1,
        });
    }
};

const beforeUpdate = (e) => {
    schedule.value.amount = remainingAmount.value;
    schedule.value.percentage = remainingPercentage.value;
};

const checkDuplicateReference = async () => {
    if (!form.agreement_ref_no) {
        showDuplicateAlert.value = false;
        return;
    }

    try {
        const response = await axios.get("/api/check-agreement-reference", {
            params: {
                reference_no: form.agreement_ref_no,
                exclude_id: props.agreement.id,
            },
        });

        showDuplicateAlert.value = response.data.exists;
    } catch (error) {
        console.error("Error checking reference:", error);
    }
};
const isPastDue = (date) => {
    if (!date) return false;
    const today = moment();
    const dueDate = moment(date);
    return dueDate.isValid() && dueDate.isBefore(today, "day");
};
// sum up the percentages as numbers
const totalPercentage = computed(() => {
    return form.payment_schedule.reduce(
        (sum, item) => sum + (Number(item.percentage) || 0),
        0
    );
});

// sum up amounts (converted to the agreement currency)
const totalAmount = computed(() => {
    return form.payment_schedule.reduce((sum, item) => {
        const amt = Number(item.amount) || 0;
        // convert amt to agreement-currency if needed
        const converted =
            item.currency === form.currency
                ? amt
                : item.currency === "KHR"
                ? amt / (item.exchange_rate || 4100)
                : amt * (item.exchange_rate || 4100);
        return sum + converted;
    }, 0);
});

const isPaymentScheduleValid = computed(() => {
    // Round to 2 decimal places to avoid floating point precision issues
    const roundedPercentage = Math.round(totalPercentage.value * 100) / 100;
    const roundedAmount = Math.round(totalAmount.value * 100) / 100;
    const roundedAgreementAmount =
        Math.round(schedule.value.agreement_amount * 100) / 100;

    return (
        roundedPercentage === 100 && roundedAmount === roundedAgreementAmount
    );
});
const submitForm = () => {
    processing.value = true;
    if (!isPaymentScheduleValid.value) {
        toast.add({
            severity: "error",
            summary: "Validation Error",
            detail: "Payment schedule must total exactly 100% and match agreement amount",
            life: 3000,
        });
        return;
    }

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
        agreement_doc:
            agreementDocs.value.length > 0
                ? JSON.stringify(agreementDocs.value)
                : null,
        attachments:
            form.attachments.length > 0
                ? JSON.stringify(form.attachments)
                : null,
        agreement_date: formatDate(form.agreement_date),
        start_date: formatDate(form.start_date),
        end_date: formatDate(form.end_date),
        agreement_amount: schedule.value.agreement_amount,
        payment_schedule: form.payment_schedule.map((item) => ({
            ...item,
            // Preserve existing status if set, otherwise determine based on date
            status:
                item.status ||
                (isPastDue(item.due_date) ? "PAST DUE" : "UPCOMING"),
            due_date: formatDate(item.due_date),
            amount: parseFloat(item.amount),
            percentage: parseFloat(item.percentage),
        })),
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
