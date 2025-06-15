<template>
    <Head title="Invoices" />
    <input type="hidden" name="_token" :value="page.props.csrf_token" />
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
        <div class="invoices px-4">
            <div class="flex justify-end items-center">
                <div class="flex items-center gap-2">
                    <Dropdown
                        v-model="filters.status"
                        :options="statusOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Filter by Status"
                        class="w-48 h-9 text-sm"
                        size="small"
                    />
                    <Button
                        label="Clear"
                        icon="pi pi-times"
                        class="p-button-secondary"
                        size="small"
                        severity="success"
                        variant="outlined"
                        @click="filters.status = null"
                    />

                    <Button
                        v-if="userPermissions.canIssueInvoices"
                        icon="pi pi-plus"
                        label="Issue Invoice"
                        size="small"
                        @click="navigateToCreate"
                        raised
                    />
                    <Link :href="route('quotations.create')"
                        ><Button
                            v-if="userPermissions.canIssueQuotation"
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
                    /> -->
                </div>
            </div>

            <!-- Data Table -->
            <DataTable
                :value="filteredInvoices"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :showGridlines="true"
                size="small"
                class="text-sm mt-8"
                sortMode="single"
                sortField="invoice_no"
            >
                <Column
                    class=""
                    v-for="col in showColumns"
                    :key="col.field"
                    :field="col.field"
                    :header="col.header"
                    :sortable="col.sortable"
                    style="width: 10%; font-size: 12px"
                />

                <Column
                    field="grand_total"
                    header="Amount"
                    style="width: 10%; font-size: 12px"
                >
                    <template #body="{ data }">
                        <span
                            :class="{ 'text-blue-500': data.grand_total >= 0 }"
                        >
                            {{ formatCurrency(data.grand_total) }} (KHR)
                        </span>
                    </template>
                </Column>
                <!-- Approve for division head -->
                <Column
                    v-if="userPermissions.canApproveDivivsionHead"
                    field="hdStatus"
                    header="HD Status"
                    style="width: 10%; font-size: 12px"
                >
                    <template #body="slotProps">
                        <div class="flex">
                            <span
                                class="p-2 border rounded w-28 h-8 flex items-center justify-center gap-2"
                                :class="{
                                    'bg-yellow-100 text-yellow-800 border-yellow-400':
                                        slotProps.data.hdStatus === 'Pending',
                                    'bg-red-100 text-red-800 border-red-400':
                                        slotProps.data.hdStatus === 'revise',
                                    'bg-green-100 text-green-800 border-green-400':
                                        slotProps.data.hdStatus === 'approved',
                                }"
                            >
                                <i
                                    :class="{
                                        'pi pi-clock':
                                            slotProps.data.hdStatus ===
                                            'Pending',
                                        'pi pi-times':
                                            slotProps.data.hdStatus ===
                                            'revise',
                                        'pi pi-check':
                                            slotProps.data.hdStatus ===
                                            'approved',
                                    }"
                                ></i>
                                {{ capitalize(slotProps.data.hdStatus) }}
                            </span>
                            <Button
                                v-if="
                                    getHdComments(slotProps.data.id).length > 0
                                "
                                icon="pi pi-comment"
                                class="p-button-info ml-2"
                                @click="
                                    viewHdComment(
                                        getHdComments(slotProps.data.id)
                                    )
                                "
                                outlined
                            />
                        </div>
                    </template>
                </Column>
                <!-- Approve for revenue manager -->
                <Column
                    v-if="userPermissions.canApproveRevenueManager"
                    field="rmStatus"
                    header="RM Status"
                    style="width: 10%; font-size: 12px"
                >
                    <template #body="slotProps">
                        <div class="flex">
                            <span
                                class="p-2 border rounded w-28 h-8 flex items-center justify-center gap-2"
                                :class="{
                                    'bg-yellow-100 text-yellow-800 border-yellow-400':
                                        slotProps.data.rmStatus === 'Pending',
                                    'bg-red-100 text-red-800 border-red-400':
                                        slotProps.data.rmStatus === 'revise',
                                    'bg-green-100 text-green-800 border-green-400':
                                        slotProps.data.rmStatus === 'approved',
                                }"
                            >
                                <i
                                    :class="{
                                        'pi pi-clock':
                                            slotProps.data.rmStatus ===
                                            'Pending',
                                        'pi pi-times':
                                            slotProps.data.rmStatus ===
                                            'revise',
                                        'pi pi-check':
                                            slotProps.data.rmStatus ===
                                            'approved',
                                    }"
                                ></i>
                                {{ capitalize(slotProps.data.rmStatus) }}
                            </span>
                            <Button
                                v-if="
                                    getRmComments(slotProps.data.id).length > 0
                                "
                                icon="pi pi-comment"
                                class="p-button-info ml-2"
                                @click="
                                    viewRmComment(
                                        getRmComments(slotProps.data.id)
                                    )
                                "
                                outlined
                            />
                        </div>
                    </template>
                </Column>
                <!-- Approve for director -->
                <Column
                    v-if="userPermissions.canApproveDirector"
                    field="status"
                    header="Status"
                    style="width: 10%; font-size: 12px"
                >
                    <template #body="slotProps">
                        <div class="flex">
                            <span
                                class="p-2 border rounded w-28 h-8 flex items-center justify-center gap-2"
                                :class="{
                                    'bg-yellow-100 text-yellow-800 border-yellow-400':
                                        slotProps.data.status === 'Pending',
                                    'bg-red-100 text-red-800 border-red-400':
                                        slotProps.data.status === 'revise',
                                    'bg-green-100 text-green-800 border-green-400':
                                        slotProps.data.status === 'approved',
                                }"
                            >
                                <i
                                    :class="{
                                        'pi pi-clock':
                                            slotProps.data.status === 'Pending',
                                        'pi pi-times':
                                            slotProps.data.status === 'revise',
                                        'pi pi-check':
                                            slotProps.data.status ===
                                            'approved',
                                    }"
                                ></i>
                                {{ capitalize(slotProps.data.status) }}
                            </span>
                            <Button
                                v-if="
                                    slotProps.data.invoice_comments &&
                                    slotProps.data.invoice_comments.length > 0
                                "
                                icon="pi pi-comment"
                                class="p-button-info ml-2"
                                @click="
                                    viewComment(slotProps.data.invoice_comments)
                                "
                                outlined
                            />
                        </div>
                    </template>
                </Column>
                <!-- Actions -->
                <Column
                    header="Actions"
                    headerStyle="text-align: center"
                    bodyStyle="text-align: center"
                    style="width: 5%; font-size: 12px"
                >
                    <template #body="{ data }">
                        <div class="flex gap-2 justify-center">
                            <Button
                                v-if="userPermissions.canViewDivivsionHead"
                                icon="pi pi-eye"
                                aria-label="View"
                                severity="info"
                                class="custom-button"
                                @click="viewHdInvoice(data)"
                                size="small"
                                outlined
                            />
                            <Button
                                v-if="userPermissions.canViewRevenueManager"
                                icon="pi pi-eye"
                                aria-label="View"
                                severity="info"
                                class="custom-button"
                                @click="viewRmInvoice(data)"
                                size="small"
                                outlined
                            />
                            <Button
                                v-if="userPermissions.canViewDirector"
                                icon="pi pi-eye"
                                aria-label="View"
                                severity="info"
                                class="custom-button"
                                @click="viewInvoice(data)"
                                size="small"
                                outlined
                            />
                            <div v-if="userPermissions.canEditInvoices">
                                <Button
                                    v-if="canEditInvoice(data)"
                                    icon="pi pi-pencil"
                                    aria-label="Edit"
                                    severity="warning"
                                    class="custom-button"
                                    @click="editInvoice(data)"
                                    size="small"
                                    outlined
                                />
                            </div>
                            <Button
                                v-if="userPermissions.canPrintInvoices"
                                icon="pi pi-print"
                                aria-label="Print"
                                @click="printInvoice(data.id)"
                                outlined
                                size="small"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <!-- Comments Dialog -->
            <Dialog
                v-model:visible="isCommentDialogVisible"
                header="Comments"
                class="w-80"
                :draggable="false"
                :resizable="false"
                :position="'center'"
                :closeOnEscape="false"
            >
                <div v-if="selectedComment.length > 0">
                    <div
                        v-for="(item, index) in selectedComment"
                        :key="index"
                        class="text-border"
                    >
                        <p>{{ item.comment || "No comment text" }}</p>
                        <small>{{ formatDate(item.created_at) }}</small>
                    </div>
                </div>
                <div v-else>
                    <p>No comments available</p>
                </div>
                <div class="flex justify-end mt-4">
                    <Button
                        label="Close"
                        class="p-button-secondary"
                        @click="isCommentDialogVisible = false"
                    />
                </div>
            </Dialog>

            <!-- Comments Dialog -->
            <Dialog
                v-model:visible="isFeedbackDialogVisible"
                header="Customer Feedback"
                modal
                :style="{ width: '30rem' }"
                class="text-sm"
                :draggable="false"
                :resizable="false"
                :position="'center'"
                :closeOnEscape="false"
            >
                <div v-if="selectedInvoice" class="flex flex-col gap-4">
                    <p>
                        <strong>Quotation No.:</strong>
                        {{ selectedInvoice.invoice_no }}
                    </p>
                    <p>
                        <strong>Customer Name:</strong>
                        {{ selectedInvoice.customer?.name || "N/A" }}
                    </p>
                    <p>
                        <strong>Total:</strong>
                        {{ selectedInvoice.total }}
                    </p>
                    <div class="flex flex-col gap-2">
                        <label for="feedbackComment" class="block font-bold"
                            >Comment:</label
                        >
                        <textarea
                            id="feedbackComment"
                            v-model="feedbackComment"
                            rows="3"
                            class="w-full border rounded p-2"
                            placeholder="Enter your feedback here..."
                        ></textarea>
                    </div>

                    <!-- Approve/Reject Buttons -->
                    <div class="flex justify-end gap-2">
                        <Button
                            label="Approve"
                            severity="success"
                            @click="handleApprove"
                            :disabled="selectedInvoice?.status === 'approved'"
                        />
                        <Button
                            label="Reject"
                            severity="danger"
                            @click="handleReject"
                            :disabled="selectedInvoice?.status === 'approved'"
                        />
                    </div>
                </div>
            </Dialog>

            <!-- View Detail Invoices for RM -->
            <Dialog
                v-model:visible="isViewRmDialogVisible"
                header="Invoice Details for RM"
                modal
                :style="{ width: '40rem' }"
                class="text-sm"
                :draggable="false"
                :resizable="false"
                :position="'center'"
                :closeOnEscape="false"
            >
                <div v-if="selectedInvoice" class="p-4 space-y-4">
                    <!-- Header Info -->
                    <div class="flex justify-between">
                        <div class="flex flex-col w-1/2 gap-4">
                            <p>
                                <strong>Customer Name:</strong>
                                {{ selectedInvoice.customer?.name || "N/A" }}
                            </p>
                            <p>
                                <strong>Address:</strong>
                                {{ selectedInvoice.address }}
                            </p>
                            <p>
                                <strong>Email:</strong>
                                <a
                                    v-if="
                                        selectedInvoice.email ||
                                        selectedInvoice.customer?.email
                                    "
                                    :href="`mailto:${
                                        selectedInvoice.email ||
                                        selectedInvoice.customer?.email
                                    }`"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{
                                        selectedInvoice.email ||
                                        selectedInvoice.customer?.email
                                    }}
                                </a>
                                <span v-else>N/A</span>
                            </p>
                        </div>
                        <div class="flex flex-col w-1/2 items-end gap-4">
                            <div class="grid gap-4">
                                <p v-if="selectedInvoice.quotation_no">
                                    <strong>Quotation No.:</strong>
                                    {{ selectedInvoice.quotation_no }}
                                </p>
                                <p v-else-if="selectedInvoice.agreement_no">
                                    <strong>Agreement No.:</strong>
                                    {{ selectedInvoice.agreement_no }}
                                </p>
                                <p>
                                    <strong>Invoice No.:</strong>
                                    {{ selectedInvoice.invoice_no }}
                                </p>
                                <p>
                                    <strong>Invoice Date:</strong>
                                    {{ selectedInvoice.invoice_date }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <span class="font-bold block mb-2 text-center">Items</span>
                    <DataTable
                        v-if="selectedInvoice.payment_schedules?.length"
                        :value="selectedInvoice.payment_schedules"
                        responsiveLayout="scroll"
                        class="text-sm mb-4"
                    >
                        <Column field="id" header="Payment Schedule ID" />
                        <Column field="amount" header="Amount">
                            <template #body="{ data }">
                                {{ formatCurrency(data.amount) }}
                            </template>
                        </Column>
                        <Column
                            field="short_description"
                            header="Description"
                        />
                    </DataTable>

                    <DataTable
                        v-else
                        :value="selectedInvoice.products"
                        responsiveLayout="scroll"
                        class="text-sm"
                    >
                        <Column field="name" header="Item" />
                        <Column field="pivot.quantity" header="Qty" />
                        <Column header="Unit Price">
                            <template #body="{ data }">
                                {{ formatCurrency(data.pivot.price) }}
                            </template>
                        </Column>
                    </DataTable>

                    <!-- Totals -->
                    <div class="text-left">
                        <br />
                        <p>
                            <strong>Total:</strong>
                            {{ formatCurrency(selectedInvoice.grand_total) }}
                            <span class="text-xs text-gray-500 ml-1"
                                >(KHR)</span
                            >
                        </p>
                    </div>
                </div>

                <!-- Comments -->
                <div class="p-4">
                    <label for="comment" class="block font-bold mb-2"
                        >Comment:</label
                    >
                    <textarea
                        id="comment"
                        v-model="statusForm.comment"
                        placeholder="Enter your comment..."
                        class="w-full p-2 border rounded"
                        :class="{ 'border-red-500': statusForm.errors.comment }"
                    />
                    <p
                        v-if="statusForm.errors.comment"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ statusForm.errors.comment }}
                    </p>
                </div>

                <!-- Footer Buttons -->
                <template #footer>
                    <Button
                        label="Approve"
                        icon="pi pi-check"
                        class="p-button-success"
                        size="small"
                        @click="changeRmStatus('approved')"
                        :disabled="selectedInvoice?.status === 'approved'"
                    />
                    <Button
                        label="Revise"
                        icon="pi pi-times"
                        class="p-button-danger"
                        size="small"
                        @click="changeRmStatus('revise')"
                        :disabled="selectedInvoice?.status === 'approved'"
                    />
                    <Button
                        label="Close"
                        severity="secondary"
                        @click="isViewRmDialogVisible = false"
                    />
                </template>
            </Dialog>

            <!-- View Detail Invoices for HD -->
            <Dialog
                v-model:visible="isViewHdDialogVisible"
                header="Invoice Details for HD"
                modal
                :style="{ width: '40rem' }"
                class="text-sm"
                :draggable="false"
                :resizable="false"
                :position="'center'"
                :closeOnEscape="false"
            >
                <div v-if="selectedInvoice" class="p-4 space-y-4">
                    <!-- Header Info -->
                    <div class="flex justify-between">
                        <div class="flex flex-col w-1/2 gap-4">
                            <p>
                                <strong>Customer Name:</strong>
                                {{ selectedInvoice.customer?.name || "N/A" }}
                            </p>
                            <p>
                                <strong>Address:</strong>
                                {{ selectedInvoice.address }}
                            </p>
                            <p>
                                <strong>Email:</strong>
                                <a
                                    v-if="
                                        selectedInvoice.email ||
                                        selectedInvoice.customer?.email
                                    "
                                    :href="`mailto:${
                                        selectedInvoice.email ||
                                        selectedInvoice.customer?.email
                                    }`"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{
                                        selectedInvoice.email ||
                                        selectedInvoice.customer?.email
                                    }}
                                </a>
                                <span v-else>N/A</span>
                            </p>
                        </div>
                        <div class="flex flex-col w-1/2 items-end gap-4">
                            <div class="grid gap-4">
                                <p v-if="selectedInvoice.quotation_no">
                                    <strong>Quotation No.:</strong>
                                    {{ selectedInvoice.quotation_no }}
                                </p>
                                <p v-else-if="selectedInvoice.agreement_no">
                                    <strong>Agreement No.:</strong>
                                    {{ selectedInvoice.agreement_no }}
                                </p>
                                <p>
                                    <strong>Invoice No.:</strong>
                                    {{ selectedInvoice.invoice_no }}
                                </p>
                                <p>
                                    <strong>Invoice Date:</strong>
                                    {{ selectedInvoice.invoice_date }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <span class="font-bold block mb-2 text-center">Items</span>
                    <DataTable
                        v-if="selectedInvoice.payment_schedules?.length"
                        :value="selectedInvoice.payment_schedules"
                        responsiveLayout="scroll"
                        class="text-sm mb-4"
                    >
                        <Column field="id" header="Payment Schedule ID" />
                        <Column field="amount" header="Amount">
                            <template #body="{ data }">
                                {{ formatCurrency(data.amount) }}
                            </template>
                        </Column>
                        <Column
                            field="short_description"
                            header="Description"
                        />
                    </DataTable>

                    <DataTable
                        v-else
                        :value="selectedInvoice.products"
                        responsiveLayout="scroll"
                        class="text-sm"
                    >
                        <Column field="name" header="Item" />
                        <Column field="pivot.quantity" header="Qty" />
                        <Column header="Unit Price">
                            <template #body="{ data }">
                                {{ formatCurrency(data.pivot.price) }}
                            </template>
                        </Column>
                    </DataTable>

                    <!-- Totals -->
                    <div class="text-left">
                        <br />
                        <p>
                            <strong>Total:</strong>
                            {{ formatCurrency(selectedInvoice.grand_total) }}
                            <span class="text-xs text-gray-500 ml-1"
                                >(KHR)</span
                            >
                        </p>
                    </div>
                </div>

                <!-- Comments -->
                <div class="p-4">
                    <label for="comment" class="block font-bold mb-2"
                        >Comment:</label
                    >
                    <textarea
                        id="comment"
                        v-model="statusForm.comment"
                        placeholder="Enter your comment..."
                        class="w-full p-2 border rounded"
                        :class="{ 'border-red-500': statusForm.errors.comment }"
                    />
                    <p
                        v-if="statusForm.errors.comment"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ statusForm.errors.comment }}
                    </p>
                </div>

                <!-- Footer Buttons -->
                <template #footer>
                    <Button
                        label="Approve"
                        icon="pi pi-check"
                        class="p-button-success"
                        size="small"
                        @click="changeHdStatus('approved')"
                        :disabled="
                            statusForm.processing ||
                            !statusForm.comment.trim() ||
                            selectedInvoice?.status === 'approved'
                        "
                    />
                    <Button
                        label="Revise"
                        icon="pi pi-times"
                        class="p-button-danger"
                        size="small"
                        @click="changeHdStatus('revise')"
                        :disabled="
                            statusForm.processing ||
                            !statusForm.comment.trim() ||
                            selectedInvoice?.status === 'approved'
                        "
                    />
                    <Button
                        label="Close"
                        severity="secondary"
                        @click="isViewHdDialogVisible = false"
                    />
                </template>
            </Dialog>

            <!-- View Detail Invoices for Director -->
            <Dialog
                v-model:visible="isViewDialogVisible"
                header="Invoice Details"
                modal
                :style="{ width: '40rem' }"
                class="text-sm"
                :draggable="false"
                :resizable="false"
                :position="'center'"
                :closeOnEscape="false"
            >
                <div v-if="selectedInvoice" class="p-4 space-y-4">
                    <!-- Header Info -->
                    <div class="flex justify-between">
                        <div class="flex flex-col w-1/2 gap-4">
                            <p>
                                <strong>Customer Name:</strong>
                                {{ selectedInvoice.customer?.name || "N/A" }}
                            </p>
                            <p>
                                <strong>Address:</strong>
                                {{ selectedInvoice.address }}
                            </p>
                            <p>
                                <strong>Email:</strong>
                                <a
                                    v-if="
                                        selectedInvoice.email ||
                                        selectedInvoice.customer?.email
                                    "
                                    :href="`mailto:${
                                        selectedInvoice.email ||
                                        selectedInvoice.customer?.email
                                    }`"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{
                                        selectedInvoice.email ||
                                        selectedInvoice.customer?.email
                                    }}
                                </a>
                                <span v-else>N/A</span>
                            </p>
                        </div>
                        <div class="flex flex-col w-1/2 items-end gap-4">
                            <div class="grid gap-4">
                                <p v-if="selectedInvoice.quotation_no">
                                    <strong>Quotation No.:</strong>
                                    {{ selectedInvoice.quotation_no }}
                                </p>
                                <p v-else-if="selectedInvoice.agreement_no">
                                    <strong>Agreement No.:</strong>
                                    {{ selectedInvoice.agreement_no }}
                                </p>
                                <p>
                                    <strong>Invoice No.:</strong>
                                    {{ selectedInvoice.invoice_no }}
                                </p>
                                <p>
                                    <strong>Invoice Date:</strong>
                                    {{ selectedInvoice.invoice_date }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <span class="font-bold block mb-2 text-center">Items</span>
                    <DataTable
                        v-if="selectedInvoice.payment_schedules?.length"
                        :value="selectedInvoice.payment_schedules"
                        responsiveLayout="scroll"
                        class="text-sm mb-4"
                    >
                        <Column field="id" header="Payment Schedule ID" />
                        <Column field="amount" header="Amount">
                            <template #body="{ data }">
                                {{ formatCurrency(data.amount) }}
                            </template>
                        </Column>
                        <Column
                            field="short_description"
                            header="Description"
                        />
                    </DataTable>

                    <DataTable
                        v-else
                        :value="selectedInvoice.products"
                        responsiveLayout="scroll"
                        class="text-sm"
                    >
                        <Column field="name" header="Item" />
                        <Column field="pivot.quantity" header="Qty" />
                        <Column header="Unit Price">
                            <template #body="{ data }">
                                {{ formatCurrency(data.pivot.price) }}
                            </template>
                        </Column>
                    </DataTable>

                    <!-- Totals -->
                    <div class="text-left">
                        <br />
                        <p>
                            <strong>Total:</strong>
                            {{ formatCurrency(selectedInvoice.grand_total) }}
                            <span class="text-xs text-gray-500 ml-1"
                                >(KHR)</span
                            >
                        </p>
                    </div>
                </div>

                <!-- Comments -->
                <div class="p-4">
                    <label for="comment" class="block font-bold mb-2"
                        >Comment:</label
                    >
                    <textarea
                        id="comment"
                        v-model="statusForm.comment"
                        placeholder="Enter your comment..."
                        class="w-full p-2 border rounded"
                        :class="{ 'border-red-500': statusForm.errors.comment }"
                    />
                    <p
                        v-if="statusForm.errors.comment"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ statusForm.errors.comment }}
                    </p>
                </div>

                <!-- Footer Buttons -->
                <template #footer>
                    <Button
                        label="Approve"
                        icon="pi pi-check"
                        class="p-button-success"
                        size="small"
                        @click="changeStatus('approved')"
                        :disabled="
                            statusForm.processing ||
                            !statusForm.comment.trim() ||
                            selectedInvoice?.status === 'approved'
                        "
                    />
                    <Button
                        label="Revise"
                        icon="pi pi-times"
                        class="p-button-danger"
                        size="small"
                        @click="changeStatus('revise')"
                        :disabled="
                            statusForm.processing ||
                            !statusForm.comment.trim() ||
                            selectedInvoice?.status === 'approved'
                        "
                    />
                    <Button
                        label="Close"
                        severity="secondary"
                        @click="isViewDialogVisible = false"
                    />
                </template>
            </Dialog>

            <!-- Comment Dialog -->
            <Dialog
                v-model:visible="isCommentDialogVisible"
                :header="commentDialogTitle"
                class="w-80"
                :draggable="false"
                :resizable="false"
                :position="'center'"
                :closeOnEscape="false"
            >
                <div v-if="selectedComment.length > 0">
                    <div
                        v-for="(item, index) in selectedComment"
                        :key="index"
                        class="text-border mb-4 p-2 border-b"
                    >
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-semibold">{{
                                capitalize(item.status)
                            }}</span>
                            <small class="text-gray-500">{{
                                formatDate(item.created_at)
                            }}</small>
                        </div>
                        <p class="text-sm">
                            {{ item.comment || "No comment text" }}
                        </p>
                    </div>
                </div>
                <div v-else>
                    <p>No comments available</p>
                </div>
                <div class="flex justify-end mt-4">
                    <Button
                        label="Close"
                        class="p-button-secondary"
                        @click="isCommentDialogVisible = false"
                    />
                </div>
            </Dialog>
        </div>
    </GuestLayout>
</template>

<script setup>
import ChooseColumns from "@/Components/ChooseColumns.vue";
import Customers from "@/Components/Customers.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import moment from "moment";
import { useForm } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, computed, onMounted, defineProps } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/vue3";
import { watch } from "vue";
import {
    Button,
    DataTable,
    Column,
    DatePicker,
    InputText,
    Dropdown,
    Dialog,
    KeyFilter,
    Breadcrumb,
} from "primevue";

// Props
const props = defineProps({
    invoices: { type: Object, required: true },
    hdComments: { type: Array, default: () => [] },
    rmComments: { type: Array, default: () => [] },
});
onMounted(() => {
    console.log("Invoices:", props.invoices);
    console.log("HD Comments:", props.hdComments);
    console.log("RM Comments:", props.rmComments);
});

// The Breadcrumb Quotations
const page = usePage();
const userPermissions = computed(() => {
    const roles = page.props.userRoles || [];
    return {
        canIssueInvoices: roles.some((role) =>
            role.toLowerCase().includes("division staff")
        ),
        canIssueQuotation: roles.some((role) =>
            role.toLowerCase().includes("division staff")
        ),
        canCustomerStatus: roles.some((role) =>
            role.toLowerCase().includes("division staff")
        ),
        canEditInvoices: roles.some(
            (role) =>
                role.toLowerCase().includes("division staff") ||
                role.toLowerCase().includes("chef department")
        ),
        canPrintInvoices: roles.some(
            (role) =>
                role.toLowerCase().includes("director") ||
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("revenue manager")
        ),
        canApproveDivivsionHead: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("division staff")
        ),
        canViewDivivsionHead: roles.some((role) =>
            role.toLowerCase().includes("chef department")
        ),
        canApproveRevenueManager: roles.some(
            (role) =>
                role.toLowerCase().includes("revenue manager") ||
                role.toLowerCase().includes("division staff")
        ),
        canViewRevenueManager: roles.some((role) =>
            role.toLowerCase().includes("revenue manager")
        ),
        canApproveDirector: roles.some(
            (role) =>
                role.toLowerCase().includes("director") ||
                role.toLowerCase().includes("division staff")
        ),
        canViewDirector: roles.some((role) =>
            role.toLowerCase().includes("director")
        ),
    };
});
const items = computed(() => [
    { label: "", to: "/dashboard", icon: "pi pi-home" },
    { label: page.props.title || "Invoices", to: route("invoices.list") },
]);

const filters = ref({
    status: null,
});

const selectedInvoice = ref(null);
const isViewHdDialogVisible = ref(false);
const isViewRmDialogVisible = ref(false);
const isViewDialogVisible = ref(false);
const isFeedbackDialogVisible = ref(false);
const comment = ref("");

const commentDialogTitle = ref("Comments");

const viewHdInvoice = (invoice) => {
    statusForm.reset();
    selectedInvoice.value = invoice;
    isViewHdDialogVisible.value = true;
};

const viewRmInvoice = (invoice) => {
    statusForm.reset();
    selectedInvoice.value = invoice;
    isViewRmDialogVisible.value = true;
};

const viewInvoice = (invoice) => {
    statusForm.reset();
    selectedInvoice.value = invoice;
    isViewDialogVisible.value = true;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const statusOptions = ref([
    { label: "Pending", value: "Pending" },
    { label: "Approved", value: "Approved" },
    { label: "Revise", value: "Revise" },
    { label: "Rejected", value: "Rejected" },
]);

const columns = [
    { field: "invoice_no", header: "Invoice No", sortable: true },
    //{ field: "start_date", header: "Date", sortable: true },
    //{ field: "end_date", header: "Due Date", sortable: true },
    { field: "customer.name", header: "Customer", sortable: true },
    { field: "agreement_no", header: "Agreement No" },
    { field: "quotation_no", header: "Quotation No" },
];

const filteredInvoices = computed(() => {
    let invoices = props.invoices.data;

    // Filter by selected status
    if (filters.value.status) {
        invoices = invoices.filter(
            (invoice) => invoice.status === filters.value.status
        );
    }

    // Normalize roles for comparison
    const roles = (page.props.userRoles || []).map((r) => r.toLowerCase());

    const isChefDeptOnly =
        roles.includes("chef department") &&
        !roles.includes("revenue manager") &&
        !roles.includes("director");

    const isDirectorOnly =
        roles.includes("director") &&
        !roles.includes("revenue manager") &&
        !roles.includes("chef department");

    const isRMOnly =
        !roles.includes("director") &&
        roles.includes("revenue manager") &&
        !roles.includes("chef department");

    if (isRMOnly) {
        invoices = invoices.filter((invoice) => {
            return invoice.hdStatus === "approved";
        });
    }

    if (isDirectorOnly) {
        invoices = invoices.filter((invoice) => {
            return invoice.rmStatus === "approved";
        });
    }

    return invoices;
});

const selectedComment = ref("");
const commentText = ref("");
const isCommentDialogVisible = ref(false);
const isStatusDialogVisible = ref(false);

const statusForm = useForm({
    status: "",
    comment: "",
});

// Open the status dialog for a selected invoice
const toggleStatus = (invoice) => {
    selectedInvoice.value = invoice;
    isStatusDialogVisible.value = true;

    // Reset form fields before showing the dialog
    statusForm.reset();
};

const canEditInvoice = (invoice) => {
    const hasPaymentSchedules =
        invoice.payment_schedules && invoice.payment_schedules.length > 0;

    const editableStatus =
        invoice.status !== "approved" || invoice.status === "revise";

    const hasQuotation = invoice.quotation_no;

    return editableStatus && !hasPaymentSchedules && !hasQuotation;
};

// right after your other consts
const capitalize = (text) => {
    if (!text) return "";
    return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
};

const editInvoice = (invoice) => {
    Inertia.visit(`/invoices/${invoice.id}/edit`);
};

// Add these computed properties to filter comments by invoice ID
const getHdComments = (invoiceId) => {
    return (Array.isArray(props.hdComments) ? props.hdComments : []).filter(
        (c) => c.invoice_id === invoiceId
    );
};

const getRmComments = (invoiceId) => {
    return (Array.isArray(props.rmComments) ? props.rmComments : []).filter(
        (c) => c.invoice_id === invoiceId
    );
};

const changeHdStatus = (status) => {
    if (!selectedInvoice.value) return;

    // Ensure the status you're sending is valid
    const validStatuses = ["approved", "rejected", "pending", "revise"]; // Include 'revise'
    if (!validStatuses.includes(status)) {
        console.error(`Invalid status: ${status}`);
        return; // Prevent the API call with invalid status
    }

    // Set the status and comment from the form
    statusForm.status = status;
    statusForm.comment = statusForm.comment.trim(); // Ensure no leading/trailing spaces

    // Update the invoice status via an API request
    statusForm.put(route("invoices.updateStatusHD", selectedInvoice.value.id), {
        headers: {
            "X-CSRF-TOKEN": page.props.csrf_token,
            "X-Requested-With": "XMLHttpRequest",
        },
        onSuccess: () => {
            // close the details dialog
            isViewHdDialogVisible.value = false;

            // reset for next time
            statusForm.reset();
            selectedInvoice.value = null;
        },
        onError: (errors) => {
            console.error("Status update failed:", errors);
        },
    });
};

const changeRmStatus = (status) => {
    if (!selectedInvoice.value) return;

    // Ensure the status you're sending is valid
    const validStatuses = ["approved", "rejected", "pending", "revise"]; // Include 'revise'
    if (!validStatuses.includes(status)) {
        console.error(`Invalid status: ${status}`);
        return; // Prevent the API call with invalid status
    }

    // Set the status and comment from the form
    statusForm.status = status;
    statusForm.comment = statusForm.comment.trim(); // Ensure no leading/trailing spaces

    // Update the invoice status via an API request
    statusForm.put(route("invoices.updateStatusRM", selectedInvoice.value.id), {
        headers: {
            "X-CSRF-TOKEN": page.props.csrf_token,
            "X-Requested-With": "XMLHttpRequest",
        },
        onSuccess: () => {
            // close the details dialog
            isViewRmDialogVisible.value = false;

            // reset for next time
            statusForm.reset();
            selectedInvoice.value = null;
        },
        onError: (errors) => {
            console.error("Status update failed:", errors);
        },
    });
};

const changeStatus = (status) => {
    if (!selectedInvoice.value) return;

    // Ensure the status you're sending is valid
    const validStatuses = ["approved", "rejected", "pending", "revise"]; // Include 'revise'
    if (!validStatuses.includes(status)) {
        console.error(`Invalid status: ${status}`);
        return; // Prevent the API call with invalid status
    }

    // Set the status and comment from the form
    statusForm.status = status;
    statusForm.comment = statusForm.comment.trim(); // Ensure no leading/trailing spaces

    // Update the invoice status via an API request
    statusForm.put(route("invoices.updateStatus", selectedInvoice.value.id), {
        headers: {
            "X-CSRF-TOKEN": page.props.csrf_token,
            "X-Requested-With": "XMLHttpRequest",
        },
        onSuccess: () => {
            // close the details dialog
            isViewDialogVisible.value = false;

            // reset for next time
            statusForm.reset();
            selectedInvoice.value = null;
        },
        onError: (errors) => {
            console.error("Status update failed:", errors);
        },
    });
};

const viewComment = (invoiceComments) => {
    commentDialogTitle.value = "Invoice Comments";
    if (invoiceComments && Array.isArray(invoiceComments)) {
        selectedComment.value = invoiceComments;
        isCommentDialogVisible.value = true;
    } else {
        selectedComment.value = [];
        isCommentDialogVisible.value = true;
    }
};

const viewHdComment = (hdComments) => {
    commentDialogTitle.value = "HD Approval Comments";
    if (hdComments && Array.isArray(hdComments)) {
        selectedComment.value = hdComments;
        isCommentDialogVisible.value = true; // Ensure the dialog is visible
    } else {
        selectedComment.value = [];
        isCommentDialogVisible.value = true; // Ensure the dialog is visible
    }
};

const viewRmComment = (rmComments) => {
    commentDialogTitle.value = "RM Approval Comments";
    if (rmComments && Array.isArray(rmComments)) {
        selectedComment.value = rmComments;
        isCommentDialogVisible.value = true; // Ensure the dialog is visible
    } else {
        selectedComment.value = [];
        isCommentDialogVisible.value = true; // Ensure the dialog is visible
    }
};

const handleStatusClick = (invoice) => {
    selectedInvoice.value = invoice;

    if (invoice.customer_status === "Pending") {
        isSendDialogVisible.value = true;
    } else if (invoice.customer_status === "Sent") {
        isFeedbackDialogVisible.value = true;
    }
};

const formatDate = (dateString) => {
    return moment(dateString).format("MMMM D, YYYY [at] h:mm A");
};

const selectedColumns = ref(columns.slice());
const showColumns = ref(columns);

const updateColumns = () => {
    showColumns.value = selectedColumns.value;
};

const navigateToCreate = () => {
    Inertia.visit("/invoices/create");
};

const deleteInvoice = (id) => {
    if (confirm("Are you sure you want to delete this invoice?")) {
        Inertia.delete(`/invoices/${id}`);
    }
};

const over_due = (rowData) => {
    if (!rowData.end_date) return "-"; // If there's no end date, return "-"

    const dueDate = moment(rowData.end_date);
    const currentDate = moment();
    const overdue = currentDate.diff(dueDate, "days");

    // If overdue is 0, return "Not Past Due", otherwise return the number of overdue days
    return overdue > 0 ? `${overdue} days ago` : "Not Past Due";
};

const printInvoice = (invoice_no, include_catelog = 0) => {
    const invoiceUrl = `/invoices/${invoice_no}?include_catelog=${include_catelog}`;
    const printWindow = window.open(invoiceUrl, "_self");
};

const searchInvoices = () => {
    const formattedStartDate = filters.value.start_date
        ? moment(filters.value.start_date).format("YYYY-MM-DD")
        : null;
    const formattedEndDate = filters.value.end_date
        ? moment(filters.value.end_date).format("YYYY-MM-DD")
        : null;

    // Create an object with all filter values
    const invoiceFilters = {
        invoice_no_start: filters.value.invoice_no_start,
        invoice_no_end: filters.value.invoice_no_end,
        category_name_english: filters.value.category_name_english,
        currency: filters.value.currency,
        start_date: formattedStartDate,
        end_date: formattedEndDate,
        customer: filters.value.customer,
        status: filters.value.status,
        income_type: filters.value.income_type,
    };

    // Save all filter values to localStorage as a JSON string
    localStorage.setItem("invoiceFilters", JSON.stringify(invoiceFilters));

    Inertia.get("/invoices", invoiceFilters);
};

const clearFilters = () => {
    filters.value = {
        invoice_no_start: null,
        invoice_no_end: null,
        category_name_english: null, // Changed from customer_type to category_name_english
        currency: null,
        start_date: null,
        end_date: null,
        customer: null,
        status: null,
    };
    searchInvoices();
};

const computeAmountDue = (invoice) => {
    const grandTotal = invoice.grand_total || 0;
    const amountPaid = invoice.agreement?.amount || 0;
    return (grandTotal - amountPaid).toFixed(2);
};
</script>

<style scoped></style>
