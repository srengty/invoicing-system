<template>
    <Head title="Create Agreements"></Head>
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
        <div class="create-agreement pl-4 pr-4">
            <Toast />
            <Form
                @submit="submit"
                :action="route('agreements.store')"
                :initial-values="form"
                class="mt-6"
            >
                <div
                    class="create-agreement flex flex-row justify-between gap-2"
                >
                    <div class="border border-gray-200 rounded-lg p-4 w-1/2">
                        <div class="grid grid-cols-2 gap-2 items-center">
                            <span class="col-span-2 text-xl font-semibold mb-5"
                                >Record Agreement</span
                            >
                            <!-- Quotation No Input with Search -->
                            <span
                                v-tooltip="
                                    'must be approved and no agreement attached'
                                "
                                class="text-sm"
                            >
                                Quotation No.
                            </span>
                            <InputText
                                v-model="form.quotation_no"
                                placeholder="Enter Quotation No."
                                class="w-full"
                                size="small"
                            >
                                <template #suffix>
                                    <i
                                        v-if="form.quotation_no"
                                        class="pi pi-times cursor-pointer text-gray-500 hover:text-gray-700"
                                        @click="
                                            form.quotation_no = '';
                                            form.customer_id = null;
                                            form.address = '';
                                            customerName.value = '';
                                        "
                                    />
                                    <i
                                        v-else
                                        class="pi pi-search text-gray-400"
                                    />
                                </template>
                            </InputText>
                            <!-- <span class="text-sm required"
                                >Agreement No. {{ agreement_max }}</span
                            > -->
                            <span class="text-sm required">Agreement No. </span>
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
                            <span class="text-sm required">Date</span>
                            <DatePicker
                                date-format="yy/mm/dd"
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
                            <InputText
                                name="customer_name"
                                v-model="customerName"
                                size="small"
                                readonly
                            />
                            <span class="text-sm">Address</span>
                            <InputText
                                name="address"
                                v-model="form.address"
                                size="small"
                                readonly
                            />
                            <!-- <FileUpload name="agreement_doc" auto customUpload @select="onFileSelect" mode="basic" :url="route('agreements.upload')" accept="image/*" :maxFileSize="1000000" @upload="onUpload"/> -->
                            <span class="text-sm required">Agreement doc</span>
                            <FileUpload
                                name="agreement_doc"
                                :url="route('agreements.upload')"
                                mode="basic"
                                auto
                                multiple
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
                    <div class="border border-gray-200 rounded-lg p-4 w-1/2">
                        <div class="grid grid-cols-2 gap-2 items-center">
                            <span class="col-span-2 font-semibold text-xl mb-5"
                                >Agreement summary</span
                            >
                            <span class="text-sm">Start date</span>
                            <DatePicker
                                date-format="yy/mm/dd"
                                name="start_date"
                                v-model="form.start_date"
                                showIcon
                                size="small"
                            />
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
                                    :suffix="riels ? '៛' : '$'"
                                />
                            </InputGroup>
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
                    <div class="border border-gray-200 rounded-lg p-4 w-1/3">
                        <h3 class="font-semibold text-xl mb-4">
                            Add Attachment
                        </h3>

                        <div class="space-y-4">
                            <span class="text-sm required">Attachment</span>
                            <FileUpload
                                name="attachments"
                                :url="route('agreements.upload')"
                                mode="basic"
                                auto
                                multiple
                                accept="application/pdf"
                                @before-upload="beforeUploadAttachment"
                                @upload="onUploadAttachments"
                                class="custom-file-upload w-full h-9"
                                chooseLabel="Upload Attachment(s)"
                            >
                                <template #chooseicon>
                                    <i class="pi pi-paperclip mr-2"></i>
                                </template>
                            </FileUpload>

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
                                                <!-- Link for the attachment -->
                                                <a
                                                    :href="item.path"
                                                    target="_blank"
                                                    class="text-sm font-medium text-blue-500 hover:underline"
                                                >
                                                    {{ item.name }}
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
                <PaymentSchedule
                    class="mt-2"
                    v-model="form.payment_schedule"
                    :currency="form.currency"
                    :agreement_amount="schedule.agreement_amount"
                />
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
                <div class="flex justify-end gap-2 mt-10">
                    <Button
                        label="Save"
                        type="submit"
                        raised
                        class="w-full md:w-28"
                        :disabled="isStoringAgreement"
                        icon="pi pi-check"
                        size="small"
                    ></Button>
                    <Button
                        label="Cancel"
                        severity="secondary"
                        raised
                        class="w-full md:w-28"
                        @click="cancel"
                        icon="pi pi-times"
                        size="small"
                    ></Button>
                </div>
            </Form>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { Form } from "@primevue/forms";
