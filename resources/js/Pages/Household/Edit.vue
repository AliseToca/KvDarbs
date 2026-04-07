<script setup>
import {useForm, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import MainLayout from '../../Layouts/Main.vue';
import InputField from "../../Components/Inputs/InputField.vue";
import Breadcrumb from "../../Components/Breadcrumb.vue";
import Select from "../../Components/Inputs/SelectField.vue";
import ConfirmDeleteModal from "../../Components/Modals/ConfirmDeleteModal.vue";
import Avatar from "../../Components/Avatar.vue";

const {translations, breadcrumbs, household, enums, user} = usePage().props;

const household_users = computed(() => usePage().props.household_users);

const roles = enums.roles;

const showDeleteHouseholdModal = ref(false);
const showDeleteUserModal = ref(false);
const selectedMember = ref(null);

function openDeleteModal(member) {
    selectedMember.value = member;
    showDeleteUserModal.value = true;
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

            <header class="household-header">
                <h1>{{ translations.household.your }}</h1>

                <button
                    class="button primary"
                    @click="showDeleteHouseholdModal = true"
                >
                    <i class="pi pi-trash"/>
                </button>
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
                            <div class="user-info">
                                <Avatar :avatarSrc="member.avatar_src" small/>
                                <span>{{ member.username }}</span>
                            </div>
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

            <ConfirmDeleteModal
                v-model="showDeleteUserModal"
                :title=translations.auth.from_user
                :message="`${translations.household.delete_message.ask_confirmation} <strong>${selectedMember?.username}</strong> ${translations.household.delete_message.from_household}`"
                route-name="households.users.destroy"
                :route-param="{ household: household.id, user: selectedMember?.id }"
            />

            <ConfirmDeleteModal
                v-model="showDeleteHouseholdModal"
                :title="translations.household.delete_message.household"
                :message="`${translations.household.delete_message.ask_confirmation} &quot;<strong>${household.name}</strong>&quot;?`"
                route-name="households.destroy"
                :route-param="household.id"
            />
        </section>
    </MainLayout>
</template>
