<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage, useForm, router} from "@inertiajs/vue3";
import InputField from "../../Components/Inputs/InputField.vue";
import {computed, ref} from "vue";

const {translations, page_name, shopping_list: rawList, household} = usePage().props;
const shopping_list = ref([...rawList]);

const form = useForm({name: ''});

function submit() {
    if (!form.name.trim()) return;
    form.post(route('shopping-list.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            shopping_list.value = usePage().props.shopping_list;
            form.reset('name');
        },
    });
}

function toggle(item) {
    item.is_checked = !item.is_checked;
    router.patch(route('shopping-list.toggle', item.id), {}, {
        preserveScroll: true,
        preserveState: true,
    });
}

function remove(item) {
    const index = shopping_list.value.indexOf(item);
    shopping_list.value.splice(index, 1);

    router.delete(route('shopping-list.destroy', item.id), {
        preserveScroll: true,
        preserveState: true,
    });
}

const unchecked = computed(() => shopping_list.value.filter(item => !item.is_checked));
const checked = computed(() => shopping_list.value.filter(item => item.is_checked));
const total = computed(() => unchecked.value.length + checked.value.length);
</script>

<template>
    <MainLayout>
        <header class="shopping-list-header">
            <h1>{{ page_name }}</h1>

            <div class="shopping-list-header-info">
                <span>
                    {{ translations.household.name }}s
                    <strong>{{ household.name }}</strong>
                </span>

                <span class="divider">•</span>

                <span>{{ total }} {{ translations.household.products.plural }}</span>
            </div>
        </header>

        <section class="shopping-list-wrapper">
            <form class="form-field" @submit.prevent="submit">
                <InputField
                    v-model="form.name"
                    :placeholderValue="translations.shopping_list.add_to_list"
                    :maxLength="40"
                    class="input-field"
                />
                <button type="submit" class="button primary">
                    <i class="pi pi-plus"/>
                </button>
            </form>

            <ul class="shopping-list">
                <li v-if="unchecked.length" class="shopping-list-divider">
                    {{ translations.shopping_list.to_purchase }} ({{ unchecked.length }})
                </li>

                <li
                    v-for="item in unchecked"
                    :key="item.id"
                    class="shopping-list-item"
                >
                    <label>
                        <input type="checkbox" class="checkbox" :checked="item.is_checked" @change="toggle(item)"/>
                        {{ item.name }}
                    </label>
                    <button type="button" @click="remove(item)"><i class="pi pi-times"/></button>
                </li>

                <li v-if="checked.length" class="shopping-list-divider">
                    {{ translations.shopping_list.purchased }} ({{ checked.length }})
                </li>

                <li
                    v-for="item in checked"
                    :key="item.id"
                    class="shopping-list-item shopping-list-item--checked"
                >
                    <label>
                        <input type="checkbox" class="checkbox" :checked="item.is_checked" @change="toggle(item)"/>
                        {{ item.name }}
                    </label>
                    <button type="button" @click="remove(item)"><i class="pi pi-times"/></button>
                </li>
            </ul>
        </section>
    </MainLayout>
</template>
