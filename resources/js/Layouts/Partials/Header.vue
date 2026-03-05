<script setup>
import { onMounted, onUnmounted, ref, watch, computed } from 'vue'
import { router, usePage, Link} from '@inertiajs/vue3'
import NavBar from "../../Components/NavBar.vue";
import Avatar from "../../Components/Avatar.vue";
import Dropdown from "../../Components/Dropdown.vue";

const page = usePage();
const { translations, headerMenu, languagePage } = page.props;

const user = computed(() => page.props.user);
const avatarSrc = computed(() => page.props.user?.avatar_src);

const isMenuOpen = ref(false);
const isMobile = ref(false);

//Mobilā režīma noteikšana
let mediaQuery;
let mediaQueryHandler;

const setupMobile = () => {
    mediaQuery = window.matchMedia('(max-width: 768px)');
    isMobile.value = mediaQuery.matches;

    mediaQueryHandler = (event) => {
        isMobile.value = event.matches;
    }

    // Novēro ekrāna platuma izmaiņas
    mediaQuery.addEventListener('change', mediaQueryHandler);
}

//Mobilās izvēlnes vadība
const openMenu = () => (isMenuOpen.value = true);
const closeMenu = () => (isMenuOpen.value = false);

// Ja pārslēdzas uz darbavirsmu, aizver mobilo izvēlni
watch(isMobile, isMobileNow => {
    if (!isMobileNow) isMenuOpen.value = false;
});

// Bloķē lapas ritināšanu, kad izvēlne ir atvērta
watch(isMenuOpen, open => {
    document.body.style.overflow = open ? 'hidden' : '';
});

onMounted(() => {
    setupMobile();
    closeMenu();
});

onUnmounted(() => {
    // Noņem ekrāna izmēra klausītāju
    if (mediaQuery && mediaQueryHandler) {
        mediaQuery.removeEventListener('change', mediaQueryHandler);
    }
});
</script>


<template>
    <header :class="['container', 'site-header', { mobile: isMobile }]">
        <!--Logo/mājaslapas nosaukuma saite  uz sākumlapu-->
        <a :href="`/${languagePage.slug}/`">
            <strong>{{ languagePage.content.site_name }}</strong>
        </a>

        <!--Navigācijas josla darbvirsmas izmēra ekrāniem -->
        <NavBar
            v-if="!isMobile"
            :language-page="languagePage"
            :menu="headerMenu"
        />

        <!--Pieslēgšanās/izrakstīšanās pogas darbvirmas izmēra ekrāniem-->
        <div v-if="!isMobile && user !== null">
            <Dropdown :user="user" :avatar-src="avatarSrc">
                <template #trigger>
                    <Avatar :avatar-src="avatarSrc" />
                </template>

                <li>
                    <Link :href="route('recipe.my')">
                        <i class="pi pi-book" />
                        {{ translations.profile.recipes }}
                    </Link>
                </li>
                <li>
                    <Link :href="route('profile.edit')">
                        <i class="pi pi-user-edit" />
                        {{ translations.profile.edit_profile }}
                    </Link>
                </li>
                <li>
                    <Link :href="route('logout')" method="post">
                        <i class="pi pi-power-off" />
                        {{ translations.auth.logout }}
                    </Link>
                </li>
            </Dropdown>
        </div>

        <Link v-if="user === null" :href="route('login')" as="button" class="button primary">
            {{ translations.auth.login }}
        </Link>

        <!--Mobilās izvēlnes poga-->
        <button v-if="isMobile" @click="openMenu" aria-label="Open menu">
            <i class="pi pi-bars"></i>
        </button>

        <Transition
            enter-active-class="animate__animated animate__slideInRight"
            leave-active-class="animate__animated animate__slideOutRight"
        >
            <!-- Mobilās izvēlnes pārklājums -->
            <div v-if="isMobile && isMenuOpen" class="menu-overlay">
                <header>
                    <strong>{{ languagePage.content.site_name }}</strong>
                    <button @click="closeMenu">
                        <i class="pi pi-times"></i>
                    </button>
                </header>

                <!-- Mobilā navigācijas josla -->
                <NavBar
                    :language-page="languagePage"
                    :menu="headerMenu"
                />

                <!--Pieslēgšanās/izrakstīšanās mobilās pogas-->
                <button v-if="user" @click="logout" class="button primary">
                    {{ translations.auth.logout }}
                </button>
                <button v-else @click="login" class="button primary">
                    {{ translations.auth.login }}
                </button>
            </div>
        </Transition>
    </header>
</template>