import { useToast } from "primevue/usetoast";
import { reactive, onMounted, ref, computed, watch } from "vue";
import { currencies } from "@/constants";
import { usePage } from "@inertiajs/vue3";
import moment from "moment";
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
    Textarea,
    Breadcrumb,
} from "primevue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import PaymentSchedule from "./PaymentSchedule.vue";
import PopupAddPaymentSchedule from "./PopupAddPaymentSchedule.vue";

const toast = useToast();
// The Breadcrumb Quotations
const page = usePage();
const items = computed(() => [
    {
        label: "",
        to: "/",
        icon: "pi pi-home",
    },
    {
        label: "Agreements",
        to: route("agreements.index"),
    },
    {
        label: "Create Agreements",
        to: route("agreements.create"),
    },
]);
const getFileName = (path) => {
    if (!path) return "document.pdf";
    const filename = decodeURIComponent(path.split(/[\\/]/).pop());
    return filename || "document.pdf";
};
const isEditing = computed(() => !!form.id);
const props = defineProps({
    errors: Object,
    customers: Array,
    agreement_max: Number,
    agreement: Object,
    edit: Boolean,
});
const riels = computed({
    get: () => form.currency === "KHR",
    set: (value) => {
        form.currency = value ? "KHR" : "USD";
        schedule.value.exchange_rate = value ? 4100 : 1;
    },
});
const updateAgreementAmount = () => {
    if (form.currency === "USD") {
        schedule.value.agreement_amount = form.agreement_amount;
    } else if (form.currency === "KHR") {
        schedule.value.agreement_amount =
            form.agreement_amount * schedule.value.exchange_rate;
    }
};
const form = reactive({
    quotation_no: null,
    agreement_no: props.edit
        ? props.agreement.agreement_no
        : Math.max(props.agreement_max, 25000001),
    agreement_date: new Date(),
    customer_id: null,
    address: "",
    agreement_doc: [],
    progress: null,
    start_date: new Date(),
    end_date: new Date(),
    agreement_amount: 0,
    short_description: "",
    attachments: [],
    payment_schedule: [],
    attachments: null,
    currency: "KHR", // Default to USD
});

