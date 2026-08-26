<script setup>
import { ref } from 'vue';
import { Menu, X, UserRound } from 'lucide-vue-next';

const mobileOpen = ref(false);

const closeMobileMenu = () => {
    mobileOpen.value = false;
};
</script>

<template>
    <header class="relative z-50 w-full">

        <!-- =========================================================
             TOP INFORMATION BAR
        ========================================================== -->
        <div class="hidden bg-[#00351F] text-white lg:block">
            <div
                class="mx-auto flex h-9 max-w-[1440px] items-center justify-between px-6 text-xs"
            >
                <div class="flex items-center gap-6">
                    <span>BBHS Old Boys Association</span>

                    <span class="text-white/50">|</span>

                    <span>Abeokuta, Ogun State, Nigeria</span>
                </div>

                <a
                    href="/login"
                    class="flex items-center gap-2 transition-colors hover:text-[#F1D36A]"
                >
                    <UserRound :size="14" />

                    <span>Member Login</span>
                </a>
            </div>
        </div>


        <!-- =========================================================
             MAIN NAVIGATION
        ========================================================== -->
        <div class="border-b border-gray-200 bg-white">

            <div
                class="mx-auto flex h-[84px] max-w-[1440px] items-center justify-between px-5 lg:px-8"
            >

                <!-- =================================================
                     BRAND / LOGO
                ================================================== -->
                <a
                    href="/"
                    class="flex min-w-0 items-center gap-3"
                    @click="closeMobileMenu"
                >

                    <img
                        :src="'/images/bbhs/logo/bbhs-logo.png'"
                        alt="Baptist Boys' High School Old Boys Association"
                        class="h-16 w-16 shrink-0 object-contain"
                    >

                    <div class="hidden min-w-0 sm:block">

                        <div
                            class="truncate text-base font-extrabold leading-tight tracking-wide text-[#00351F] lg:text-lg"
                        >
                            BAPTIST BOYS' HIGH SCHOOL
                        </div>

                        <div
                            class="mt-0.5 text-[10px] font-bold uppercase tracking-[0.18em] text-[#087A3A] lg:text-xs"
                        >
                            OLD BOYS' ASSOCIATION
                        </div>

                        <div
                            class="mt-0.5 text-[9px] font-medium uppercase tracking-[0.16em] text-gray-500 lg:text-[10px]"
                        >
                            Abeokuta
                        </div>

                    </div>

                </a>


                <!-- =================================================
                     DESKTOP NAVIGATION
                ================================================== -->
                <nav class="hidden items-center gap-5 lg:flex xl:gap-6">

                    <a
                        href="/"
                        class="text-sm font-semibold text-gray-700 transition-colors hover:text-[#087A3A]"
                    >
                        Home
                    </a>

                    <a
                        href="/about"
                        class="text-sm font-semibold text-gray-700 transition-colors hover:text-[#087A3A]"
                    >
                        About
                    </a>

                    <a
                        href="/alumni"
                        class="text-sm font-semibold text-gray-700 transition-colors hover:text-[#087A3A]"
                    >
                        Alumni
                    </a>

                    <a
                        href="/events"
                        class="text-sm font-semibold text-gray-700 transition-colors hover:text-[#087A3A]"
                    >
                        Events
                    </a>

                    <a
                        href="/news"
                        class="text-sm font-semibold text-gray-700 transition-colors hover:text-[#087A3A]"
                    >
                        News & Media
                    </a>

                    <a
                        href="/projects"
                        class="text-sm font-semibold text-gray-700 transition-colors hover:text-[#087A3A]"
                    >
                        Projects
                    </a>

                    <a
                        href="/resources"
                        class="text-sm font-semibold text-gray-700 transition-colors hover:text-[#087A3A]"
                    >
                        Resources
                    </a>

                    <!-- Join button -->
                    <a
                        href="/membership"
                        class="ml-1 inline-flex items-center justify-center rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-bold text-[#00351F] shadow-sm transition-all hover:bg-[#E5B93F] hover:shadow-md"
                    >
                        JOIN NOW
                    </a>

                </nav>


                <!-- =================================================
                     MOBILE MENU BUTTON
                ================================================== -->
                <button
                    type="button"
                    class="rounded-lg p-2 text-[#00351F] transition-colors hover:bg-gray-100 lg:hidden"
                    @click="mobileOpen = !mobileOpen"
                    :aria-expanded="mobileOpen"
                    aria-label="Toggle navigation menu"
                >
                    <X
                        v-if="mobileOpen"
                        :size="28"
                        stroke-width="2"
                    />

                    <Menu
                        v-else
                        :size="28"
                        stroke-width="2"
                    />
                </button>

            </div>

        </div>


        <!-- =========================================================
             MOBILE NAVIGATION
        ========================================================== -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-[-10px] opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-[-10px] opacity-0"
        >

            <div
                v-if="mobileOpen"
                class="border-b border-gray-200 bg-white shadow-xl lg:hidden"
            >

                <nav class="mx-auto max-w-[1440px] px-5 py-5">

                    <div class="space-y-1">

                        <a
                            href="/"
                            @click="closeMobileMenu"
                            class="block rounded-lg px-4 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 hover:text-[#087A3A]"
                        >
                            Home
                        </a>

                        <a
                            href="/about"
                            @click="closeMobileMenu"
                            class="block rounded-lg px-4 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 hover:text-[#087A3A]"
                        >
                            About
                        </a>

                        <a
                            href="/alumni"
                            @click="closeMobileMenu"
                            class="block rounded-lg px-4 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 hover:text-[#087A3A]"
                        >
                            Alumni
                        </a>

                        <a
                            href="/events"
                            @click="closeMobileMenu"
                            class="block rounded-lg px-4 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 hover:text-[#087A3A]"
                        >
                            Events
                        </a>

                        <a
                            href="/news"
                            @click="closeMobileMenu"
                            class="block rounded-lg px-4 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 hover:text-[#087A3A]"
                        >
                            News & Media
                        </a>

                        <a
                            href="/projects"
                            @click="closeMobileMenu"
                            class="block rounded-lg px-4 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 hover:text-[#087A3A]"
                        >
                            Projects
                        </a>

                        <a
                            href="/resources"
                            @click="closeMobileMenu"
                            class="block rounded-lg px-4 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 hover:text-[#087A3A]"
                        >
                            Resources
                        </a>

                    </div>


                    <!-- Mobile actions -->
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">

                        <a
                            href="/login"
                            @click="closeMobileMenu"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#087A3A] px-5 py-3 font-semibold text-[#087A3A] transition-colors hover:bg-[#087A3A] hover:text-white"
                        >
                            <UserRound :size="18" />

                            Member Login
                        </a>

                        <a
                            href="/membership"
                            @click="closeMobileMenu"
                            class="inline-flex items-center justify-center rounded-lg bg-[#D4A72C] px-5 py-3 font-bold text-[#00351F] transition-colors hover:bg-[#E5B93F]"
                        >
                            JOIN NOW
                        </a>

                    </div>

                </nav>

            </div>

        </Transition>

    </header>
</template>