<script setup>
import {useForm, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import MainLayout from '../../Layouts/Main.vue';
import InputField from "../../Components/Inputs/InputField.vue";
import Breadcrumb from "../../Components/Breadcrumb.vue";
import Select from "../../Components/Inputs/SelectField.vue";
import DeleteHouseholdUserModal from "../../Components/Modals/DeleteHouseholdUserModal.vue";

const {translations, breadcrumbs, household, enums, user} = usePage().props;

const household_users = computed(() => usePage().props.household_users);

const roles = enums.roles;

const showDeleteModal = ref(false);
const selectedMember = ref(null);

function openDeleteModal(member) {
    selectedMember.value = member;
    showDeleteModal.value = true;
}

const nameForm = useForm({
    name: household?.name ?? '',
});

const usersForm = useForm({
    users: household_users.value.map(member => ({
        id: member.id,
        role: member.role,
    })),
});

function submitName() {
    nameForm.put(route('households.update', household.id), {
        preserveScroll: true,
    });
}

function submitRoles() {
    usersForm.patch(route('households.users.update', household.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <MainLayout>
        <section class="household">
            <Breadcrumb :items="breadcrumbs"/>

            <header>
                <h1>{{ translations.household.your }}</h1>
            </header>

            <section>
                <form class="form-field" @submit.prevent="submitName">
                    <InputField
                        v-model="nameForm.name"
                        type="text"
                        :label="translations.fields.labels.name"
                        :error="nameForm.errors.name"
                        :max-length="30"
                    />
                    <button class="button" type="submit" :disabled="nameForm.processing">
                        {{ translations.button.save }}
                    </button>
                </form>
            </section>

            <section>
                <h3>{{ translations.household.users }}</h3>

                <form class="form-field" @submit.prevent="submitRoles">
                    <ul class="household-user-list">
                        <li v-for="(member, index) in household_users" :key="member.id">
                            <span>@{{ member.username }}</span>
                            <div class="user-actions">
                                <Select
                                    v-model="usersForm.users[index].role"
                                    :label="translations.fields.labels.role"
                                    :items="roles"
                                />
                                <button
                                    :disabled="member.username === user.username"
                                    class="button primary"
                                    type="button"
                                    @click="openDeleteModal(member)"
                                >
                                    <i class="pi pi-trash"/>
                                </button>
                            </div>
                        </li>
                    </ul>
                    <button class="button" type="submit" :disabled="usersForm.processing">
                        {{ translations.button.save }}
                    </button>
                </form>
            </section>

            <DeleteHouseholdUserModal
                v-model="showDeleteModal"
                :member="selectedMember"
                :household="household"
            />
        </section>
    </MainLayout>
</template>
