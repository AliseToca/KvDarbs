<script setup>
import {onMounted, onUnmounted, ref, watch, computed} from 'vue';
import {usePage, Link} from '@inertiajs/vue3';
import NavBar from "../../Components/NavBar.vue";
import UserActionsDropdown from "../../Components/Dropdowns/UserActionsDropdown.vue";
import Avatar from "../../Components/Avatar.vue";
import UserActions from "../../Components/UserActions.vue";
import Logo from "../../Components/Logo.vue";

const {translations, headerMenu, languagePage} = usePage().props;

// Reactive reference to the current authenticated user
const user = computed(() => usePage().props.user);

const isMenuOpen = ref(false);
const isMobile = ref(false);

// Store media query and its handler so we can remove the listener on unmount
let mediaQuery;
let mediaQueryHandler;

const setupMobile = () => {
    mediaQuery = window.matchMedia('(max-width: 768px)');
    isMobile.value = mediaQuery.matches;

    mediaQueryHandler = (event) => {
        isMobile.value = event.matches;
    }

    // Listen for screen width changes
    mediaQuery.addEventListener('change', mediaQueryHandler);
}

// Mobile menu controls
const openMenu = () => (isMenuOpen.value = true);
const closeMenu = () => (isMenuOpen.value = false);

// Close mobile menu when switching to desktop
watch(isMobile, isMobileNow => {
    if (!isMobileNow) isMenuOpen.value = false;
});

// Lock page scroll when mobile menu is open
let scrollY = 0;

watch(isMenuOpen, open => {
    if (open) {
        scrollY = window.scrollY;
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollY}px`;
        document.body.style.width = '100%';
    } else {
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        window.scrollTo(0, scrollY); // restore scroll position
    }
});

onMounted(() => {
    setupMobile();
    closeMenu();
});

onUnmounted(() => {
    // Clean up screen size listener to avoid memory leaks
    if (mediaQuery && mediaQueryHandler) {
        mediaQuery.removeEventListener('change', mediaQueryHandler);
    }
});
</script>

<template>
    <header :class="['container', 'site-header', { mobile: isMobile }]">
        <!-- Logo / site name link to homepage -->
        <Logo
            :siteName=languagePage.content.site_name
            :siteNameAccent=languagePage.content.site_name_accent
            :href=languagePage.slug
        />

        <!-- Desktop navigation bar -->
        <NavBar
            v-if="!isMobile"
            :language-page="languagePage"
            :menu="headerMenu"
        />

        <!-- Desktop user actions dropdown (login/logout/profile) -->
        <UserActionsDropdown v-if="!isMobile && user !== null"/>

        <!-- Mobile menu open button (hamburger icon) -->
        <button v-if="isMobile && user !== null" @click="openMenu">
            <i class="pi pi-bars"></i>
        </button>

        <Transition
            enter-active-class="animate__animated animate__slideInRight"
            leave-active-class="animate__animated animate__slideOutRight"
        >
            <!-- Mobile menu fullscreen overlay -->
            <div v-if="isMobile && isMenuOpen" class="menu-overlay">
                <!-- Overlay header with logo and close button -->
                <header>
                    <Logo
                        :siteName=languagePage.content.site_name
                        :siteNameAccent=languagePage.content.site_name_accent
                        :href=languagePage.slug
                    />
                    <button @click="closeMenu">
                        <i class="pi pi-times"></i>
                    </button>
                </header>

                <div class="menu-content">
                    <!-- Logged in user info (avatar, name, username) -->
                    <div class="user-info">
                        <Avatar :avatarSrc="user.avatar_src" large/>
                        <h3> {{ user.name }} </h3>
                        <span> @{{ user.username }} </span>
                    </div>

                    <!-- Mobile navigation links -->
                    <NavBar
                        :language-page="languagePage"
                        :menu="headerMenu"
                    />

                    <!-- Additional user actions (profile, settings, etc.) -->
                    <nav>
                        <ul>
                            <UserActions/>
                        </ul>
                    </nav>
                </div>

                <!-- Mobile logout button -->
                <Link v-if="user !== null" :href="route('logout')" method="post" as="button" class="button primary full-width">
                    <i class="pi pi-power-off"/>
                    {{ translations.auth.logout }}
                </Link>
            </div>
        </Transition>
    </header>
</template>
