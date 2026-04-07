<script setup>
import InputField from "./Inputs/InputField.vue";
import DurationField from "./Inputs/DurationField.vue";
import SearchableSelect from "./Inputs/SearchableSelect.vue";
import SelectField from "./Inputs/SelectField.vue";
import ImageUpload from "./Inputs/ImageUpload.vue";
import TagSelector from "./Inputs/TagSelector.vue";
import { usePage } from "@inertiajs/vue3";

const { translations, enums } = usePage().props;

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    products: {
        type: Array,
        required: true,
    },
    units: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['submit']);

const visibilityOptions = enums.visibility;

function addInstruction() {
    props.form.instructions.push('');
}

function removeInstruction(index) {
    props.form.instructions.splice(index, 1);
}

function addProduct() {
    props.form.recipe_products.push({
        product_id: '',
        amount: '',
        unit_id: '',
    });
}

function removeProduct(index) {
    props.form.recipe_products.splice(index, 1);
}

function getFilteredUnits(index) {
    const productId = props.form.recipe_products[index].product_id;
    const product = props.products.find(p => p.id === productId);

    if (!product) return [];

    return props.units.filter(
        u => u.measurement_type_id === product.measurement_type_id
    );
}
</script>

<template>
    <form class="form-field" @submit.prevent="emit('submit')">
        <section>
            <InputField
                v-model="form.name"
                type="text"
                :label="translations.fields.labels.name"
                :max-length="50"
                :error="form.errors.name"
            />

            <ImageUpload
                class="form-field-item"
                v-model="form.image_src"
                :label="translations.fields.labels.image"
            />

            <SelectField
                v-model="form.visibility"
                :items="visibilityOptions"
                :label="translations.fields.labels.recipe.visibility"
                :placeholderValue="translations.recipe.visibility.placeholder"
            />
        </section>

        <section>
            <h3>{{ translations.recipe.details }}</h3>

            <div class="grid-container">
                <DurationField
                    v-model="form.prep_time"
                    :label="translations.fields.labels.recipe.prep_time"
                />

                <DurationField
                    v-model="form.cook_time"
                    :label="translations.fields.labels.recipe.cook_time"
                />

                <InputField
                    v-model="form.servings"
                    type="number"
                    :label="translations.fields.labels.recipe.servings"
                    :error="form.errors.servings"
                />
            </div>

            <TagSelector
                v-model:selectedTypeId="form.recipe_type_id"
                v-model:selectedCategoryIds="form.recipe_category_ids"
            />
        </section>

        <section>
            <div class="title">
                <h3>{{ translations.recipe.ingredients }}</h3>
                <button type="button" class="button primary" @click="addProduct">
                    <i class="pi pi-plus"/>
                </button>
            </div>

            <div v-for="(product, index) in form.recipe_products" :key="index" class="ingredient-row">
                <SearchableSelect
                    v-model="form.recipe_products[index].product_id"
                    :items="products"
                    :label="translations.fields.labels.name"
                    :placeholderValue="translations.household.search_products"
                    :notFoundMessage="translations.fields.labels.product.not_found"
                />

                <InputField
                    v-model="form.recipe_products[index].amount"
                    type="number"
                    :label="translations.fields.labels.product.amount"
                    :error="form.errors.amount"
                />

                <SearchableSelect
                    v-model="form.recipe_products[index].unit_id"
                    :items="getFilteredUnits(index)"
                    :label="translations.fields.labels.product.unit"
                    :clearable="false"
                />

                <button
                    type="button"
                    class="button"
                    @click="removeProduct(index)"
                    :disabled="form.recipe_products.length == 1"
                >
                    {{ translations.button.remove }}
                </button>
            </div>
        </section>

        <section>
            <div class="title">
                <h3>{{ translations.recipe.instructions }}</h3>
                <button type="button" class="button primary" @click="addInstruction">
                    <i class="pi pi-plus"/>
                </button>
            </div>

            <div v-for="(step, index) in form.instructions" :key="index" class="instruction-row">
                <InputField
                    v-model="form.instructions[index]"
                    type="text"
                    :label="(index + 1) + '. ' + translations.fields.labels.recipe.step"
                    :error="form.errors.instructions"
                />

                <button
                    type="button"
                    class="button"
                    @click="removeInstruction(index)"
                    :disabled="form.instructions.length === 1"
                >
                    {{ translations.button.remove }}
                </button>
            </div>
        </section>

        <button class="button" type="submit" :disabled="form.processing">
            {{ translations.button.save }}
        </button>
    </form>
</template>
