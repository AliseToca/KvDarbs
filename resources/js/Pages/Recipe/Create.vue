<script setup>
import {usePage} from "@inertiajs/vue3";
import {reactive, computed} from "vue";
import {route} from "ziggy-js";
import MainLayout from '../../Layouts/Main.vue';
import InputField from "../../Components/Inputs/InputField.vue";
import DurationField from "../../Components/Inputs/DurationField.vue";
import SearchableSelect from "../../Components/Inputs/SearchableSelect.vue";
import SelectField from "../../Components/Inputs/SelectField.vue";

const { translations, products, units} = usePage().props;

const form = reactive({
    name: '',
    prep_time: 0,
    cook_time: 0,
    servings: 0,
    recipe_products: [
        {
            product_id: '',
            amount: '',
            unit_id: '',
        }
    ],
    instructions: [''],
    visibility: 'household',
    errors: {},
})

const visibilityOptions = [
    { id: 'private', name: translations.recipe.visibility.private },
    { id: 'household', name: translations.recipe.visibility.household },
    { id: 'public', name: translations.recipe.visibility.public },
];

function addInstruction() {
    form.instructions.push('');
}

function removeInstruction(index) {
    form.instructions.splice(index, 1);
}

function addProduct() {
    form.recipe_products.push({
        product_id: '',
        amount: '',
        unit_id: '',
    });
}

function removeProduct(index) {
    form.recipe_products.splice(index, 1);
}

// Iegūst atbilstošās mērvienības
function getFilteredUnits(index) {
    const productId = form.recipe_products[index].product_id;
    const product = products.find(p => p.id === productId);

    if (!product) return [];

    return units.filter(
        u => u.measurement_type_id === product.measurement_type_id
    );
}


function submit() {
    form.post(route('recipes.store'));
}
</script>

<template>
    <MainLayout>
        <div class="recipe">
            <h1>{{ translations.recipe.create_recipe }}</h1>

            <form class="form-field">
                <section>
                    <InputField
                        v-model="form.name"
                        type="text"
                        :label="translations.fields.labels.name"
                        :max-length="50"
                        :error="form.errors.name"
                    />

                    <InputField
                        v-model="form.image_src"
                        type="file"
                        :label="translations.fields.labels.image"
                    />

                    <SelectField
                        v-model="form.visibility"
                        :items="visibilityOptions"
                        :label="translations.fields.labels.recipe.visibility"
                        placeholderValue="Select visibility"
                    />
                </section>

                <section>
                    <h3>Detaļas</h3>

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
                            min = 0
                            :label="translations.fields.labels.recipe.servings"
                            :error="form.errors.servings"
                        />
                    </div>
                </section>

                <section>
                    <div class="title">
                        <h3>{{ translations.recipe.ingredients }}</h3>

                        <button type="button" class="button" @click="addProduct">
                            +
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

                        <button type="button" class="button" @click="addInstruction">
                            +
                        </button>
                    </div>

                    <div v-for="(step, index) in form.instructions" :key="index" class="instruction-row">
                        <InputField
                            v-model="form.instructions[index]"
                            type="text"
                            :label="(index + 1) + '. ' + translations.fields.labels.recipe.step"
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

                <button class="button" @submit.prevent="submit">
                    {{translations.button.create}}
                </button>
            </form>
        </div>
    </MainLayout>
</template>
