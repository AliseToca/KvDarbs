<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const { props } = usePage()
const translations = props.translations
const { headerMenu, languagePage, auth } = usePage().props

const isMenuOpen = ref(false)
const isMobile = ref(false)

let mediaQuery

const setupMobile = () => {
    mediaQuery = window.matchMedia('(max-width: 768px)')
    isMobile.value = mediaQuery.matches

    mediaQuery.addEventListener('change', e => {
        isMobile.value = e.matches
    })
}

const openMenu = () => (isMenuOpen.value = true)
const closeMenu = () => (isMenuOpen.value = false)


watch(isMobile, mobile => {
    if (!mobile) isMenuOpen.value = false
})

watch(isMenuOpen, open => {
    document.body.style.overflow = open ? 'hidden' : ''
})

onMounted(() => {
    setupMobile()
    closeMenu()
})

onUnmounted(() => {
    mediaQuery?.removeEventListener('change')
})

const login = () => router.get('/login')
const logout = () => router.post('/logout')
</script>


<template>
    <header :class="['site-header', { mobile: isMobile }]">
        <a :href="`/${languagePage.slug}/`">
            <strong>{{ languagePage.content.site_name }}</strong>
        </a>

        <nav v-if="!isMobile && headerMenu.length && auth.user">
            <ul>
                <li v-for="item in headerMenu" :key="item.id">
                    <a :href="`/${languagePage.slug}/${item.page.slug}`">
                        {{ item.page.name }}
                    </a>
                </li>
            </ul>
        </nav>

        <div v-if="!isMobile">
            <button v-if="auth.user" @click="logout" class="button primary">
                {{ translations.auth.logout }}
            </button>
            <button v-else @click="login" class="button primary">
                {{ translations.auth.login }}
            </button>
        </div>

        <button v-if="isMobile" @click="openMenu" aria-label="Open menu">
            <i class="pi pi-bars"></i>
        </button>

        <Transition
            enter-active-class="animate__animated animate__slideInRight"
            leave-active-class="animate__animated animate__slideOutRight"
        >
            <div v-if="isMobile && isMenuOpen" class="menu-overlay">
                <header>
                    <strong>{{ languagePage.content.site_name }}</strong>
                    <button @click="closeMenu">
                        <i class="pi pi-times"></i>
                    </button>
                </header>

                <nav v-if="headerMenu.length && auth.user">
                    <ul>
                        <li v-for="item in headerMenu" :key="item.id">
                            <a :href="`/${languagePage.slug}/${item.page.slug}`">
                                {{ item.page.name }}
                            </a>
                        </li>
                    </ul>
                </nav>

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
