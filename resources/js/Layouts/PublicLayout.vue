<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import {
    Menu,
    X,
    UserRound,
    LayoutDashboard,
    LogOut,
    ShieldCheck,
    Bell,
} from 'lucide-vue-next';

const page = usePage();
const mobileOpen = ref(false);
const openDropdown = ref(null);
const mobileExpanded = ref(null);
const moreExpanded = ref(null);
const navArea = ref(null);
const moreButton = ref(null);
const measureRefs = ref([]);
const visibleCount = ref(7);
let navResizeObserver = null;

const navItems = [
    { key: 'home', label: 'Home', href: '/' },
    { key: 'about', label: 'About', items: [
        { label: 'About BBHS OBA', href: '/about' },
        { label: 'National Executives', href: '/executives' },
    ] },
    { key: 'alumni', label: 'Alumni', items: [
        { label: 'Alumni', href: '/alumni' },
        { label: 'Alumni Directory', href: '/alumni-directory' },
    ] },
    { key: 'media', label: 'Media', items: [
        { label: 'News & Updates', href: '/news' },
        { label: 'Events', href: '/events' },
        { label: 'Gallery', href: '/gallery' },
    ] },
    { key: 'giving', label: 'Giving', items: [
        { label: 'Fundraising', href: '/fundraising' },
        { label: 'Projects', href: '/projects' },
    ] },
    { key: 'heritage', label: 'Heritage', items: [
        { label: 'Historical Archives', href: '/archives' },
        { label: 'Resources', href: '/resources' },
    ] },
    { key: 'contact', label: 'Contact', href: '/contact' },
];

const visibleNavItems = computed(() => navItems.slice(0, visibleCount.value));
const overflowNavItems = computed(() => navItems.slice(visibleCount.value));

const setMeasureRef = (el, index) => {
    if (el) measureRefs.value[index] = el;
};

const calculateVisibleItems = async () => {
    await nextTick();
    if (!navArea.value || !measureRefs.value.length) return;

    const available = navArea.value.clientWidth;
    const gap = 16; // matches gap-4 on the desktop navigation
    const widths = navItems.map((_, index) => measureRefs.value[index]?.getBoundingClientRect().width ?? 0);
    const moreWidth = moreButton.value?.getBoundingClientRect().width ?? 70;

    let count = navItems.length;
    const allWidth = widths.reduce((sum, width) => sum + width, 0) + gap * Math.max(0, navItems.length - 1);

    if (allWidth > available) {
        count = 0;
        for (let candidate = navItems.length - 1; candidate >= 0; candidate--) {
            const linksWidth = widths.slice(0, candidate).reduce((sum, width) => sum + width, 0);
            const gapsWidth = gap * candidate; // gaps between visible links and More
            if (linksWidth + gapsWidth + moreWidth <= available) {
                count = candidate;
                break;
            }
        }
    }

    visibleCount.value = count;
    if (openDropdown.value && !navItems.slice(0, count).some(item => item.key === openDropdown.value)) {
        openDropdown.value = null;
    }
};

const closeMenus = () => {
    openDropdown.value = null;
    mobileExpanded.value = null;
    moreExpanded.value = null;
};

const user = computed(() => page.props.auth?.user ?? null);
const isLoggedIn = computed(() => !!user.value);
const isAdmin = computed(() => !!user.value?.is_admin);
const unreadCount = computed(() => page.props.notifications?.unread_count ?? 0);

const logoutForm = useForm({});
const logout = () => {
    logoutForm.post('/logout', {
        onFinish: () => {
            mobileOpen.value = false;
            closeMenus();
        },
    });
};

onMounted(async () => {
    await calculateVisibleItems();
    if (typeof ResizeObserver !== 'undefined' && navArea.value) {
        navResizeObserver = new ResizeObserver(calculateVisibleItems);
        navResizeObserver.observe(navArea.value);
        const header = navArea.value.closest('header');
        if (header) navResizeObserver.observe(header);
    }
    window.addEventListener('resize', calculateVisibleItems);
});

