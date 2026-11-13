<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { LayoutDashboard, Network, ClipboardCheck, Archive, Users, Globe, UserRound, Wallet, HandCoins, Contact, ChartNoAxesCombined, Images, Mail } from 'lucide-vue-next';

const page = usePage();
const isAdmin = computed(() => !!page.props.auth?.user?.is_admin);
const isExecutive = computed(() => !!page.props.auth?.user?.is_executive);

const allLinks = [
    { label: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard },
    { label: 'Manage Organisation', href: '/admin/organisation', icon: Network },
    { label: 'Review Verifications', href: '/admin/verifications', icon: ClipboardCheck },
    { label: 'Manage Archives', href: '/admin/archives', icon: Archive },
    { label: 'Members', href: '/admin/members', icon: Users },
    { label: 'Contact Directory', href: '/admin/contacts', icon: Contact },
    { label: 'Membership Dues', href: '/admin/dues', icon: Wallet },
    { label: 'Fundraising', href: '/admin/fundraising', icon: HandCoins },
    { label: 'Financial Reports', href: '/admin/financial-reports', icon: ChartNoAxesCombined },
    { label: 'Gallery', href: '/admin/gallery', icon: Images },
    { label: 'Newsletter', href: '/admin/newsletter', icon: Mail },
    { label: 'Member Dashboard', href: '/dashboard', icon: UserRound },
    { label: 'View Public Site', href: '/', icon: Globe },
];

const links = computed(() => isAdmin.value
    ? allLinks
    : allLinks.filter(link => ['Review Verifications', 'Contact Directory', 'Member Dashboard', 'View Public Site'].includes(link.label))
);
</script>

<template>
    <div class="mx-auto max-w-[1400px] px-5 pb-5 lg:px-8">
        <div class="flex flex-wrap items-center gap-2 border-t border-white/15 pt-4">
            <span class="mr-1 text-[10px] font-extrabold uppercase tracking-[0.18em] text-[#D4A72C]">Admin Menu</span>
            <a
                v-for="link in links"
                :key="link.href"
                :href="link.href"
                class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/5 px-3.5 py-2.5 text-xs font-bold text-white transition hover:border-[#D4A72C] hover:bg-white/10"
            >
                <component :is="link.icon" :size="14" />
                {{ link.label }}
            </a>
        </div>
    </div>
</template>
