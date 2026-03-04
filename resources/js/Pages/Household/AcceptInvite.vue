<script setup>
import {router, usePage, Link} from '@inertiajs/vue3';
import AuthLayout from '../../Layouts/Auth.vue';

const {translations} = usePage().props;

const props = defineProps({
    invitation: Object,
    isLoggedIn: Boolean,
    emailMatches: Boolean,
});

function accept() {
    router.post(route('households.invite.email.accept', props.invitation.token));
}
</script>

<template>
    <AuthLayout>
        <div style="display: flex; flex-direction: column; align-items: center;">
            <h1>Pievienojies {{ invitation.household.name }}</h1>
            <p>Tevi uzaicināja {{ invitation.inviter.name }}</p>

            <div v-if="isLoggedIn && emailMatches">
                <p>Akceptēt kā {{ invitation.email }}?</p>
                <button class="button" @click="accept">
                    {{ translations.button.join }}
                </button>
            </div>

            <div v-else-if="isLoggedIn && !emailMatches">
                <p>Tu esi pievienojies ar citu e-pastu. Tevi uzaicināja uz šo e-pastu {{ invitation.email }}.</p>
                <button class="button" @click="accept">
                    {{ translations.button.join_anyway }}
                </button>
            </div>

            <div v-else>
                <p>Pievienojies vai izveido jaunu kontu.</p>

                <Link :href="`/login?email=${invitation.email}&invite=${invitation.token}`" class="button" as="button">
                    {{ translations.auth.login }}
                </Link>

                <Link :href="`/register?email=${invitation.email}&invite=${invitation.token}`" class="button" as="butotn">
                    {{ translations.button.create }} Kontu
                </Link>
            </div>
        </div>
    </AuthLayout>
</template>
