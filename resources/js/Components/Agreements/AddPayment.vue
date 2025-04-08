<template>
    <div class="flex flex-col gap-5 py-2">
        <FloatLabel variant="on">
            <InputText
                id="total-agreement-amount"
                v-model="model.agreement_amount"
                fluid
                readonly
            />
            <label for="total-agreement-amount" class="required"
                >Total agreement amount</label
            >
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker
                id="due_date"
                size="30"
                v-model="model.due_date"
                fluid
                date-format="yy/mm/dd"
            />
            <label for="due_date" class="required">Due date </label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Textarea
                id="short_description"
                v-model="model.short_description"
                fluid
            ></Textarea>
            <label for="short_description" class="required"
                >Short description</label
            >
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
            <label for="percentage" class="required z-10">Percentage</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputGroup id="amount">
                <InputGroupAddon>{{
                    model.currency == "USD" ? "$" : "៛"
                }}</InputGroupAddon>
                <InputNumber
                    v-model="amountPercentage.amount"
                    fluid
                    @input="doAmountChange"
                    :maxFractionDigits="2"
                />
            </InputGroup>
            <label for="amount" class="required z-10">Amount</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Dropdown
                id="currency"
                v-model="model.currency"
                :options="currencies"
                optionLabel="name"
                optionValue="value"
                class="w-full"
                @change="doCurrencyChange"
            />
            <label for="currency" class="required">Currency</label>
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
            <Button label="Save" class="grow" @click="doSave"></Button>
            <Button
                label="Cancel"
                severity="secondary"
                @click="emit('cancel')"
            ></Button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { currencies } from "@/constants";
import { useToast } from "primevue/usetoast";
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
const maxPercentage = ref(100);
const emit = defineEmits(["cancel", "save"]);
const model = defineModel({
    type: Object,
});
const props = defineProps({
    multiCurrencies: {
        type: Boolean,
        default: () => false,
    },
});
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
onMounted(() => {
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
    const amount =
        model.value.agreement_amount *
        rates[`${model.value.agreement_currency}${model.value.currency}`];
    amountPercentage.value.percentage =
        Math.round(
            ((e.value ? e.value : model.value.amount) / amount) * 10000
        ) / 100;
};
const doPercentageChange = (e) => {
    const amount =
        model.value.agreement_amount *
        rates[`${model.value.agreement_currency}${model.value.currency}`];
    amountPercentage.value.amount =
        Math.round(
            ((e.value ? e.value : model.value.percentage) / 100) * amount * 100
        ) / 100;
};
const doCurrencyChange = (e) => {
    console.log(`${model.value.agreement_currency}${model.value.currency}`);
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
    model.value.amount = amountPercentage.value.amount;
    model.value.percentage = amountPercentage.value.percentage;
    emit("save");
};
const doCancel = () => {
    emit("cancel");
    toast.add({
        severity: "info",
        summary: "Info",
        detail: "Operation cancelled",
        life: 3000,
    });
};
</script>

<style lang="scss" scoped></style>
