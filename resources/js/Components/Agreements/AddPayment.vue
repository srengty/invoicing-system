<template>
    <div class="flex flex-col gap-5 py-2">
        <FloatLabel variant="on">
            <InputNumber
                v-model="model.agreement_amount"
                mode="decimal"
                locale="en-US"
                :minFractionDigits="2"
                :maxFractionDigits="2"
                readonly
                fluid
            />
            <label for="total-agreement-amount" class="required">
                Total agreement amount
            </label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker
                id="due_date"
                size="30"
                v-model="model.due_date"
                fluid
                date-format="yy-mm-dd"
                :min-date="minDate"
            />
            <label for="due_date" class="">Due date</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Textarea
                id="short_description"
                v-model="model.short_description"
                fluid
            ></Textarea>
            <label for="short_description" class="">Short description</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputGroup id="percentage">
                <InputNumber
                    v-model="amountPercentage.percentage"
                    fluid
                    @input="doPercentageChange"
                    :min="0"
                    :max="maxPercentage"
                    :maxFractionDigits="2"
                />
                <InputGroupAddon append>%</InputGroupAddon>
            </InputGroup>
            <label for="percentage" class="z-10">Percentage</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputGroup id="amount">
                <InputGroupAddon append>
                    {{ model.currency === "USD" ? "$" : "៛" }}
                </InputGroupAddon>
                <InputNumber
                    v-model="amountPercentage.amount"
                    mode="decimal"
                    locale="en-US"
                    :minFractionDigits="2"
                    :maxFractionDigits="2"
                    :min="0"
                    :max="maxAmount"
                    fluid
                    @input="doAmountChange"
                />
            </InputGroup>
            <label for="amount" class="z-10">Amount</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <!-- <Dropdown
                id="currency"
                v-model="model.currency"
                :options="currencies"
                optionLabel="name"
                optionValue="value"
                class="w-full"
                @change="doCurrencyChange"
            /> -->
            <InputText
                id="currency"
                v-model="model.currency"
                :options="currencies"
                optionLabel="name"
                optionValue="value"
                class="w-full"
                @change="doCurrencyChange"
                fluid
                readonly
            />
            <label for="currency" class="">Currency</label>
        </FloatLabel>

        <!-- <div class="flex items-center gap-2" v-if="props.multiCurrencies">
            <label for="currency" class="required">Currency</label>
            <RadioButtonGroup
                id="currency"
                v-model="model.currency"
                class="flex flex-wrap gap-4 w-100"
                @value-change="doCurrencyChange"
            >
                <div
                    class="flex items-center gap-2"
                    v-for="currency in currencies"
                    :key="currency"
                >
                    <RadioButton
                        :inputId="currency.value"
                        :value="currency.value"
                    />
                    <label :for="currency.value">{{ currency.name }}</label>
                </div>
            </RadioButtonGroup>
        </div>
        <FloatLabel
            variant="on"
            v-if="model.currency != model.agreement_currency"
        >
            <InputNumber
                v-model="model.exchange_rate"
                fluid
                :maxFractionDigits="6"
                id="exchange_rate"
            />
            <label for="exchange_rate" class="required z-10">Rate</label>
        </FloatLabel> -->

        <div class="flex justify-end gap-2">
            <Button
                label="Save"
                class="grow"
                @click="doSave"
                :disabled="!isValid"
            ></Button>
            <Button
                label="Cancel"
                severity="secondary"
                @click="doCancel"
            ></Button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed, watch } from "vue";
import { currencies } from "@/constants";
import { useToast } from "primevue/usetoast";
import moment from "moment";
import {
    FloatLabel,
    InputText,
    Button,
    DatePicker,
    Textarea,
    RadioButtonGroup,
    RadioButton,
    InputGroup,
    InputGroupAddon,
    InputNumber,
    Toast,
    Dropdown,
} from "primevue";

const toast = useToast();
const maxPercentage = computed(() => {
    return (
        100 - (model.value.totalPercentage || 0) + (model.value.percentage || 0)
    );
});
const maxAmount = computed(() => {
    return (
        model.value.agreement_amount -
        (model.value.totalAmount || 0) +
        (model.value.amount || 0)
    );
});
const isValid = computed(() => {
    return (
        amountPercentage.value.percentage > 0 &&
        amountPercentage.value.amount > 0 &&
        amountPercentage.value.percentage <= maxPercentage.value &&
        amountPercentage.value.amount <= maxAmount.value
    );
});

