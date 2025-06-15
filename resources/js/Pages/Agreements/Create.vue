<template>
    <Head title="Create Agreements"></Head>
    <GuestLayout>
        <NavbarLayout class="fixed top-0 z-50 w-5/6 pr-3" />
        <!-- PrimeVue Breadcrumb -->
        <div class="py-3 mt-16">
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
                <input
                    type="hidden"
                    name="_token"
                    :value="page.props.csrf_token"
                />
                <div
                    class="create-agreement flex flex-row justify-between gap-2"
                >
                    <!-- Left Column - Record Agreement -->
                    <div class="border border-gray-200 rounded-lg p-4 w-1/2">
                        <div class="grid grid-cols-2 gap-2 items-center">
                            <span class="col-span-2 text-lg font-semibold mb-5"
                                >Record Agreement</span
                            >
                            <!-- Quotation No Input with Search -->
                            <span class="text-sm">Quotation No. </span>
                            <div class="flex gap-2 text-sm">
                                <InputText
                                    v-model="form.quotation_no"
                                    placeholder="Enter Quotation No."
                                    class="w-full"
                                    size="small"
                                    @blur="searchQuotation"
                                >
                                    <template #suffix>
                                        <i
                                            v-if="form.quotation_no"
                                            class="pi pi-times cursor-pointer text-gray-500 hover:text-gray-700"
                                            @click="resetQuotationFields"
                                        />
                                        <i
                                            v-else
                                            class="pi pi-search text-gray-400"
                                        />
                                    </template>
                                </InputText>
                                <Button
                                    icon="pi pi-search"
                                    @click="searchQuotation"
                                    severity="secondary"
                                    size="small"
                                />
                            </div>
                            <small
                                v-if="quotationError"
                                class="p-error block col-span-2"
                            >
                                {{ quotationError }}
                            </small>
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
                            <span class="text-sm required">Date</span>
                            <DatePicker
                                date-format="yy-mm-dd"
                                name="agreement_date"
                                v-model="form.agreement_date"
                                showIcon
                                size="small"
                                :min-date="minDate"
                            />
                            <Message
                                v-if="errors.agreement_date"
                                severity="error"
                                variant="simple"
                                class="col-span-2"
                                size="small"
                                >{{ errors.agreement_date }}</Message
                            >
                            <span class="text-sm required">Customer</span>
                            <Dropdown
                                :filter="true"
                                v-model="form.customer_id"
                                :options="customers"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Select Customer"
                                class="w-full"
                                size="small"
                                @change="updateCustomerDetails"
                            />
                            <span class="text-sm">Address</span>
                            <Textarea
                                v-model="form.address"
                                placeholder="Display Address"
                                rows="2"
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
                                                    item.name
                                                        ? item.name.slice(
                                                              0,
                                                              20
                                                          ) +
                                                          (item.name.length > 20
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
                            <span class="col-span-2 font-semibold text-lg mb-5"
                                >Agreement summary</span
                            >
                            <span class="text-sm">Start date</span>
                            <DatePicker
                                date-format="yy-mm-dd"
                                name="start_date"
                                v-model="form.start_date"
                                showIcon
                                size="small"
                                :min-date="minDate"
                            />
                            <span class="text-sm">End date</span>
                            <DatePicker
                                date-format="yy-mm-dd"
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
                                    :minFractionDigits="2"
                                    :maxFractionDigits="2"
                                    fluid
                                    :min="0"
                                />
                            </InputGroup>
                            <span class="text-sm">Short description</span>
                            <Textarea
                                name="short_description"
                                rows="2"
                                v-model="form.short_description"
                                placeholder="Discription of the agreement"
                                size="small"
                            ></Textarea>
                            <span class="text-sm">Payment schedule</span>
                            <PopupAddPaymentSchedule
                                v-model="schedule"
                                @save="doSave"
                                @update="beforeUpdate"
                                size="small"
                                class="w-full"
                                :start_date="form.start_date"
                                :end_date="form.end_date"
                            />
                        </div>
                    </div>
                    <!-- Right Column - Attachments -->
                    <div class="border border-gray-200 rounded-lg p-4 w-1/3">
                        <h3 class="font-semibold text-lg mb-4">
                            Add Attachments
                        </h3>

                        <div class="space-y-4">
                            <span class="text-sm">Attachment</span>
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
                                    <i class="pi pi-file-pdf"></i>
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
                <PaymentSchedule
                    class="mt-2"
                    v-model="form.payment_schedule"
                    :currency="form.currency"
                    :agreement_amount="schedule.agreement_amount"
                />
                <Message
                    v-if="
                        form.payment_schedule.length > 0 &&
                        !isPaymentScheduleValid
                    "
                    severity="error"
                    class="mt-2 text-sm"
                >
                    Payment schedule must total exactly 100% (Current:
                    {{ totalPercentage }}%) and match agreement amount
                </Message>
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
                <div class="flex justify-end gap-2 mt-10 mb-10">
                    <Button
                        label="Save"
                        type="submit"
                        class="w-full md:w-28"
                        icon="pi pi-check"
                        size="small"
                        :disabled="!isPaymentScheduleValid || !isFormValid"
                        raised
                    ></Button>
                    <Button
                        label="Cancel"
                        class="w-full md:w-28"
                        @click="cancel"
                        icon="pi pi-times"
                        size="small"
                        severity="success"
                        variant="outlined"
                        raised
                    ></Button>
                </div>
            </Form>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import PaymentSchedule from "./PaymentSchedule.vue";
import PopupAddPaymentSchedule from "./PopupAddPaymentSchedule.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { Form } from "@primevue/forms";
import { useToast } from "primevue/usetoast";
import { reactive, onMounted, ref, computed, watch } from "vue";
import { currencies } from "@/constants";
import { usePage } from "@inertiajs/vue3";
import moment from "moment";
import { route } from "ziggy-js";
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
    ToggleButton,
} from "primevue";

