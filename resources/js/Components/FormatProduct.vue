<script setup>
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    name: {
        type: String,
        required: true
    },
    amount: {
       type: Number,
        required: true
    },
    measurementTypeId: {
        type: Number,
        required: true
    }
});

const {units} = usePage().props;

// Atlasām mērvienības pēc tipa un sakārtojam no lielākās uz mazāko
const availableUnits = computed(() =>
    units
        .filter(u => u.measurement_type_id === props.measurementTypeId)
        .sort((a, b) => b.conversion_factor - a.conversion_factor)
);

// Izvēlamies piemērotāko mērvienību konkrētajam daudzumam
const selectedUnit = computed(() =>
    availableUnits.value.find(u => props.amount >= u.conversion_factor) ?? availableUnits.value.at(-1)
);

// Pārrēķinām daudzumu uz izvēlēto mērvienību un noapaļojam
const convertedAmount = computed(() => {
    if (!selectedUnit.value) return props.amount;

    const raw = props.amount / selectedUnit.value.conversion_factor;
    const rounded = Math.round(raw * 100) / 100;

    return rounded.toString();
});
</script>

<template>
    <span>
        {{ convertedAmount }}{{ selectedUnit.name }} {{ name }}
    </span>
</template>