const exchangeRate = computed(() => {
    if (model.value.currency === model.value.agreement_currency) {
        return 1;
    }
    return model.value.currency === "KHR"
        ? model.value.exchange_rate || 4100
        : 1 / (model.value.exchange_rate || 4100);
});

const emit = defineEmits(["cancel", "save"]);
const model = defineModel({
    type: Object,
    default: () => ({
        due_date: moment(new Date()).add(30, "days").toDate(),
    }),
});
const props = defineProps({
    startDate: [String, Date],
    endDate: [String, Date],
    multiCurrencies: {
        type: Boolean,
        default: () => false,
    },
});
const minDate = computed(() => new Date());
const maxDate = computed(() => null);

const amountPercentage = ref({
    amount: 0,
    percentage: 0,
    currency: "USD",
});
const rates = {
    USDUSD: 1,
    KHRKHR: 1,
    KHRUSD: 1.0 / 4100.0,
    USDKHR: 4100,
};
const formatDate = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}/${month}/${day}`;
};
onMounted(() => {
    if (!model.value.id) {
        model.value.due_date = moment().add(14, "days").toDate();
    }

    amountPercentage.value = {
        percentage: model.value.percentage || 0,
        amount: model.value.amount || 0,
    };
    amountPercentage.value.percentage = model?.value.percentage ?? 0;
    amountPercentage.value.currency = model?.value.currency ?? "USD";
    rates["USDKHR"] = model.value.exchange_rate ?? 4100;
    rates["KHRUSD"] = 1.0 / rates["USDKHR"];
    maxPercentage.value = model?.value.percentage ?? 100;
    amountPercentage.value.amount = model?.value.amount ?? 0;
    if (model.value.currency != model.value.agreement_currency) {
        model.value.exchange_rate =
            rates[`${model.value.agreement_currency}${model.value.currency}`];
        amountPercentage.value.amount =
            amountPercentage.value.amount * model.value.exchange_rate;
    }
});
const doAmountChange = (e) => {
    const newAmount = e.value || 0;
    const totalAgreementInSelectedCurrency =
        model.value.agreement_amount * exchangeRate.value;

    amountPercentage.value.percentage = parseFloat(
        ((newAmount / totalAgreementInSelectedCurrency) * 100).toFixed(2)
    );
    amountPercentage.value.amount = newAmount;
};

const doPercentageChange = (e) => {
    const newPercentage = e.value || 0;
    const totalAgreementInSelectedCurrency =
        model.value.agreement_amount * exchangeRate.value;

    amountPercentage.value.amount = parseFloat(
        ((totalAgreementInSelectedCurrency * newPercentage) / 100).toFixed(2)
    );
    amountPercentage.value.percentage = newPercentage;
};
const doCurrencyChange = (e) => {
    console.log(`${model.value.agreement_currency}${model.value.currency}`);
    doPercentageChange({ value: amountPercentage.value.percentage });
    if (model.value.currency != model.value.agreement_currency) {
        model.value.exchange_rate =
            rates[`${model.value.agreement_currency}${model.value.currency}`];
        amountPercentage.value.amount =
            ((model.value.agreement_amount *
                amountPercentage.value.percentage) /
                100) *
            model.value.exchange_rate;
        // if(model.value.agreement_currency=='USD'){
        // }
        // else amountPercentage.value.amount = (model.value.agreement_amount*model.value.percentage/100)/model.value.exchange_rate;
    } else
        amountPercentage.value.amount =
            (model.value.agreement_amount * amountPercentage.value.percentage) /
            100;
};
const doSave = () => {
    if (!isValid.value) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Please enter valid percentage and amount values",
            life: 3000,
        });
        return;
    }

    model.value.amount = amountPercentage.value.amount;
    model.value.percentage = amountPercentage.value.percentage;
    emit("save");
};
const doCancel = () => {
    emit("cancel");
};
</script>

<style lang="scss" scoped></style>
