<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { router, usePage, Link} from '@inertiajs/vue3'
import NavBar from "../../Components/NavBar.vue";

const { props } = usePage()
const translations = props.translations
const { headerMenu, languagePage, auth } = usePage().props

const isMenuOpen = ref(false)
const isMobile = ref(false)

//Mobilā režīma noteikšana
let mediaQuery
let mediaQueryHandler

const setupMobile = () => {
    mediaQuery = window.matchMedia('(max-width: 768px)')
    isMobile.value = mediaQuery.matches

    mediaQueryHandler = (event) => {
        isMobile.value = event.matches
    }

    // Novēro ekrāna platuma izmaiņas
    mediaQuery.addEventListener('change', mediaQueryHandler)
}

//Mobilās izvēlnes vadība
const openMenu = () => (isMenuOpen.value = true)
const closeMenu = () => (isMenuOpen.value = false)

// Ja pārslēdzas uz darbavirsmu, aizver mobilo izvēlni
watch(isMobile, isMobileNow => {
    if (!isMobileNow) isMenuOpen.value = false
})

// Bloķē lapas ritināšanu, kad izvēlne ir atvērta
watch(isMenuOpen, open => {
    document.body.style.overflow = open ? 'hidden' : ''
})

onMounted(() => {
    setupMobile()
    closeMenu()
})

onUnmounted(() => {
    // Noņem ekrāna izmēra klausītāju
    if (mediaQuery && mediaQueryHandler) {
        mediaQuery.removeEventListener('change', mediaQueryHandler)
    }
})

const login = () => router.get('/login')
const logout = () => router.post('/logout')
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
        <div v-if="!isMobile">
            <Link v-if="auth.user" :href="route('profile.edit')" as="button" class="button round primary">
                <i class="pi pi-user"></i>
            </Link>

<!--            <button v-if="auth.user" @click="logout" class="button round primary">-->
<!--                {{ translations.auth.logout }}-->
<!--            </button>-->
            <button v-else @click="login" class="button primary">
                {{ translations.auth.login }}
            </button>
        </div>

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
