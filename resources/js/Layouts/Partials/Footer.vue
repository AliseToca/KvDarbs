<script setup>
import {Link, usePage} from "@inertiajs/vue3";
import NavBar from "../../Components/NavBar.vue";
import UserActions from "../../Components/UserActions.vue";

const { footerMenu, languagePage, auth, translations} = usePage().props;
</script>

<template>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-info">
                <h3>{{languagePage.content.site_name}}</h3>

                <div class="footer-socials">
                    <Link class="footer-socials-link"><i class="pi pi-facebook"/></Link>
                    <Link  class="footer-socials-link"><i class="pi pi-instagram"/></Link>
                    <Link class="footer-socials-link"><i class="pi pi-twitter"/></Link>
                </div>
            </div>
            <div class="footer-links">
                <div class="footer-links-column">
                    <h3 class="footer-links-column-title">{{ translations.navigation.footer.links }}</h3>

                    <NavBar
                        class="footer-links-column-list"
                        :menu="footerMenu"
                        :languagePage="languagePage"
                        :auth="auth"
                    />
                </div>

                <div class="footer-links-column">
                    <h3 class="footer-links-column-title">{{ translations.navigation.footer.profile }}</h3>

                    <ul class="footer-links-column-list">
                        <UserActions :showIcons="false"/>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</template>
