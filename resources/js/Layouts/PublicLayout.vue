<script setup>
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import {
    Menu,
    X,
    UserRound,
    LayoutDashboard,
    LogOut,
    ShieldCheck,
} from 'lucide-vue-next';

const page = usePage();
const mobileOpen = ref(false);

const user = computed(() => page.props.auth?.user ?? null);
const isLoggedIn = computed(() => !!user.value);
const isAdmin = computed(() => !!user.value?.is_admin);

const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout', {
        onFinish: () => {
            mobileOpen.value = false;
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-white">

        <!-- TOP BAR -->
        <div class="bg-[#00351F] text-white">
            <div class="mx-auto flex min-h-[38px] max-w-[1440px] items-center justify-between px-5 text-xs lg:px-8">
                <div class="flex items-center gap-5">
                    <span>BBHS Old Boys Association</span>
                    <span class="hidden text-white/40 sm:inline">|</span>
                    <span class="hidden sm:inline">Abeokuta, Ogun State, Nigeria</span>
                </div>

                <div>
                    <a
                        v-if="!isLoggedIn"
                        href="/login"
                        class="inline-flex items-center gap-1.5 font-semibold hover:text-[#F1D36A]"
                    >
                        <UserRound :size="14" />
                        Member Login
                    </a>

                    <a
                        v-else-if="!isAdmin"
                        href="/dashboard"
                        class="inline-flex items-center gap-1.5 font-semibold hover:text-[#F1D36A]"
                    >
                        <LayoutDashboard :size="14" />
                        Member Dashboard
                    </a>

                    <a
                        v-else
                        href="/admin/dashboard"
                        class="inline-flex items-center gap-1.5 font-semibold hover:text-[#F1D36A]"
                    >
                        <ShieldCheck :size="14" />
                        Admin Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN HEADER -->
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex min-h-[84px] max-w-[1440px] items-center justify-between px-5 lg:px-8">

                <a href="/" class="flex items-center gap-3" @click="mobileOpen = false">
                    <img
                        :src="'/images/bbhs/logo/bbhs-logo.png'"
                        alt="BBHS Old Boys Association"
                        class="h-16 w-16 object-contain"
                    />

                    <div class="hidden sm:block">
                        <div class="text-base font-extrabold tracking-wide text-[#00351F] lg:text-lg">
                            BAPTIST BOYS' HIGH SCHOOL
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-[#087A3A] lg:text-xs">
                            OLD BOYS' ASSOCIATION
                        </div>
                        <div class="text-[9px] uppercase tracking-[0.16em] text-gray-500 lg:text-[10px]">
                            ABEOKUTA
                        </div>
                    </div>
                </a>

                <!-- DESKTOP -->
                <nav class="hidden items-center gap-5 lg:flex xl:gap-6">
                    <a href="/" class="font-semibold text-gray-700 hover:text-[#087A3A]">Home</a>
                    <a href="/about" class="font-semibold text-gray-700 hover:text-[#087A3A]">About</a>
                    <a href="/alumni" class="font-semibold text-gray-700 hover:text-[#087A3A]">Alumni</a>
                    <a href="/events" class="font-semibold text-gray-700 hover:text-[#087A3A]">Events</a>
                    <a href="/news" class="font-semibold text-gray-700 hover:text-[#087A3A]">News & Media</a>
                    <a href="/projects" class="font-semibold text-gray-700 hover:text-[#087A3A]">Projects</a>
                    <a href="/resources" class="font-semibold text-gray-700 hover:text-[#087A3A]">Resources</a>

                    <a
                        v-if="!isLoggedIn"
                        href="/membership"
                        class="rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-extrabold text-[#00351F] hover:bg-[#E5B93F]"
                    >
                        JOIN NOW
                    </a>

                    <template v-else-if="!isAdmin">
                        <a
                            href="/dashboard"
                            class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-4 py-3 text-sm font-bold text-white"
                        >
                            <LayoutDashboard :size="16" />
                            DASHBOARD
                        </a>

                        <button
                            type="button"
                            :disabled="logoutForm.processing"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-3 text-sm font-bold text-gray-700 hover:border-[#006B3C] hover:text-[#006B3C] disabled:opacity-50"
                            @click="logout"
                        >
                            <LogOut :size="16" />
                            LOGOUT
                        </button>
                    </template>

                    <template v-else>
                        <a
                            href="/admin/dashboard"
                            class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-4 py-3 text-sm font-bold text-white"
                        >
                            <ShieldCheck :size="16" />
                            ADMIN
                        </a>

                        <button
                            type="button"
                            :disabled="logoutForm.processing"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-3 text-sm font-bold text-gray-700 hover:border-[#006B3C] hover:text-[#006B3C] disabled:opacity-50"
                            @click="logout"
                        >
                            <LogOut :size="16" />
                            LOGOUT
                        </button>
                    </template>
                </nav>

                <button
                    type="button"
                    class="rounded-lg p-2 text-[#00351F] lg:hidden"
                    @click="mobileOpen = !mobileOpen"
                    aria-label="Toggle menu"
                >
                    <X v-if="mobileOpen" :size="27" />
                    <Menu v-else :size="27" />
                </button>
            </div>

            <!-- MOBILE -->
            <div v-if="mobileOpen" class="border-t border-gray-100 bg-white shadow-lg lg:hidden">
                <nav class="space-y-1 px-5 py-5">
                    <a href="/" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">Home</a>
                    <a href="/about" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">About</a>
                    <a href="/alumni" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">Alumni</a>
                    <a href="/events" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">Events</a>
                    <a href="/news" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">News & Media</a>
                    <a href="/projects" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">Projects</a>
                    <a href="/resources" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">Resources</a>
                    <a href="/gallery" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">Gallery</a>
                    <a href="/contact" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">Contact</a>

                    <div class="mt-4 grid gap-2">
                        <template v-if="!isLoggedIn">
                            <a href="/login" @click="mobileOpen = false" class="rounded-lg border border-[#006B3C] px-4 py-3 text-center font-bold text-[#006B3C]">
                                MEMBER LOGIN
                            </a>
                            <a href="/admin/login" @click="mobileOpen = false" class="rounded-lg border border-gray-200 px-4 py-3 text-center font-bold text-gray-700">
                                ADMIN LOGIN
                            </a>
                            <a href="/membership" @click="mobileOpen = false" class="rounded-lg bg-[#D4A72C] px-4 py-3 text-center font-bold text-[#00351F]">
                                JOIN NOW
                            </a>
                        </template>

                        <template v-else-if="!isAdmin">
                            <a href="/dashboard" @click="mobileOpen = false" class="rounded-lg bg-[#006B3C] px-4 py-3 text-center font-bold text-white">
                                MEMBER DASHBOARD
                            </a>
                            <button type="button" class="rounded-lg border border-gray-200 px-4 py-3 font-bold" @click="logout">
                                LOGOUT
                            </button>
                        </template>

                        <template v-else>
                            <a href="/admin/dashboard" @click="mobileOpen = false" class="rounded-lg bg-[#006B3C] px-4 py-3 text-center font-bold text-white">
                                ADMIN DASHBOARD
                            </a>
                            <button type="button" class="rounded-lg border border-gray-200 px-4 py-3 font-bold" @click="logout">
                                LOGOUT
                            </button>
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <!-- FOOTER -->
        <footer class="bg-[#00351F] text-white">
            <div class="mx-auto max-w-[1440px] px-6 py-14 lg:px-8">
                <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

                    <div>
                        <img :src="'/images/bbhs/logo/bbhs-logo.png'" alt="BBHS" class="mb-5 h-20 w-20 object-contain" />
                        <h3 class="text-lg font-bold">Baptist Boys' High School</h3>
                        <p class="text-sm font-semibold text-[#D4A72C]">Old Boys' Association</p>
                        <p class="mt-4 max-w-sm text-sm leading-7 text-white/70">
                            United by heritage, committed to brotherhood, and inspired to build a stronger future for Baptist Boys' High School and generations to come.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-bold">Quick Links</h3>
                        <div class="mt-5 space-y-3 text-sm text-white/70">
                            <a href="/about" class="block hover:text-[#D4A72C]">About Us</a>
                            <a href="/alumni" class="block hover:text-[#D4A72C]">Alumni</a>
                            <a href="/events" class="block hover:text-[#D4A72C]">Events & Reunions</a>
                            <a href="/projects" class="block hover:text-[#D4A72C]">Projects</a>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold">Resources</h3>
                        <div class="mt-5 space-y-3 text-sm text-white/70">
                            <a href="/news" class="block hover:text-[#D4A72C]">News & Media</a>
                            <a href="/gallery" class="block hover:text-[#D4A72C]">Gallery</a>
                            <a href="/resources" class="block hover:text-[#D4A72C]">Resources</a>
                            <a href="/contact" class="block hover:text-[#D4A72C]">Contact Us</a>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold">Member Portal</h3>

                        <p class="mt-5 text-sm leading-6 text-white/70">
                            Access your profile, membership information, events, connections and exclusive resources.
                        </p>

                        <template v-if="!isLoggedIn">
                            <div class="mt-6 space-y-2">
                                <a href="/login" class="block rounded-lg bg-[#D4A72C] px-4 py-3 text-center text-sm font-bold text-[#00351F]">
                                    MEMBER LOGIN
                                </a>
                                <a href="/admin/login" class="block rounded-lg border border-white/20 px-4 py-3 text-center text-sm font-bold text-white hover:bg-white/10">
                                    ADMIN LOGIN
                                </a>
                            </div>
                        </template>

                        <template v-else>
                            <div class="mt-6 flex flex-wrap gap-2">
                                <a
                                    :href="isAdmin ? '/admin/dashboard' : '/dashboard'"
                                    class="rounded-lg bg-[#D4A72C] px-4 py-3 text-sm font-bold text-[#00351F]"
                                >
                                    {{ isAdmin ? 'ADMIN DASHBOARD' : 'DASHBOARD' }}
                                </a>

                                <button
                                    type="button"
                                    :disabled="logoutForm.processing"
                                    class="rounded-lg border border-white/20 px-4 py-3 text-sm font-bold hover:bg-white/10 disabled:opacity-50"
                                    @click="logout"
                                >
                                    LOGOUT
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-12 border-t border-white/10 pt-6 text-sm text-white/60">
                    © {{ new Date().getFullYear() }} BBHS Old Boys Association. All Rights Reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
