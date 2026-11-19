<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard, Network, ClipboardCheck, Archive, Users, Globe, UserRound,
    Wallet, HandCoins, Contact, ChartNoAxesCombined, Images, Mail, CalendarDays,
    Newspaper, HeartHandshake, Menu, X, Search, Bell, CircleHelp,
    ChevronDown, ExternalLink, LogOut, ShieldCheck, Star, MessageCircle,
} from 'lucide-vue-next';

const page = usePage();
// Resolve the logo from Laravel's public directory without Vite treating it as a module import.
const logoUrl = '/images/bbhs/logo/bbhs-logo.png';
const mobileOpen = ref(false);
const profileMenuOpen = ref(false);
const collapsed = ref(false);
const search = ref('');
const isAdmin = computed(() => !!page.props.auth?.user?.is_admin);
const isExecutive = computed(() => !!page.props.auth?.user?.is_executive);
const isGlobalExecutive = computed(() => !!page.props.auth?.user?.is_global_executive);
const isWelfareTeam = computed(() => !!page.props.auth?.user?.welfare_team);
const user = computed(() => page.props.auth?.user ?? {});
const unread = computed(() => Number(page.props.notifications?.unread_count ?? 0));
const logoutForm = useForm({});
const logout = () => logoutForm.post('/logout');

const membership = [
    { label: 'All Members', href: '/admin/members', icon: Users },
    { label: 'Bulk Member Import', href: '/admin/members/import', icon: Users },
    { label: 'Membership Verification', href: '/admin/verifications', icon: ClipboardCheck },
    { label: 'Membership Reports', href: '/admin/membership-reports', icon: ChartNoAxesCombined },
    { label: 'Contact Directory', href: '/admin/contacts', icon: Contact },
];
const finance = [
    { label: 'Dues & Payments', href: '/admin/dues', icon: Wallet },
    { label: 'Fundraising', href: '/admin/fundraising', icon: HandCoins },
    { label: 'Financial Reports', href: '/admin/financial-reports', icon: ChartNoAxesCombined },
    { label: 'Welfare Workspace', href: '/admin/welfare', icon: HeartHandshake },
    { label: 'Welfare Reports', href: '/admin/welfare-reports', icon: ChartNoAxesCombined },
    { label: 'Welfare Team', href: '/admin/welfare-team', icon: Users },
];
const organisation = [
    { label: 'Global Executives', href: '/admin/organisation', icon: ShieldCheck },
    { label: 'Graduation Sets & Branches', href: '/admin/organisation', icon: Network },
    { label: 'Administrators', href: '/admin/organisation', icon: UserRound },
    { label: 'Historical Archives', href: '/admin/archives', icon: Archive },
];
const content = [
    { label: 'Events & Meetings', href: '/admin/events', icon: CalendarDays },
    { label: 'News & Announcements', href: '/admin/news', icon: Newspaper },
    { label: 'Gallery', href: '/admin/gallery', icon: Images },
    { label: 'Newsletter', href: '/admin/newsletter', icon: Mail },
    { label: 'Alumni Spotlight', href: '/admin/spotlights', icon: Star },
];
const groups = computed(() => {
    if (isAdmin.value) return [
        { title: 'Overview', links: [{ label: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard }] },
        { title: 'Membership', links: membership },
        { title: 'Finance & Welfare', links: finance },
        { title: 'Organisation', links: organisation },
        { title: 'Content & Events', links: content },
    ];
    if (isGlobalExecutive.value) return [
        { title: 'National Executive', links: [
            { label: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard },
            { label: 'Membership Verification', href: '/admin/verifications', icon: ClipboardCheck },
            { label: 'Welfare Reports', href: '/admin/welfare-reports', icon: ChartNoAxesCombined },
            ...(isWelfareTeam.value ? [{ label: 'Welfare Workspace', href: '/admin/welfare', icon: HeartHandshake }] : []),
        ] },
    ];
    if (isWelfareTeam.value) return [{ title: 'Welfare Administration', links: [
        { label: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard },
        { label: 'Welfare Workspace', href: '/admin/welfare', icon: HeartHandshake },
        { label: 'Welfare Reports', href: '/admin/welfare-reports', icon: ChartNoAxesCombined },
    ] }];
    if (isExecutive.value) return [{ title: 'Membership', links: [
        { label: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard },
        ...membership.filter(x => ['Membership Verification', 'Bulk Member Import', 'Membership Reports', 'Contact Directory'].includes(x.label)),
    ] }];
    return [{ title: 'Administration', links: [{ label: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard }] }];
});
const visibleGroups = computed(() => groups.value.map(group => ({ ...group, links: group.links.filter(link => link.label.toLowerCase().includes(search.value.toLowerCase())) })).filter(group => group.links.length));
const active = href => page.url === href || (href !== '/admin/dashboard' && page.url.startsWith(href + '/'));
const isAdminPage = computed(() => page.url.startsWith('/admin'));