onBeforeUnmount(() => {
    navResizeObserver?.disconnect();
    window.removeEventListener('resize', calculateVisibleItems);
});
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

                <!-- DESKTOP: show every group that fits; overflow alone moves into More -->
                <div class="hidden min-w-0 flex-1 items-center gap-3 lg:flex">
                    <nav ref="navArea" class="flex min-w-0 flex-1 items-center justify-end gap-4">
                        <template v-for="item in visibleNavItems" :key="item.key">
                            <a v-if="item.href" :href="item.href" class="whitespace-nowrap text-[13px] font-semibold text-gray-700 transition hover:text-[#087A3A]">{{ item.label }}</a>
                            <div v-else class="relative">
                                <button type="button" @click="openDropdown = openDropdown === item.key ? null : item.key" @keydown.escape="closeMenus" :aria-expanded="openDropdown === item.key" aria-haspopup="true" class="inline-flex items-center gap-1 whitespace-nowrap text-[13px] font-semibold text-gray-700 transition hover:text-[#087A3A]">
                                    {{ item.label }} <span aria-hidden="true" class="text-[11px]">⌄</span>
                                </button>
                                <div v-if="openDropdown === item.key" class="absolute left-0 top-full z-50 mt-3 min-w-52 rounded-xl border border-gray-100 bg-white p-2 shadow-xl">
                                    <a v-for="child in item.items" :key="child.href" :href="child.href" @click="closeMenus" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-[#F2F8F4] hover:text-[#087A3A]">{{ child.label }}</a>
                                </div>
                            </div>
                        </template>

                        <div v-if="overflowNavItems.length" class="relative shrink-0">
                            <button type="button" @click="openDropdown = openDropdown === 'more' ? null : 'more'" @keydown.escape="closeMenus" :aria-expanded="openDropdown === 'more'" aria-haspopup="true" class="inline-flex items-center gap-1 whitespace-nowrap rounded-lg px-2 py-2 text-[13px] font-semibold text-gray-700 hover:bg-gray-50 hover:text-[#087A3A]">More <span aria-hidden="true">⌄</span></button>
                            <div v-if="openDropdown === 'more'" class="absolute right-0 top-full z-50 mt-3 max-h-[70vh] w-64 overflow-y-auto rounded-xl border border-gray-100 bg-white p-2 shadow-xl">
                                <template v-for="item in overflowNavItems" :key="item.key">
                                    <a v-if="item.href" :href="item.href" @click="closeMenus" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-[#F2F8F4] hover:text-[#087A3A]">{{ item.label }}</a>
                                    <div v-else class="border-b border-gray-100 last:border-0">
                                        <button type="button" @click="moreExpanded = moreExpanded === item.key ? null : item.key" :aria-expanded="moreExpanded === item.key" class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-gray-800 hover:bg-[#F2F8F4] hover:text-[#087A3A]">
                                            <span>{{ item.label }}</span><span class="text-xs text-gray-400">{{ moreExpanded === item.key ? '−' : '+' }}</span>
                                        </button>
                                        <div v-if="moreExpanded === item.key" class="mb-2 ml-3 border-l-2 border-[#DCEBE2] pl-3">
                                            <a v-for="child in item.items" :key="child.href" :href="child.href" @click="closeMenus" class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-[#F2F8F4] hover:text-[#087A3A]">{{ child.label }}</a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </nav>

                    <a v-if="isLoggedIn" href="/notifications" class="relative inline-flex shrink-0 items-center rounded-lg border border-gray-200 p-2.5 text-[#006B3C] hover:bg-[#EEF8F2]" aria-label="Notifications">
                        <Bell :size="18" />
                        <span v-if="unreadCount > 0" class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-extrabold text-white">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
                    </a>

                    <a v-if="!isLoggedIn" href="/membership" class="shrink-0 whitespace-nowrap rounded-lg bg-[#D4A72C] px-4 py-3 text-xs font-extrabold text-[#00351F] hover:bg-[#E5B93F]">JOIN</a>
                    <template v-else-if="!isAdmin">
                        <a href="/dashboard" class="shrink-0 whitespace-nowrap rounded-lg bg-[#006B3C] px-3 py-3 text-xs font-bold text-white">DASHBOARD</a>
                        <button type="button" :disabled="logoutForm.processing" class="shrink-0 whitespace-nowrap rounded-lg border border-gray-200 px-3 py-3 text-xs font-bold text-gray-700 hover:border-[#006B3C] hover:text-[#006B3C] disabled:opacity-50" @click="logout">LOGOUT</button>
                    </template>
                    <template v-else>
                        <a href="/admin/dashboard" class="shrink-0 whitespace-nowrap rounded-lg bg-[#006B3C] px-3 py-3 text-xs font-bold text-white"><ShieldCheck :size="15" class="mr-1 inline"/>ADMIN</a>
                        <a href="/admin/notifications/create" class="shrink-0 whitespace-nowrap rounded-lg border border-[#006B3C] px-3 py-3 text-xs font-bold text-[#006B3C]">UPDATE</a>
                        <button type="button" :disabled="logoutForm.processing" class="shrink-0 whitespace-nowrap rounded-lg border border-gray-200 px-3 py-3 text-xs font-bold text-gray-700 hover:border-[#006B3C] hover:text-[#006B3C] disabled:opacity-50" @click="logout">LOGOUT</button>
                    </template>
                </div>

                <!-- Hidden measuring row: used to decide whether More is actually needed -->
                <div class="pointer-events-none absolute -z-10 flex w-max items-center gap-3 opacity-0" aria-hidden="true">
                    <span v-for="(item, index) in navItems" :key="item.key" :ref="el => setMeasureRef(el, index)" class="whitespace-nowrap text-[13px] font-semibold">
                        {{ item.label }}<span v-if="item.items" class="ml-1 text-[11px]">⌄</span>
                    </span>
                </div>
                <span ref="moreButton" class="pointer-events-none absolute -z-10 whitespace-nowrap rounded-lg px-2 py-2 text-[13px] font-semibold opacity-0" aria-hidden="true">More⌄</span>

                <button type="button" class="rounded-lg p-2 text-[#00351F] lg:hidden" @click="mobileOpen = !mobileOpen; closeMenus()" aria-label="Toggle menu">
                    <X v-if="mobileOpen" :size="27" />
                    <Menu v-else :size="27" />
                </button>
            </div>

            <!-- MOBILE / TABLET -->
            <div v-if="mobileOpen" class="border-t border-gray-100 bg-white shadow-lg lg:hidden">
                <nav class="space-y-1 px-5 py-5">
                    <a href="/" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">Home</a>

                    <template v-for="item in navItems.filter(item => item.key !== 'home')" :key="item.key">
                        <a v-if="item.href" :href="item.href" @click="mobileOpen = false" class="block rounded-lg px-4 py-3 font-semibold hover:bg-gray-50">{{ item.label }}</a>
                        <div v-else class="rounded-lg border border-gray-100">
                            <button type="button" @click="mobileExpanded = mobileExpanded === item.key ? null : item.key" :aria-expanded="mobileExpanded === item.key" class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-left font-semibold hover:bg-gray-50">
                                {{ item.label }} <span aria-hidden="true">⌄</span>
                            </button>
                            <div v-if="mobileExpanded === item.key" class="space-y-1 border-t border-gray-100 px-3 py-2">
                                <a v-for="child in item.items" :key="child.href" :href="child.href" @click="mobileOpen = false; closeMenus()" class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">{{ child.label }}</a>
                            </div>
                        </div>
                    </template>

                    <a v-if="isLoggedIn" href="/notifications" @click="mobileOpen = false" class="flex items-center justify-between rounded-lg px-4 py-3 font-semibold text-[#006B3C] hover:bg-gray-50"><span class="flex items-center gap-2"><Bell :size="17" /> Notifications</span><span v-if="unreadCount > 0" class="rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold text-white">{{ unreadCount }}</span></a>

                    <div class="mt-4 grid gap-2">
                        <template v-if="!isLoggedIn">
                            <a href="/login" @click="mobileOpen = false" class="rounded-lg border border-[#006B3C] px-4 py-3 text-center font-bold text-[#006B3C]">MEMBER LOGIN</a>
                            <a href="/admin/login" @click="mobileOpen = false" class="rounded-lg border border-gray-200 px-4 py-3 text-center font-bold text-gray-700">ADMIN LOGIN</a>
                            <a href="/membership" @click="mobileOpen = false" class="rounded-lg bg-[#D4A72C] px-4 py-3 text-center font-bold text-[#00351F]">JOIN NOW</a>
                        </template>
                        <template v-else-if="!isAdmin">
                            <a href="/dashboard" @click="mobileOpen = false" class="rounded-lg bg-[#006B3C] px-4 py-3 text-center font-bold text-white">MEMBER DASHBOARD</a>
                            <button type="button" class="rounded-lg border border-gray-200 px-4 py-3 font-bold" @click="logout">LOGOUT</button>
                        </template>
                        <template v-else>
                            <a href="/admin/dashboard" @click="mobileOpen = false" class="rounded-lg bg-[#006B3C] px-4 py-3 text-center font-bold text-white">ADMIN DASHBOARD</a>
                            <a href="/admin/notifications/create" @click="mobileOpen = false" class="rounded-lg border border-[#006B3C] px-4 py-3 text-center font-bold text-[#006B3C]">SEND NOTIFICATION</a>
                            <button type="button" class="rounded-lg border border-gray-200 px-4 py-3 font-bold" @click="logout">LOGOUT</button>
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
                            <a href="/executives" class="block hover:text-[#D4A72C]">National Executives</a>
                            <a href="/alumni" class="block hover:text-[#D4A72C]">Alumni</a>
                            <a href="/events" class="block hover:text-[#D4A72C]">Events & Reunions</a>
                            <a href="/projects" class="block hover:text-[#D4A72C]">Projects</a>
                            <a href="/fundraising" class="block hover:text-[#D4A72C]">Fundraising</a>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold">Resources</h3>
                        <div class="mt-5 space-y-3 text-sm text-white/70">
                            <a href="/news" class="block hover:text-[#D4A72C]">News & Media</a>
                            <a href="/gallery" class="block hover:text-[#D4A72C]">Gallery</a>
                            <a href="/resources" class="block hover:text-[#D4A72C]">Resources</a>
                            <a href="/archives" class="block hover:text-[#D4A72C]">Historical Archives</a>
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

                <div class="mt-12 border-t border-white/10 pt-6">
                    <p class="text-center text-xs font-semibold leading-6 text-white/75">The New BBHS OBA Digital Ecosystem is proudly sponsored by
                        <template v-for="(sponsor, index) in (page.props.sponsorProfiles ?? [])" :key="sponsor.name">
                            <span v-if="index > 0"> and </span>
                            <a v-if="sponsor.profile_url" :href="sponsor.profile_url" class="text-[#F1D36A] underline-offset-2 hover:underline">{{ sponsor.name }}<span v-if="sponsor.position" class="ml-1 text-white/60">({{ sponsor.position }})</span></a>
                            <span v-else class="text-[#F1D36A]">{{ sponsor.name }}<span v-if="sponsor.position" class="ml-1 text-white/60">({{ sponsor.position }})</span></span>
                        </template>.
                    </p>
                    <p class="mt-3 text-center text-xs text-white/45">© {{ new Date().getFullYear() }} BBHS Old Boys Association. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
