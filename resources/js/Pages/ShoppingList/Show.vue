<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage, useForm, router } from "@inertiajs/vue3";
import InputField from "../../Components/Inputs/InputField.vue";
import {ref} from "vue";

const { translations, page_name, shopping_list: rawList } = usePage().props;
const shopping_list = ref([...rawList]);

console.log(translations);
const form = useForm({ name: '' });

function submit() {
    if (!form.name.trim()) return;
    form.post(route('shopping-list.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // sync from server after add (we don't know the new item's id yet)
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
</script>

<template>
    <MainLayout>
        <h1>{{ page_name }}</h1>

        <section class="shopping-list-wrapper">
            <form class="form-field" @submit.prevent="submit">
                <InputField
                    v-model="form.name"
                    :label="translations.shopping_list.add_to_list"
                    :maxLength="40"
                />
                <button type="submit" class="button primary">
                    <i class="pi pi-plus"/>
                </button>

            </form>

            <ul class="shopping-list">
                <li
                    v-for="item in shopping_list"
                    :key="item.id"
                    class="shopping-list-item"
                    :class="{ 'shopping-list-item--checked': item.is_checked }"
                >
                    <label>
                        <input
                            type="checkbox"
                            class="checkbox"
                            :checked="item.is_checked"
                            @change="toggle(item)"
                        />
                        {{ item.name }}
                    </label>

                    <button type="button" @click="remove(item)">
                        <i class="pi pi-times"/>
                    </button>
                </li>
            </ul>
        </section>
    </MainLayout>
</template>