const minDate = new Date();
const toast = useToast();
// The Breadcrumb Quotations
const page = usePage();
const userPermissions = computed(() => {
    const roles = page.props.userRoles || [];
    return {
        cancreateAgreement: roles.some((role) =>
            role.toLowerCase().includes("chef department")
        ),
    };
});
const items = computed(() => [
    {
        label: "",
        to: "/dashboard",
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
const form = reactive({
    quotation_no: null,
    agreement_no: props.edit
        ? props.agreement.agreement_no
        : Math.max(props.agreement_max, 25000001),
    agreement_date: new Date(),
    customer_id: null,
    customer_name: "",
    address: "",
    agreement_doc: [],
    progress: null,
    start_date: new Date(),
    end_date: moment(new Date()).add(30, "days").toDate(),
    agreement_amount: 0,
    short_description: "",
    attachments: [],
    payment_schedule: [],
    attachments: null,
    currency: "KHR", // Default to USD
});
const withQuotation = ref(true);
const address = ref("");
const quotationError = ref("");
const isSearching = ref(false);

const updateCustomerDetails = () => {
    const selectedCustomer = props.customers.find(
        (c) => c.id === form.customer_id
    );
    if (selectedCustomer) {
        form.customer_name = selectedCustomer.name;
        form.address = selectedCustomer.address;
    }
};

const searchQuotation = async () => {
    if (!form.quotation_no) {
        resetQuotationFields();
        return;
    }

    isSearching.value = true;
    quotationError.value = "";

    try {
        const response = await axios.get("/search-quotation", {
            params: { quotation_no: form.quotation_no },
        });

        if (response.data) {
            form.customer_name = response.data.customer_name; // Update form directly
            form.address = response.data.address;
            form.customer_id = response.data.customer_id;
            form.agreement_amount = response.data.agreement_amount;
            schedule.value.agreement_amount = response.data.agreement_amount;

            // Set currency based on quotation
            if (response.data.currency === "USD") {
                schedule.value.exchange_rate =
                    response.data.exchange_rate || 4100;
                riels.value = false;
            } else {
                schedule.value.exchange_rate = 1;
                riels.value = true;
            }
        } else {
            quotationError.value = "Quotation not found";
            resetQuotationFields();
        }
    } catch (error) {
        if (error.response && error.response.status === 404) {
            quotationError.value =
                error.response.data.error || "Quotation not found";
        } else {
            quotationError.value = "Error fetching quotation";
            console.error("Error fetching quotation", error);
        }
        resetQuotationFields();
    } finally {
        isSearching.value = false;
    }
};

const resetQuotationFields = () => {
    if (!withQuotation.value) return;

    form.quotation_no = null;
    form.customer_name = "";
    form.address = "";
    form.customer_id = null;
    form.agreement_amount = 0;
    schedule.value.agreement_amount = 0;
    form.currency = "KHR";
    riels.value = true;
    schedule.value.exchange_rate = 4100;
    quotationError.value = "";
};
watch(
    () => form.quotation_no,
    (newVal) => {
        if (!newVal) {
            resetQuotationFields();
            return;
        }
        const timer = setTimeout(() => {
            searchQuotation();
        }, 5000);

        return () => clearTimeout(timer);
    }
);

watch(
    () => form.start_date,
    (newStartDate) => {
        if (newStartDate) {
            form.end_date = moment(newStartDate).add(30, "days").toDate();
        }
    },
    { immediate: true }
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
const totalAgreement = ref(10000);
const shortDescription = ref("");
const percentage = ref();
const amount = ref();
const currency = ref("USD");
const locale = ref("en-US");
const agreementDocs = ref([]);
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
        form.agreement_ref_no = props.agreement.agreement_ref_no;
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
        form.end_date = moment(form.start_date).add(30, "days").toDate();
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
         _token: page.props.csrf_token,
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
    if (props.errors.agreement_ref_no) {
        toast.add({
            severity: "error",
            summary: "Validation Error",
            detail: "Please correct the agreement reference number",
            life: 3000,
        });
        return;
    }
    if (!isFormValid.value) {
        toast.add({
            severity: "error",
            summary: "Validation Error",
            detail: "Please complete all required fields and ensure payment schedule totals 100%",
            life: 3000,
        });
        return;
    }
    isStoringAgreement.value = true;
    //form.post(route('agreements.store'));
    // if(e.valid){
    //     router.push(route('agreements.store'), form);
    //     toast.add({ severity: 'success', summary: 'Success', detail: 'Agreement created successfully' });
    // }else{
    //     toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill all required fields' });
    // }
};
const totalPercentage = computed(() => {
    return form.payment_schedule.reduce(
        (sum, item) => sum + item.percentage,
        0
    );
});
const totalAmount = computed(() => {
    return form.payment_schedule.reduce((sum, item) => {
        const amount =
            item.currency === form.currency
                ? item.amount
                : item.currency === "KHR"
                ? item.amount / item.exchange_rate
                : item.amount * item.exchange_rate;
        return sum + amount;
    }, 0);
});
const isPaymentScheduleValid = computed(() => {
    const roundedPercentage = Math.round(totalPercentage.value * 100) / 100;
    const roundedAmount = Math.round(totalAmount.value * 100) / 100;
    const roundedAgreementAmount =
        Math.round(schedule.value.agreement_amount * 100) / 100;

    return (
        roundedPercentage === 100 && roundedAmount === roundedAgreementAmount
    );
});

const isPaymentScheduleComplete = computed(() => {
    return Math.round(totalPercentage.value * 100) / 100 === 100;
});

const isFormValid = computed(() => {
    return (
        form.agreement_no &&
        form.customer_id && // Customer is always required
        form.agreement_date &&
        form.agreement_doc?.length > 0 &&
        isPaymentScheduleComplete.value
    );
});

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
    const newTotalPercentage = totalPercentage.value + e.percentage;
    const newTotalAmount = totalAmount.value + e.amount;

    if (newTotalPercentage > 100) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: `Cannot add payment - would exceed 100% (current: ${totalPercentage.value}%)`,
            life: 3000,
        });
        return;
    }

    if (newTotalAmount > schedule.value.agreement_amount) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: `Cannot add payment - would exceed agreement amount`,
            life: 3000,
        });
        return;
    }

    schedule.value.currency = e.currency;
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
};
const beforeUpdate = (e) => {
    schedule.value.amount = remainingAmount.value;
    schedule.value.percentage = remainingPercentage.value;

    // Ensure we don't exceed 100% or agreement amount
    if (schedule.value.percentage > remainingPercentage.value) {
        schedule.value.percentage = remainingPercentage.value;
    }

    if (schedule.value.amount > remainingAmount.value) {
        schedule.value.amount = remainingAmount.value;
    }
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
    if (!form.agreement_ref_no) {
        showDuplicateAlert.value = false;
        return;
    }

    try {
        const response = await axios.get("/api/check-agreement-reference", {
            params: {
                reference_no: form.agreement_ref_no,
            },
        });

        if (response.data.exists) {
            showDuplicateAlert.value = true;
            errors.agreement_ref_no =
                "The agreement ref no has already been taken.";
        } else {
            showDuplicateAlert.value = false;
            errors.agreement_ref_no = "";
        }
    } catch (error) {
        console.error("Error checking reference:", error);
        showDuplicateAlert.value = false;
        errors.agreement_ref_no = "";
    }
};
</script>

<style>
.required:after {
    content: " *";
    color: red;
}
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
