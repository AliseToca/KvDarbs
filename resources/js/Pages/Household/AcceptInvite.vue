<script setup>
import {router, usePage, Link} from '@inertiajs/vue3';
import AuthLayout from '../../Layouts/Auth.vue';

// Get global translations from Inertia shared props
const {translations} = usePage().props;

const props = defineProps({
    invitation: Object,  // Invitation data: token, email, household, inviter
    isLoggedIn: Boolean, // Whether the current user is authenticated
    emailMatches: Boolean, // Whether the logged-in user's email matches the invitation email
});

// Accept the invitation via POST request using the invitation token
function accept() {
    router.post(route('households.invite.email.accept', props.invitation.token));
}
</script>

<template>
    <AuthLayout>
        <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
            <h1>Pievienojies mājsaimniecībā "{{ invitation.household.name }}"</h1>
            <p>Tevi uzaicināja {{ invitation.inviter.name }}</p>

            <!-- Case 1: User is logged in and their email matches the invitation -->
            <div v-if="isLoggedIn && emailMatches" class="button-container">
                <p>Akceptēt kā {{ invitation.email }}?</p>
                <button class="button" @click="accept">
                    {{ translations.button.join }}
                </button>
            </div>

            <!-- Case 2: User is logged in but with a different email than the invitation -->
            <div v-else-if="isLoggedIn && !emailMatches" style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                <p>Tu esi pievienojies ar citu e-pastu. Tevi uzaicināja uz šo e-pastu {{ invitation.email }}.</p>
                <!-- Still allow joining despite the email mismatch -->
                <button class="button" @click="accept">
                    {{ translations.button.join_anyway }}
                </button>
            </div>

            <!-- Case 3: User is not logged in — prompt to log in or register -->
            <div v-else>
                <p>Pievienojies vai izveido jaunu kontu.</p>

                <div class="button-container">
                    <!-- Pre-fill email and pass invite token so it's handled after login -->
                    <Link :href="`/login?email=${invitation.email}&invite=${invitation.token}`" class="button primary" as="button">
                        {{ translations.auth.login }}
                    </Link>

                    <!-- Pre-fill email and pass invite token so it's handled after registration -->
                    <Link :href="`/register?email=${invitation.email}&invite=${invitation.token}`" class="button" as="butotn">
                        {{ translations.auth.register }}
                    </Link>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
