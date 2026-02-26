<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { router, usePage, Link} from '@inertiajs/vue3'
import NavBar from "../../Components/NavBar.vue";

const { translations, headerMenu, languagePage, auth } = usePage().props;

const isMenuOpen = ref(false);
const isMobile = ref(false);

// Darbvirsmas nolaižamā izvēlne
const openDropdown = ref(false);

const toggleDropdown = () => {
    openDropdown.value = !openDropdown.value;

    if (openDropdown.value) {
        setTimeout(() => document.addEventListener('click', closeOnOutside), 0);
    }
}

const closeOnOutside = () => {
    const dropdown = document.querySelector('.dropdown');
    if (dropdown && dropdown.contains(e.target)) return;

    openDropdown.value = false;
    document.removeEventListener('click', closeOnOutside);
}

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

    router.on('start', () => {
        openDropdown.value = false;
    });
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
            :auth="auth"
            :language-page="languagePage"
            :menu="headerMenu"
        />

        <!--Pieslēgšanās/izrakstīšanās pogas darbvirmas izmēra ekrāniem-->
        <div v-if="!isMobile" class="profile">
            <button
                v-if="auth.user"
                @click="toggleDropdown"
                class="button round primary"
            >
                <i class="pi pi-user"></i>
            </button>

            <!-- Nolaižamajā izvēlnē -->
            <ul v-if="openDropdown" class="dropdown">
                <li class="user-info">
                    <p><strong>{{ auth.user.name }}</strong></p>
                    <p>@{{ auth.user.username }}</p>
                </li>
                <li>
                    <i class="pi pi-user-edit"/>
                    <Link :href="route('profile.edit')">
                        {{ translations.profile.edit_profile}}
                    </Link>
                </li>
                <li>
                    <i class="pi pi-power-off"/>
                    <Link :href="route('logout')" method="post">
                        {{ translations.auth.logout }}
                    </Link>
                </li>
            </ul>
        </div>

        <Link v-if="!auth.user" :href="route('login')" as="button" class="button primary">
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
                    :auth="auth"
                    :language-page="languagePage"
                    :menu="headerMenu"
                />

                <!--Pieslēgšanās/izrakstīšanās mobilās pogas-->
                <button v-if="auth.user" @click="logout" class="button primary">
                    {{ translations.auth.logout }}
                </button>
                <button v-else @click="login" class="button primary">
                    {{ translations.auth.login }}
                </button>
            </div>
        </Transition>
    </header>
</template>