onMounted(() => document.body.classList.add('bbhs-admin-shell'));
onBeforeUnmount(() => document.body.classList.remove('bbhs-admin-shell'));
</script>

<template>
    <aside class="bbhs-admin-sidebar" :class="[mobileOpen ? 'is-open' : '', collapsed ? 'is-collapsed' : '']">
        <div class="flex h-[76px] items-center gap-3 border-b border-white/10 px-5">
            <img :src="logoUrl" alt="BBHS" class="h-11 w-11 shrink-0 object-contain" />
            <div v-if="!collapsed" class="min-w-0"><p class="text-lg font-black tracking-wide text-white">BBHS</p><p class="text-[9px] font-bold uppercase tracking-[.18em] text-[#E8C65A]">Alumni Administration</p></div>
            <button class="ml-auto text-white/70 lg:hidden" @click="mobileOpen=false" aria-label="Close menu"><X :size="21" /></button>
        </div>
        <div v-if="!collapsed" class="px-4 pt-5"><p class="text-[10px] font-bold uppercase tracking-[.18em] text-[#E8C65A]">Signed in as</p><p class="mt-1 truncate text-sm font-semibold text-white">{{ user.name || 'Administrator' }}</p><p class="text-xs text-white/50">{{ isAdmin ? 'Super Administrator' : isGlobalExecutive ? 'Global Executive' : isWelfareTeam ? 'Welfare Team' : 'Administrator' }}</p></div>
        <nav class="admin-side-scroll mt-5 space-y-5 overflow-y-auto px-3 pb-6">
            <section v-for="group in visibleGroups" :key="group.title">
                <p v-if="!collapsed" class="mb-2 px-3 text-[10px] font-extrabold uppercase tracking-[.16em] text-[#E8C65A]">{{ group.title }}</p>
                <a v-for="link in group.links" :key="link.href + link.label" :href="link.href" :title="collapsed ? link.label : undefined" @click="mobileOpen=false" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-semibold transition" :class="active(link.href) ? 'bg-[#E8C65A] text-[#06382d] shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white'">
                    <component :is="link.icon" :size="17" class="shrink-0" /><span v-if="!collapsed" class="truncate">{{ link.label }}</span>
                </a>
            </section>
        </nav>
        <div class="mt-auto border-t border-white/10 p-3">
            <a href="/dashboard" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs font-semibold text-white/80 hover:bg-white/10"><UserRound :size="17"/> <span v-if="!collapsed">Member Dashboard</span></a>
            <a href="/" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs font-semibold text-white/80 hover:bg-white/10"><ExternalLink :size="17"/> <span v-if="!collapsed">View Public Website</span></a>
            <button @click="logout" :disabled="logoutForm.processing" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-xs font-semibold text-white/70 hover:bg-red-500/20 hover:text-white"><LogOut :size="17"/> <span v-if="!collapsed">Sign out</span></button>
        </div>
    </aside>
    <div v-if="mobileOpen" class="fixed inset-0 z-[70] bg-slate-950/50 lg:hidden" @click="mobileOpen=false"></div>
    <header class="bbhs-admin-topbar">
        <div class="flex min-w-0 items-center gap-3">
            <button class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" @click="mobileOpen=!mobileOpen" aria-label="Open navigation"><Menu :size="21" /></button>
            <button class="hidden rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:block" @click="collapsed=!collapsed" aria-label="Collapse navigation"><Menu :size="20" /></button>
            <div class="hidden min-w-0 text-sm text-slate-500 sm:block">Administration <span class="mx-1 text-slate-300">/</span> <span class="font-bold text-[#06382d]">{{ page.props.title || (page.url.includes('dashboard') ? 'Dashboard' : 'Workspace') }}</span></div>
        </div>
        <div class="flex min-w-0 items-center gap-2 sm:gap-4">
            <label class="hidden w-56 items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 md:flex lg:w-72"><Search :size="16" class="text-slate-400"/><input v-model="search" class="w-full border-0 bg-transparent p-0 text-xs outline-none focus:ring-0" placeholder="Filter menu items..." /></label>
            <a href="/messages" class="relative rounded-lg p-2 text-slate-600 hover:bg-slate-100" aria-label="Messages"><MessageCircle :size="19"/><span v-if="page.props.messages?.unread_count" class="absolute right-0 top-0 rounded-full bg-emerald-600 px-1.5 text-[9px] font-bold text-white">{{ page.props.messages.unread_count }}</span></a>
            <button type="button" class="relative rounded-lg p-2 text-slate-600 hover:bg-slate-100" aria-label="Notifications"><Bell :size="19"/><span v-if="unread" class="absolute right-0 top-0 rounded-full bg-red-500 px-1.5 text-[9px] font-bold text-white">{{ unread }}</span></button>
            <a href="/contact" class="hidden rounded-lg p-2 text-slate-600 hover:bg-slate-100 sm:block" aria-label="Help"><CircleHelp :size="19"/></a>
            <div class="relative hidden border-l border-slate-200 pl-3 sm:block">
                <button type="button" @click="profileMenuOpen=!profileMenuOpen" class="flex items-center gap-2 rounded-lg p-1.5 text-left hover:bg-slate-100">
                    <img v-if="user.profile_photo_url" :src="user.profile_photo_url" alt="Profile photograph" class="h-9 w-9 rounded-full border border-slate-200 object-cover"/>
                    <span v-else class="flex h-9 w-9 items-center justify-center rounded-full bg-[#EAF0F8] text-[#06382d]"><UserRound :size="19"/></span>
                    <span class="max-w-32"><span class="block truncate text-xs font-bold text-slate-800">{{ user.name || 'Admin User' }}</span><span class="block text-[10px] text-slate-500">{{ isAdmin ? 'Super Admin' : isGlobalExecutive ? 'Global Executive' : isWelfareTeam ? 'Welfare Team' : 'Administrator' }}</span></span>
                    <ChevronDown :size="14"/>
                </button>
                <div v-if="profileMenuOpen" class="absolute right-0 top-full z-[100] mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl">
                    <a href="/profile" class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-emerald-50">My Profile</a>
                    <button @click="logout" :disabled="logoutForm.processing" class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-red-600 hover:bg-red-50">Sign out</button>
                </div>
            </div>
        </div>
    </header>