const customerName = ref("");
const address = ref("");
const searchQuotation = async () => {
    if (form.quotation_no) {
        // console.log("Searching for quotation no:", form.quotation_no);
        try {
            const response = await axios.get("/search-quotation", {
                params: { quotation_no: form.quotation_no },
            });

            if (response.data) {
                customerName.value = response.data.customer_name;
                form.address = response.data.address;
                form.customer_id = response.data.customer_id;
                form.agreement_amount = response.data.agreement_amount;
                schedule.value.agreement_amount =
                    response.data.agreement_amount;
                if (response.data.currency === "USD") {
                    schedule.value.exchange_rate =
                        response.data.exchange_rate || 4100;
                    riels.value = false;
                } else {
                    schedule.value.exchange_rate = 1;
                    riels.value = true;
                }
            } else {
                customerName.value = "";
                form.address = "";
                form.customer_id = null;
                form.agreement_amount = 0;
                schedule.value.agreement_amount = 0;
                schedule.value.exchange_rate = 1;
            }
        } catch (error) {
            console.error("Error fetching quotation", error);
        }
    }
};
watch(
    () => form.quotation_no,
    (newVal) => {
        const timer = setTimeout(() => {
            searchQuotation();
        }, 500);

        return () => clearTimeout(timer);
    }
);
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
    // form.agreement_no = props.agreement_max > 25000001 ? props.agreement_max : 25000001;
    // form.agreement_no = props.edit ? props.agreement.agreement_no : 25000001;
    form.agreement_no = props.edit
        ? props.agreement.agreement_no
        : Math.max(props.agreement_max, 25000001);
    if (props.edit) {
        console.log("edit", props.agreement);

        // Basic form data
        form.quotation_no = props.agreement.quotation_no;
        form.agreement_no = props.agreement.agreement_no;
        form.agreement_reference_no = props.agreement.agreement_reference_no;
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
        form.payment_schedule = props.agreement.payment_schedules?.map(
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
        form.attachments = parsedAttachments?.map((att) => {
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
        agreement_date: form.agreement_date
            ? form.agreement_date.toLocaleDateString("fr-FR")
            : null,
        start_date: form.start_date
            ? form.start_date.toLocaleDateString("fr-FR")
            : null,
        end_date: form.end_date
            ? form.end_date.toLocaleDateString("fr-FR")
            : null,
        payment_schedule: form.payment_schedule.map((v) => ({
            ...v,
            due_date: v.due_date
                ? v.due_date.toLocaleDateString("fr-FR")
                : null,
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
        router.post(route("agreements.store"), data, {
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "Agreement created successfully",
                    life: 3000,
                });
            },
            onError: () => {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Failed to create agreement",
                    life: 3000,
                });
            },
        });
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
const cancel = () => {
    toast.add({
        severity: "secondary",
        summary: "Cancelled",
        detail: "Agreement creation cancelled",
        life: 3000,
    });
    setTimeout(() => {
        router.visit(route("agreements.index"));
    }, 500);
};
const onUpload = (e) => {
    try {
        const response = JSON.parse(e.xhr.responseText);
        const uploadedFiles = Array.isArray(response) ? response : [response];

        if (!Array.isArray(form.agreement_doc)) {
            form.agreement_doc = [];
        }

        uploadedFiles.forEach((file) => {
            form.agreement_doc.push({
                path: file.path,
                name: file.name,
                size: file.size,
            });
        });

        // Update UI
        agreementDocs.value = form.agreement_doc;

        toast.add({
            severity: "success",
            summary: "Uploaded",
            detail: `${uploadedFiles.length} agreement doc(s) uploaded successfully`,
            life: 3000,
        });
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to upload agreement documents",
            life: 3000,
        });
        console.error("Agreement doc upload error:", error);
    }
};
const removeAgreementDoc = (index) => {
    form.agreement_doc.splice(index, 1);
    agreementDocs.value.splice(index, 1);

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
        const uploadedFiles = Array.isArray(response) ? response : [response];

        if (!Array.isArray(form.attachments)) {
            form.attachments = [];
        }

        uploadedFiles.forEach((file) => {
            form.attachments.push({
                path: file.path,
                name: file.name,
                size: file.size,
            });
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
        console.error("Attachment upload error:", error);
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
const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};
const beforeUpload = (e) => {
    e.formData.append("agreement_doc_old", form.agreement_doc?.path || "");
    e.formData.append("_token", page.props.csrf_token); // CSRF token is essential
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
const showDuplicateAlert = ref(false);
const checkDuplicateReference = async () => {
    if (!form.agreement_reference_no) {
        showDuplicateAlert.value = false;
        return;
    }

    try {
        const response = await axios.get("/api/check-agreement-reference", {
            params: {
                reference_no: form.agreement_reference_no,
            },
        });

        showDuplicateAlert.value = response.data.exists;
    } catch (error) {
        console.error("Error checking reference:", error);
    }
};
const formatDate = (date, format = "YYYY-DD-MM") => {
    if (!date) return "N/A";
    return moment(date).format(format);
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
