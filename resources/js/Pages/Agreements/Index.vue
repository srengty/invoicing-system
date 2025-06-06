<template>
    <Head title="Agreements"></Head>
    <GuestLayout>
        <NavbarLayout class="fixed top-0 z-50 w-5/6" />
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
        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />
        <!-- Due Soon/Past Due Payments -->
        <!-- <div
            v-if="hasDueSoonOrPastDuePaymentsAlter"
            class="mb-4 p-4 border-round"
            :class="
                alterHasPastDuePayments
                    ? 'bg-red-100 border-red-300 border-1'
                    : 'bg-orange-100 border-orange-300 border-1'
            "
        >
            <div class="flex align-items-center gap-3">
                <i
                    class="pi pi-exclamation-triangle text-xl"
                    :class="
                        alterHasPastDuePayments
                            ? 'text-red-500'
                            : 'text-orange-500'
                    "
                ></i>
                <span v-if="alterHasPastDuePayments" class="text-red-800">
                    You have {{ pastDuePaymentsCount }} past due payment(s).
                    Please generate invoices immediately!
                </span>
                <span v-else class="text-orange-800">
                    You have {{ dueSoonPaymentsCount }} payment(s) due soon
                    (within 13 days). Please generate invoices to avoid delays.
                </span>
            </div>
        </div> -->
        <div class="agreements pl-4 pr-4">
            <div class="flex justify-end items-center">
                <div class="flex items-center gap-2">
                    <Dropdown
                        v-model="searchType"
                        :options="searchOptions"
                        optionLabel="label"
                        optionValue="value"
                        class="w-48 h-9 text-sm"
                        size="small"
                        placeholder="Select field to search"
                    />
                    <InputText
                        v-model="searchTerm"
                        placeholder="Search"
                        class="w-64 h-9"
                        size="small"
                    />
                    <!-- Start Date Filter -->
                    <Calendar
                        v-if="searchType === 'start_date'"
                        v-model="startDateFilter"
                        dateFormat="yy-mm-dd"
                        showIcon
                        class="w-40 h-9 text-xs"
                        size="small"
                    />
                    <!-- End Date Filter -->
                    <Calendar
                        v-if="searchType === 'end_date'"
                        v-model="endDateFilter"
                        dateFormat="yy-mm-dd"
                        showIcon
                        class="w-40 h-9 text-xs"
                        size="small"
                    />
                    <Button
                        label="Clear"
                        @click="clearFilters"
                        class="p-button-secondary w-24"
                        icon="pi pi-times"
                        size="small"
                        severity="success"
                        variant="outlined"
                    />
                    <Button
                        v-if="userPermissions.canCreateAgreements"
                        icon="pi pi-plus"
                        label="Issue Agreement"
                        @click="openCreate"
                        size="small"
                        raised
                    ></Button>
                    <Link :href="route('quotations.create')"
                        ><Button
                            v-if="userPermissions.canCreateQuotations"
                            icon="pi pi-plus"
                            label="Issue Quotation"
                            size="small"
                            raised
                    /></Link>
                    <!-- <ChooseColumns
                        :columns="columns"
                        v-model="selectedColumns"
                        @apply="updateColumns"
                        size="small"
                        raised
                    /> -->
                </div>
            </div>

            <DataTable
                :value="filteredAgreements"
                :rowStyle="rowStyle"
                scrollable
                scrollHeight="flex"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :stripedRows="true"
                :showGridlines="true"
                resizableColumns
                columnResizeMode="fit"
                class="mt-10 border-t-2"
            >
                <template v-for="col of showColumns" :key="col.field">
                    <Column
                        v-if="
                            ![
                                'actions',
                                'customer.name',
                                'agreement_doc',
                                'agreement_date',
                                'start_date',
                                'end_date',
                                'status',
                                'amount',
                                'due_payment',
                                'total_progress_payment',
                                'payment_schedules',
                                'total_progress_payment_percentage',
                            ].includes(col.field)
                        "
                        :field="col.field"
                        :header="col.header"
                        :sortable="col.sortable"
                        style="width: 5%; font-size: 12px"
                    ></Column>
                    <!-- column for agreement date -->
                    <Column
                        v-if="['agreement_date'].includes(col.field)"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data[col.field]) }}
                        </template>
                    </Column>
                    <!-- column for agreement doc -->
                    <Column
                        v-if="col.field === 'agreement_doc'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <a
                                v-if="slotProps.data[col.field]"
                                :href="slotProps.data[col.field]"
                                target="_blank"
                                >View doc</a
                            >
                        </template>
                    </Column>
                    <!-- column for customer name -->
                    <Column
                        v-if="col.field === 'customer.name'"
                        :field="col.field"
                        :header="col.header"
                        style="width: 5%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <div class="flex flex-col">
                                <div class="flex items-center">
                                    <span
                                        :class="{
                                            'text-red-500 font-bold':
                                                hasDueSoonOrPastDuePayments(
                                                    slotProps.data
                                                ),
                                        }"
                                    >
                                        {{ slotProps.data.customer?.name }}
                                    </span>
                                </div>
                                <!-- Show days until due for DUE SOON payments -->
                                <div
                                    v-if="hasDueSoonPayments(slotProps.data)"
                                    class="text-[10px] text-orange-500 mt-1 font-semibold"
                                >
                                    Due in
                                    {{ getNearestDueDays(slotProps.data) }} days
                                </div>
                                <!-- Show overdue days for PAST DUE payments -->
                                <div
                                    v-if="hasPastDuePayments(slotProps.data)"
                                    class="text-[10px] text-red-500 mt-1 font-semibold"
                                >
                                    Overdue by
                                    {{ getOverdueDays(slotProps.data) }} days
                                </div>
                            </div>
                        </template>
                    </Column>

                    <!-- column for status -->
                    <Column
                        v-if="col.field === 'status'"
                        :field="col.field"
                        :header="col.header"
                        style="width: 5%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <div class="flex flex-col">
                                <Tag
                                    :value="
                                        getStatusLabel(slotProps.data.status)
                                    "
                                    :severity="
                                        getStatusSeverity(slotProps.data.status)
                                    "
                                    style="
                                        text-transform: uppercase;
                                        font-size: 12px;
                                        font-weight: 600;
                                    "
                                />
                                <span
                                    v-if="isExpiringSoon(slotProps.data)"
                                    class="ml-2 text-yellow-600 font-semibold text-[10px]"
                                >
                                    (Expires in
                                    {{ daysUntilExpiration(slotProps.data) }}
                                    days)
                                </span>
                            </div>
                        </template>
                    </Column>
                    <!-- column for Total Amount -->
                    <Column
                        v-if="col.field === 'amount'"
                        :field="col.field"
                        :header="col.header"
                        style="width: 5%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data[col.field]) }}
                            <span class="text-xs text-gray-500 ml-1">
                                ({{ slotProps.data.currency }})
                            </span>
                        </template>
                    </Column>
                    <!-- Update the due_payment column to show the calculated due payment -->
                    <Column
                        v-if="col.field === 'due_payment'"
                        :field="col.field"
                        :header="col.header"
                        style="width: 5%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <span>
                                {{ formatCurrency(slotProps.data.due_payment) }}
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ slotProps.data.currency }})
                                </span>
                                <Tag
                                    v-if="slotProps.data.due_payment > 0"
                                    value="PAST DUE"
                                    severity="danger"
                                    class="ml-2"
                                />
                            </span>
                        </template>
                    </Column>
                    <!-- Update the total_progress_payment column to show the sum of all receipts -->
                    <Column
                        v-if="col.field === 'payment_schedules'"
                        :field="col.field"
                        :header="col.header"
                        style="width: 5%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <div class="flex items-center">
                                <span
                                    class="hover:text-primary hover:underline cursor-pointer transition-all duration-200"
                                    @click="
                                        showProgressPayments(slotProps.data)
                                    "
                                >
                                    {{
                                        formatCurrency(
                                            getTotalPaidAmount(
                                                slotProps.data.payment_schedules
                                            )
                                        )
                                    }}
                                </span>
                                <Button
                                    v-if="
                                        (slotProps.data.payment_schedules || [])
                                            .length
                                    "
                                    icon="pi pi-info-circle"
                                    @click="
                                        showProgressPayments(slotProps.data)
                                    "
                                    severity="secondary"
                                    size="small"
                                    text
                                    rounded
                                    class="ml-2"
                                    v-tooltip.top="'View all payment schedules'"
                                />
                            </div>
                        </template>
                    </Column>

                    <!-- Update the total_progress_payment_percentage column to show the percentage -->
                    <Column
                        v-if="col.field === 'total_progress_payment_percentage'"
                        :field="col.field"
                        :header="col.header"
                        style="width: 5%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <div class="progress-bar-wrapper flex items-center">
                                <ProgressBar
                                    :value="
                                        slotProps.data
                                            .total_progress_payment_percentage ||
                                        0
                                    "
                                    :showValue="false"
                                    :class="
                                        progressBarClass(
                                            slotProps.data
                                                .total_progress_payment_percentage ||
                                                0
                                        )
                                    "
                                    style="
                                        flex-grow: 1;
                                        border: gray 1px solid;
                                        border-radius: 5px;
                                        background-color: white;
                                    "
                                />
                                <span class="progress-bar-text ml-2">
                                    {{
                                        (
                                            slotProps.data
                                                .total_progress_payment_percentage ||
                                            0
                                        ).toFixed(0)
                                    }}%
                                </span>
                            </div>
                        </template>
                    </Column>
                    <!-- Start Date Column -->
                    <Column
                        v-if="col.field === 'start_date'"
                        :field="col.field"
                        :header="col.header"
                        style="width: 5%; font-size: 12px"
                    >
                        <template>
                            <div class="flex flex-col gap-1">
                                <span class="font-medium">Start Date</span>
                                <Calendar
                                    v-model="startDateFilter"
                                    dateFormat="yy-mm-dd"
                                    showIcon
                                    class="w-full text-xs"
                                />
                            </div>
                        </template>
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.start_date) }}
                        </template>
                    </Column>
                    <!-- End Date Column -->
                    <Column
                        v-else-if="col.field === 'end_date'"
                        :field="col.field"
                        :header="col.header"
                        style="width: 5%; font-size: 12px"
                    >
                        <template>
                            <div class="flex flex-col gap-1">
                                <span class="font-medium">End Date</span>
                                <Calendar
                                    v-model="endDateFilter"
                                    dateFormat="yy-mm-dd"
                                    showIcon
                                    class="w-full text-xs"
                                />
                            </div>
                        </template>
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.end_date) }}
                        </template>
                    </Column>
                    <!-- column for view/edit -->
                    <Column
                        v-if="col.field === 'actions'"
                        :field="col.field"
                        :header="col.header"
                        frozen
                        alignFrozen="right"
                        style="width: 5%; font-size: 12px; z-index: 2"
                    >
                        <template #body="slotProps">
                            <Button
                                severity=""
                                size="small"
                                icon="pi pi-eye"
                                outlined
                                class="mr-2"
                                style="width: 30px; height: 30px"
                                :disabled="slotProps.data.status === 'Closed'"
                                @click="viewAgreementDetails(slotProps.data)"
                            />
                            <Button
                                v-if="userPermissions.canEditAgreements"
                                severity="info"
                                size="small"
                                icon="pi pi-pencil"
                                outlined
                                style="width: 30px; height: 30px"
                                :disabled="slotProps.data.status === 'Closed'"
                                @click="
                                    router.get(
                                        route('agreements.edit', {
                                            agreement_no:
                                                slotProps.data.agreement_no,
                                        })
                                    )
                                "
                            />

                            <!-- <Button
                                severity=""
                                size="small"
                                @click="
                                    router.get(
                                        route('agreements.print', {
                                            id: slotProps.data.agreement_no,
                                        })
                                    )
                                "
                                icon="pi pi-print"
                                aria-label="print"
                                outlined
                                class="mr-2"
                            /> -->
                        </template>
                    </Column>
                </template>
            </DataTable>

            <!-- Total Progress Payment Dialog (View) -->
            <Dialog
                v-model:visible="progressPaymentsDialog"
                :style="{ width: '45vw' }"
                header="Progress Payment Details"
                modal
                dismissableMask
                class="text-sm"
                :draggable="false"
                :resizable="false"
                position="center"
                :closeOnEscape="false"
            >
                <div class="grid">
                    <div class="col-12">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Agreement No:</label
                                    >
                                    <div class="text-sm">
                                        {{
                                            selectedAgreement?.agreement_no ||
                                            "N/A"
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Customer:</label
                                    >
                                    <div>
                                        {{
                                            selectedAgreement?.customer?.name ||
                                            "N/A"
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Total Amount:</label
                                    >
                                    <div>
                                        {{
                                            formatCurrency(
                                                selectedAgreement?.amount || 0
                                            )
                                        }}
                                        ({{
                                            selectedAgreement?.currency || "-"
                                        }})
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex gap-2">
                                    <label class="font-semibold">Status:</label>
                                    <Tag
                                        :value="
                                            getStatusLabel(
                                                selectedAgreement?.status
                                            )
                                        "
                                        :severity="
                                            getStatusSeverity(
                                                selectedAgreement?.status
                                            )
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <Divider class="my-4" />
                <div class="text-md font-bold mb-3">Progress Payments</div>
                <DataTable
                    :value="paidProgressPayments"
                    :paginator="true"
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20]"
                    :stripedRows="true"
                    :showGridlines="true"
                    class="mt-3 text-sm"
                    size="small"
                >
                    <Column
                        field="id"
                        header="No."
                        style="width: 5%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column
                        field="due_date"
                        header="Due Date"
                        style="width: 15%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <span
                                :class="{
                                    'text-red-500':
                                        getPaymentStatus(slotProps.data) ===
                                            'DUE SOON' ||
                                        getPaymentStatus(slotProps.data) ===
                                            'PAST DUE',
                                }"
                            >
                                {{ formatDate(slotProps.data.due_date) }}
                            </span>
                        </template>
                    </Column>
                    <Column
                        field="short_description"
                        header="Description"
                        style="width: 15%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            {{ slotProps.data.short_description || "N/A" }}
                        </template>
                    </Column>
                    <Column
                        field="percentage"
                        header="Percentage"
                        style="width: 15%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            {{ Math.trunc(slotProps.data.percentage) }}%
                        </template>
                    </Column>
                    <Column
                        field="amount"
                        header="Amount"
                        style="width: 15%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.amount) }} ({{
                                slotProps.data.currency
                            }})
                        </template>
                    </Column>
                    <Column header="Status" style="width: 10%; font-size: 12px">
                        <template #body="slotProps">
                            <Tag
                                :value="getPaymentStatus(slotProps.data)"
                                :severity="
                                    getStatusSeverityPayment(slotProps.data)
                                "
                                class="text-transform: uppercase"
                            />
                        </template>
                    </Column>
                </DataTable>
                <template #footer>
                    <Button
                        label="Close"
                        icon="pi pi-times"
                        @click="progressPaymentsDialog = false"
                        class="p-button-text"
                    />
                </template>
            </Dialog>

            <!-- Agreement Details Dialog (View)-->
            <Dialog
                v-model:visible="agreementDetailsDialog"
                :style="{ width: '45vw' }"
                header="Agreement Details"
                :modal="true"
                :dismissableMask="true"
                class="text-sm"
                :draggable="false"
                :resizable="false"
                :position="'center'"
                :closeOnEscape="false"
            >
                <div class="grid">
                    <div class="col-12">
                        <!-- Agreement Information Section -->
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold text-sm"
                                        >Agreement No:</label
                                    >
                                    <div class="text-xs">
                                        {{
                                            selectedAgreementDetails?.agreement_no
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold text-sm"
                                        >Quotation No:</label
                                    >
                                    <div class="text-xs">
                                        {{
                                            selectedAgreementDetails?.quotation_no ||
                                            "N/A"
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold text-sm"
                                        >Agreement Ref No:</label
                                    >
                                    <div class="text-xs">
                                        {{
                                            selectedAgreementDetails?.agreement_ref_no ||
                                            "N/A"
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold text-sm"
                                        >Customer:</label
                                    >
                                    <div class="text-xs">
                                        {{
                                            selectedAgreementDetails?.customer
                                                ?.name
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold text-sm"
                                        >Total Amount:</label
                                    >
                                    <div class="text-xs">
                                        {{
                                            formatCurrency(
                                                selectedAgreementDetails?.amount
                                            )
                                        }}
                                        ({{
                                            selectedAgreementDetails?.currency
                                        }})
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold text-sm"
                                        >Agreement Date:</label
                                    >
                                    <div class="text-xs">
                                        {{
                                            formatDate(
                                                selectedAgreementDetails?.agreement_date
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold text-sm"
                                        >Start Date:</label
                                    >
                                    <div class="text-xs">
                                        {{
                                            formatDate(
                                                selectedAgreementDetails?.start_date
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold tetxt-sm"
                                        >End Date:</label
                                    >
                                    <div class="text-xs">
                                        {{
                                            formatDate(
                                                selectedAgreementDetails?.end_date
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-8">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold text-sm"
                                        >Description:</label
                                    >
                                    <div class="text-xs">
                                        {{
                                            selectedAgreementDetails?.short_description ||
                                            "N/A"
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex items-center gap-2">
                                    <label class="font-semibold text-sm"
                                        >Status:</label
                                    >
                                    <Tag
                                        :value="
                                            getStatusLabel(
                                                selectedAgreementDetails?.status
                                            )
                                        "
                                        :severity="
                                            getStatusSeverity(
                                                selectedAgreementDetails?.status
                                            )
                                        "
                                    />
                                </div>
                            </div>
                            <!-- Agreement Documents -->
                            <div class="col-12">
                                <div class="field">
                                    <label
                                        class="font-semibold block mb-1 text-sm"
                                        >Agreement Documents:</label
                                    >
                                    <ul class="ml-1 space-y-1">
                                        <li
                                            v-for="(
                                                doc, index
                                            ) in selectedAgreementDetails?.agreement_doc"
                                            :key="'doc-' + index"
                                            class="flex items-center gap-2"
                                        >
                                            <i
                                                class="pi pi-file-pdf text-red-500"
                                            ></i>
                                            <a
                                                :href="doc.path"
                                                target="_blank"
                                                class="text-blue-600 hover:underline"
                                            >
                                                {{
                                                    doc.name ||
                                                    `Document ${index + 1}`
                                                }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Attachments -->
                            <div class="col-12">
                                <div class="field">
                                    <label
                                        class="font-semibold block mb-1 text-sm"
                                        >Attachments:</label
                                    >
                                    <ul class="ml-1 space-y-1">
                                        <li
                                            v-for="(
                                                file, index
                                            ) in selectedAgreementDetails?.attachments"
                                            :key="'attach-' + index"
                                            class="flex items-center gap-2"
                                        >
                                            <i
                                                class="pi pi-file-pdf text-red-500"
                                            ></i>
                                            <a
                                                :href="file.path"
                                                target="_blank"
                                                class="text-blue-600 hover:underline"
                                            >
                                                {{
                                                    file.name ||
                                                    `Attachment ${index + 1}`
                                                }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <Divider class="my-4" />
                        <!-- Payment Schedule Section -->
                        <div class="text-md font-bold mb-3">
                            Payment Schedule
                        </div>
                        <DataTable
                            :value="
                                selectedAgreementDetails?.payment_schedules ||
                                []
                            "
                            :rowClass="paymentScheduleRowClass"
                            :paginator="true"
                            :rows="5"
                            :rowsPerPageOptions="[5, 10, 20]"
                            :stripedRows="true"
                            :showGridlines="true"
                            class="mt-3"
                            size="small"
                        >
                            <Column
                                field="id"
                                header="No"
                                style="width: 5%; font-size: 12px"
                            >
                                <template #body="slotProps">
                                    {{ slotProps.index + 1 }}
                                </template>
                            </Column>
                            <Column
                                field="due_date"
                                header="Due Date"
                                style="width: 15%; font-size: 12px"
                            >
                                <template #body="slotProps">
                                    <span
                                        :class="{
                                            'text-red-500':
                                                getPaymentStatus(
                                                    slotProps.data
                                                ) === 'DUE SOON' ||
                                                getPaymentStatus(
                                                    slotProps.data
                                                ) === 'PAST DUE',
                                        }"
                                    >
                                        {{
                                            formatDate(slotProps.data.due_date)
                                        }}
                                    </span>
                                </template>
                                <template #editor="{ data, field }">
                                    <DatePicker
                                        v-model="data[field]"
                                        fluid
                                        date-format="yy-mm-dd"
                                    />
                                </template>
                            </Column>
                            <Column
                                field="short_description"
                                header="Description"
                                style="width: 15%; font-size: 12px"
                            >
                                <template #body="slotProps">
                                    {{
                                        slotProps.data.short_description ||
                                        "N/A"
                                    }}
                                </template>
                            </Column>
                            <Column
                                field="percentage"
                                header="Percentage"
                                style="width: 5%; font-size: 12px"
                            >
                                <template #body="slotProps">
                                    {{ Math.trunc(slotProps.data.percentage) }}%
                                </template>
                            </Column>
                            <Column
                                field="amount"
                                header="Amount"
                                style="width: 15%; font-size: 12px"
                            >
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.amount) }}
                                    ({{ slotProps.data.currency }})
                                </template>
                            </Column>
                            <Column
                                header="Status"
                                style="width: 15%; font-size: 12px"
                            >
                                <template #body="slotProps">
                                    <Tag
                                        :value="
                                            getPaymentStatus(slotProps.data)
                                        "
                                        :severity="
                                            getStatusSeverityPayment(
                                                slotProps.data
                                            )
                                        "
                                        class="text-transform: uppercase"
                                    />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
                <template #footer>
                    <Button
                        label="Close"
                        icon="pi pi-times"
                        @click="agreementDetailsDialog = false"
                        class="p-button-text"
                    />
                </template>
            </Dialog>
        </div>
    </GuestLayout>
</template>

<script setup>
import ChooseColumns from "@/Components/ChooseColumns.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import PaymentSchedule from "./PaymentSchedule.vue";
import { ref, watch, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import { usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { route } from "ziggy-js";
import moment from "moment";
import axios from "axios";
import {
    DataTable,
    Column,
    Button,
    Popover,
    Dropdown,
    InputText,
    Dialog,
    ProgressBar,
    Tag,
    Divider,
    Breadcrumb,
    Badge,
    ProgressSpinner,
    Card,
    Calendar,
    DatePicker,
} from "primevue";

const toast = useToast();
const props = defineProps({
    agreements: {
        type: Array,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});
// The Breadcrumb Quotations
const page = usePage();
const userPermissions = computed(() => {
    const roles = page.props.userRoles || [];
    return {
        canCreateAgreements: roles.some((role) =>
            role.toLowerCase().includes("chef department")
        ),
        canEditAgreements: roles.some((role) =>
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
    { label: page.props.title || "Agreements", to: route("agreements.index") },
]);
const columns = [
    { field: "quotation_no", header: "Quotation No." },
    { field: "agreement_no", header: "Agreement No." },
    { field: "agreement_ref_no", header: "Agreement Ref No." },
    { field: "customer.name", header: "Customer" },
    { field: "status", header: "Status" },
    { field: "amount", header: "Total amount" },
    { field: "due_payment", header: "Due Payment" },
    { field: "payment_schedules", header: "Total Progress Payment" }, //sum of all recipes amount
    {
        field: "total_progress_payment_percentage",
        header: "Total Progress Payment %",
    },
    { field: "start_date", header: "Start Date" },
    { field: "end_date", header: "End Date" },
    { field: "short_description", header: "Short description" },
    {
        field: "actions",
        header: "  Actions",
    },
];

const getTotalPaidAmount = (paymentSchedules = []) => {
    return paymentSchedules.reduce((total, schedule) => {
        const paidAmount = parseFloat(schedule.paid_amount || 0);
        return total + paidAmount;
    }, 0);
};

const paidProgressPayments = computed(() => {
    if (!selectedAgreement.value?.payment_schedules) return [];
    return selectedAgreement.value.payment_schedules.filter(
        (schedule) => getPaymentStatus(schedule) === "PAID"
    );
});

const defaultColumns = columns.filter(
    (col) =>
        ![
            "agreement_doc",
            // "total_progress_payment",
            "address",
            // "agreement_ref_no",
        ].includes(col.field)
);
const selectedColumns = ref(defaultColumns);
const showColumns = ref(defaultColumns);
const updateColumns = (columns) => {
    showColumns.value = selectedColumns.value;
};
const openCreate = () => {
    router.get(route("agreements.create"));
};
const formatDate = (date, format = "YYYY-MM-DD") => {
    if (!date) return "N/A";
    const parsedDate = moment(
        date,
        ["YYYY-MM-DD", "DD/MM/YYYY", moment.ISO_8601],
        true
    );
    return parsedDate.isValid() ? parsedDate.format(format) : "Invalid date";
};
const formatCurrency = (value) => {
    if (value === null || value === undefined || value === "") return "0.00";
    const numValue =
        typeof value === "string"
            ? parseFloat(value.replace(/,/g, ""))
            : Number(value);

    return numValue.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};
// function filter agreements
const searchTerm = ref("");
const searchType = ref("");
const searchOptions = ref([
    { label: "Agreement Number", value: "agreement_no" },
    { label: "Quotation Number", value: "quotation_no" },
    { label: "Agreement Reference Number", value: "agreement_ref_no" },
    { label: "Customer Name", value: "customer.name" },
    { label: "Status", value: "status" },
    { label: "Total Amount", value: "amount" },
    { label: "Due Payment", value: "due_payment" },
    { label: "Start Date", value: "start_date" },
    { label: "End Date", value: "end_date" },
]);
const clearFilters = () => {
    // reset search dropdown + text
    searchType.value = "";
    searchTerm.value = "";
    // reset date pickers
    startDateFilter.value = null;
    endDateFilter.value = null;
};

const getFieldValue = (obj, path) => {
    return path.split(".").reduce((o, p) => (o || {})[p], obj) || "";
};
const startDateFilter = ref(null);
const endDateFilter = ref(null);

const calculateDuePayment = (agreement) => {
    if (
        !agreement.payment_schedules ||
        agreement.payment_schedules.length === 0
    ) {
        return 0;
    }

    const today = moment();
    let duePayment = 0;

    agreement.payment_schedules.forEach((schedule) => {
        const dueDate = moment(schedule.due_date, [
            "YYYY-MM-DD",
            "DD/MM/YYYY",
            moment.ISO_8601,
        ]);

        // Only sum if due date is valid and before today, and amount is positive
        if (dueDate.isValid() && dueDate.isBefore(today, "day")) {
            duePayment += parseFloat(schedule.amount) || 0;
        }
    });

    return duePayment;
};

const processedAgreements = computed(() => {
    return props.agreements.map((agreement) => {
        // Change here: sum paid_amount from payment_schedules instead of progress_payments
        console.log(agreement.total_progress_payment_percentage);
        const totalPaid = (agreement.payment_schedules || []).reduce(
            (sum, schedule) => sum + (parseFloat(schedule.paid_amount) || 0),
            0
        );

        const totalPercentage =
            agreement.amount > 0 ? (totalPaid / agreement.amount) * 100 : 0;

        const schedules = agreement.payment_schedules || [];

        const duePayment = calculateDuePayment(agreement);

        return {
            ...agreement,
            payment_schedules: schedules,
            due_payment: calculateDuePayment(agreement),
            total_progress_payment: totalPaid,
            total_progress_payment_percentage: totalPercentage,
        };
    });
});

// Filter agreements locally (alternative to server-side search)
const filteredAgreements = computed(() => {
    return processedAgreements.value.filter((agreement) => {
        // Start and End Date Filtering
        const start = moment(agreement.start_date, [
            "YYYY-MM-DD",
            "DD/MM/YYYY",
            moment.ISO_8601,
        ]);
        const end = moment(agreement.end_date, [
            "YYYY-MM-DD",
            "DD/MM/YYYY",
            moment.ISO_8601,
        ]);

        const matchStart =
            !startDateFilter.value ||
            start.isSameOrAfter(moment(startDateFilter.value));
        const matchEnd =
            !endDateFilter.value ||
            end.isSameOrBefore(moment(endDateFilter.value));
        // Other field search logic
        const fieldValue = getFieldValue(agreement, searchType.value);
        const matchesSearch =
            !searchTerm.value || !searchType.value
                ? true
                : (typeof fieldValue === "string" &&
                      fieldValue
                          .toLowerCase()
                          .includes(searchTerm.value.toLowerCase())) ||
                  (typeof fieldValue === "number" &&
                      fieldValue.toString().includes(searchTerm.value));

        return matchesSearch && matchStart && matchEnd;
    });
});

const progressBarClass = (percentage) => {
    if (percentage >= 100) return "bg-green-500";
    if (percentage >= 75) return "bg-blue-500";
    if (percentage >= 50) return "bg-yellow-500";
    if (percentage >= 25) return "bg-orange-500";
    return "bg-red-500";
};
// Progress payments dialog
const progressPaymentsDialog = ref(false);
const selectedProgressPayments = ref([]);
const selectedAgreement = ref(null);

const showProgressPayments = (agreement) => {
    try {
        selectedAgreement.value = { ...agreement };
        selectedProgressPayments.value = [
            ...(agreement.progress_payments || []),
        ];
        progressPaymentsDialog.value = true;
    } catch (error) {
        console.error("Error showing progress payments:", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load payment details",
            life: 3000,
        });
    }
};
// Agreement Details Dialog
const agreementDetailsDialog = ref(false);
const selectedAgreementDetails = ref(null);
const viewAgreementDetails = async (agreement) => {
    try {
        const response = await axios.get(
            route("agreements.show", { agreement_no: agreement.agreement_no })
        );

        const formattedData = {
            ...response.data,
            agreement_doc: response.data.agreement_doc,
            attachments: response.data.attachments,
            payment_schedules:
                response.data.payment_schedules?.map((schedule) => ({
                    ...schedule,
                    due_date: moment(
                        schedule.due_date,
                        ["YYYY-MM-DD", "DD/MM/YYYY"],
                        true
                    ).format("YYYY-MM-DD"),
                })) || [],
        };

        selectedAgreementDetails.value = formattedData;
        agreementDetailsDialog.value = true;
    } catch (error) {
        console.error("Error loading agreement details:", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load agreement details",
            life: 3000,
            group: "tr",
        });
    }
};

const isPastDue = (date) => {
    if (!date) return false;
    const today = moment();
    const dueDate = moment(
        date,
        ["YYYY-MM-DD", "DD/MM/YYYY", moment.ISO_8601],
        true
    );
    return dueDate.isValid() && dueDate.isBefore(today, "day");
};
const paymentScheduleRowClass = (payment) => {
    const status = getPaymentStatus(payment);
    console.log("Row Class for:", payment.id, "Status:", status); // Debug

    return {
        // Both “PAST DUE” and “DUE SOON” get the red styling
        "bg-red-500": status === "PAST DUE" || status === "DUE SOON",
        "border-l-4 border-red-500":
            status === "PAST DUE" || status === "DUE SOON",
        // ... other statuses
        "bg-green-50": status === "PAID",
        "border-l-4 border-green-500": status === "PAID",
        "bg-yellow-50": status === "PARTIALLY_PAID",
        "border-l-4 border-yellow-500": status === "PARTIALLY_PAID",
        "bg-blue-50": status === "UPCOMING",
        "border-l-4 border-blue-500": status === "UPCOMING",
    };
};

const rowStyle = (agreement) => {
    if (hasDueSoonOrPastDuePayments(agreement)) {
        return {
            backgroundColor: "#fee2e2",
            borderLeft: "4px solid #f56565",
        };
    }
    return {};
};

const isExpiringSoon = (agreement) => {
    if (agreement.status !== "Open") return false;

    const endDate = moment(
        agreement.end_date,
        ["YYYY-MM-DD", "DD/MM/YYYY"],
        true
    );
    const today = moment();
    const daysRemaining = endDate.diff(today, "days");

    return daysRemaining <= 30 && daysRemaining >= 0;
};

const daysUntilExpiration = (agreement) => {
    const endDate = moment(
        agreement.end_date,
        ["YYYY-MM-DD", "DD/MM/YYYY"],
        true
    );
    const today = moment();
    return endDate.diff(today, "days");
};

const getStatusSeverity = (status) => {
    const upperStatus = status?.toUpperCase();
    switch (upperStatus) {
        case "OPEN":
            return "success";
        case "CLOSED":
            return "danger";
        case "ABNORMAL CLOSED":
            return "warn";
        default:
            return "info";
    }
};

const getStatusLabel = (status) => {
    const upperStatus = status?.toUpperCase();
    switch (upperStatus) {
        case "OPEN":
            return "OPEN";
        case "CLOSED":
            return "CLOSED";
        case "ABNORMAL CLOSED":
            return "ABNORMAL CLOSED";
        default:
            return status || "Unknown";
    }
};

const getPaymentStatus = (schedule) => {
    // First check if fully paid
    if (schedule.status === "PAID" || schedule.paid_amount >= schedule.amount) {
        return "PAID";
    }

    // Then check if partially paid
    if (schedule.paid_amount > 0) {
        return "PARTIALLY_PAID";
    }

    // Check due date status
    const today = moment();
    const dueDate = moment(
        schedule.due_date,
        ["YYYY-MM-DD", "DD/MM/YYYY", moment.ISO_8601],
        true
    );

    if (!dueDate.isValid()) {
        return schedule.status || "UPCOMING";
    }

    // Check if past due
    if (dueDate.isBefore(today, "day")) {
        return "PAST DUE";
    }

    // Check if due within 14 days
    const daysUntilDue = dueDate.diff(today, "days");
    if (daysUntilDue <= 13 && daysUntilDue >= 0) {
        return "DUE SOON";
    }

    // Default to upcoming
    return "UPCOMING";
};
const getStatusSeverityPayment = (schedule) => {
    const status = getPaymentStatus(schedule);
    switch (status) {
        case "PAID":
            return "success";
        case "PARTIALLY_PAID":
            return "warning";
        case "PAST DUE":
            return "danger";
        case "DUE SOON":
            return "warn"; // Orange color
        case "UPCOMING":
            return "info";
        default:
            return "warning";
    }
};
// <!-- Due Soon/Past Due Payments -->
const hasDueSoonOrPastDuePayments = (agreement) => {
    return (agreement.payment_schedules || []).some((schedule) => {
        const status = getPaymentStatus(schedule);
        return status === "DUE SOON" || status === "PAST DUE";
    });
};

const hasDueSoonOrPastDuePaymentsAlter = computed(() => {
    return filteredAgreements.value.some((agreement) => {
        return (agreement.payment_schedules || []).some((schedule) => {
            const status = getPaymentStatus(schedule);
            return status === "DUE SOON" || status === "PAST DUE";
        });
    });
});

const getPaymentStatusTooltip = (agreement) => {
    const schedules = agreement.payment_schedules || [];
    const pastDueCount = schedules.filter(
        (s) => getPaymentStatus(s) === "PAST DUE"
    ).length;
    const dueSoonCount = schedules.filter(
        (s) => getPaymentStatus(s) === "DUE SOON"
    ).length;

    let message = "";
    if (pastDueCount > 0 && dueSoonCount > 0) {
        message = `This agreement has ${pastDueCount} past due and ${dueSoonCount} due soon payments`;
    } else if (pastDueCount > 0) {
        message = `This agreement has ${pastDueCount} past due payment(s)`;
    } else if (dueSoonCount > 0) {
        message = `This agreement has ${dueSoonCount} payment(s) due soon`;
    }

    return message;
};

const alterHasPastDuePayments = computed(() => {
    return filteredAgreements.value.some((agreement) => {
        return (agreement.payment_schedules || []).some(
            (schedule) => getPaymentStatus(schedule) === "PAST DUE"
        );
    });
});

const pastDuePaymentsCount = computed(() => {
    return filteredAgreements.value.reduce((count, agreement) => {
        return (
            count +
            (agreement.payment_schedules || []).filter(
                (schedule) => getPaymentStatus(schedule) === "PAST DUE"
            ).length
        );
    }, 0);
});

const dueSoonPaymentsCount = computed(() => {
    return filteredAgreements.value.reduce((count, agreement) => {
        return (
            count +
            (agreement.payment_schedules || []).filter(
                (schedule) => getPaymentStatus(schedule) === "DUE SOON"
            ).length
        );
    }, 0);
});
const hasDueSoonPayments = (agreement) => {
    return (agreement.payment_schedules || []).some(
        (schedule) => getPaymentStatus(schedule) === "DUE SOON"
    );
};

const hasPastDuePayments = (agreement) => {
    return (agreement.payment_schedules || []).some(
        (schedule) => getPaymentStatus(schedule) === "PAST DUE"
    );
};

const getNearestDueDays = (agreement) => {
    const today = moment();
    let nearestDays = Infinity;

    (agreement.payment_schedules || []).forEach((schedule) => {
        if (getPaymentStatus(schedule) === "DUE SOON") {
            const dueDate = moment(
                schedule.due_date,
                ["YYYY-MM-DD", "DD/MM/YYYY"],
                true
            );
            const daysUntilDue = dueDate.diff(today, "days");
            if (daysUntilDue < nearestDays) {
                nearestDays = daysUntilDue;
            }
        }
    });

    return nearestDays !== Infinity ? nearestDays : 0;
};

const getOverdueDays = (agreement) => {
    const today = moment();
    let maxOverdue = 0;

    (agreement.payment_schedules || []).forEach((schedule) => {
        if (getPaymentStatus(schedule) === "PAST DUE") {
            const dueDate = moment(
                schedule.due_date,
                ["YYYY-MM-DD", "DD/MM/YYYY"],
                true
            );
            const overdueDays = today.diff(dueDate, "days");
            if (overdueDays > maxOverdue) {
                maxOverdue = overdueDays;
            }
        }
    });

    return maxOverdue;
};
</script>

<style scoped>
.text-primary {
    color: var(--primary-color);
}
.text-primary:hover {
    text-decoration: underline;
}
/* Add this to ensure the left border is visible */
:deep(.p-datatable .p-datatable-tbody > tr) {
    border-left: 0 solid transparent; /* Reset default border */
}

.border-left-4 {
    border-left-width: 4px !important;
}
</style>