</template>

<style>
    body.bbhs-admin-shell { background:#f4f6fa; }
    body.bbhs-admin-shell #app { padding-left:260px; padding-top:64px; transition:padding-left .2s ease; }
    body.bbhs-admin-shell .bbhs-admin-sidebar { position:fixed; z-index:80; inset:0 auto 0 0; display:flex; width:260px; flex-direction:column; background:linear-gradient(180deg,#07543f 0%,#06382d 100%); transition:width .2s ease,transform .2s ease; }
    body.bbhs-admin-shell .bbhs-admin-sidebar.is-collapsed { width:76px; }
    body.bbhs-admin-shell .bbhs-admin-sidebar.is-collapsed nav a { justify-content:center; }
    body.bbhs-admin-shell .bbhs-admin-topbar { position:fixed; z-index:60; top:0; right:0; left:260px; display:flex; height:64px; align-items:center; justify-content:space-between; gap:12px; border-bottom:1px solid #e2e8f0; background:rgba(255,255,255,.96); padding:0 24px; backdrop-filter:blur(12px); transition:left .2s ease; }
    body.bbhs-admin-shell .bbhs-admin-sidebar.is-collapsed ~ .bbhs-admin-topbar { left:76px; }
    body.bbhs-admin-shell:has(.bbhs-admin-sidebar.is-collapsed) #app { padding-left:76px; }
    body.bbhs-admin-shell .admin-side-scroll { scrollbar-width:thin; scrollbar-color:#49617f transparent; }
    @media(max-width:1023px) { body.bbhs-admin-shell #app{padding-left:0;padding-top:60px;} body.bbhs-admin-shell .bbhs-admin-sidebar{width:280px;transform:translateX(-100%);} body.bbhs-admin-shell .bbhs-admin-sidebar.is-open{transform:translateX(0);} body.bbhs-admin-shell .bbhs-admin-topbar{left:0;height:60px;padding:0 12px;} }
</style>
