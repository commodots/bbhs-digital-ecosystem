<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../Components/AdminQuickLinks.vue';
import { Head } from '@inertiajs/vue3';
import {
    Users, Newspaper, CalendarDays, HandCoins, ArrowRight, Clock3,
    ClipboardCheck, Wallet, HeartHandshake, ChartNoAxesCombined,
} from 'lucide-vue-next';

const props = defineProps({
    admin: Object,
    stats: { type: Object, default: () => ({}) },
    bySet: { type: Array, default: () => [] },
    byBranch: { type: Array, default: () => [] },
    registrationTrend: { type: Array, default: () => [] },
    recentMembers: { type: Array, default: () => [] },
    upcomingEvents: { type: Array, default: () => [] },
    recentNews: { type: Array, default: () => [] },
});
const money = value => '₦' + Number(value || 0).toLocaleString('en-NG', { maximumFractionDigits: 0 });
const cards = [
    { title: 'Total Members', value: 'members', description: 'Registered Old Boys', icon: Users, href: '/admin/members' },
    { title: 'Pending Approvals', value: 'pending_verifications', description: 'Set and branch verification queue', icon: ClipboardCheck, href: '/admin/verifications' },
    { title: 'Outstanding Dues', value: 'dues_outstanding_members', description: 'Members with unpaid dues', icon: Wallet, href: '/admin/dues' },
    { title: 'Pending Welfare', value: 'pending_welfare', description: 'Applications requiring attention', icon: HeartHandshake, href: '/admin/welfare' },
    { title: 'Pending Payments', value: 'pending_financial_items', description: 'Dues payments and donations', icon: HandCoins, href: '/admin/financial-reports' },
    { title: 'Upcoming Events', value: 'upcoming_events', description: 'Published future events', icon: CalendarDays, href: '/admin/events' },
    { title: 'Published News', value: 'published_news', description: 'Live association updates', icon: Newspaper, href: '/admin/news' },
    { title: 'Active Fundraising Campaigns', value: 'active_fundraising_campaigns', description: 'Projects, initiatives and causes', icon: HandCoins, href: '/admin/fundraising' },
];
</script>

<template>
<PublicLayout>
    <Head title="National Executive Dashboard" />
    <AdminQuickLinks />
    <main class="min-h-screen bg-[#F4F6FA] py-7 lg:py-9">
        <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div><p class="text-xs font-bold uppercase tracking-[.18em] text-[#B58A16]">National Administration</p><h1 class="mt-1 text-3xl font-extrabold tracking-tight text-[#10284B]">Dashboard</h1><p class="mt-1 text-sm text-slate-500">Welcome back, {{ admin?.name ?? 'Administrator' }}. Here's the current association overview.</p></div>
                <a href="/admin/verifications" class="inline-flex items-center gap-2 rounded-xl bg-[#10284B] px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-[#193b69]"><ClipboardCheck :size="16"/> Review Applications</a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a v-for="card in cards" :key="card.title" :href="card.href" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]"><component :is="card.icon" :size="20" /></div>
                        <ArrowRight :size="17" class="text-gray-300" />
                    </div>
                    <div class="mt-5 text-3xl font-extrabold text-[#00351F]">{{ Number(stats[card.value] || 0).toLocaleString() }}</div>
                    <h2 class="mt-1 font-bold text-[#00351F]">{{ card.title }}</h2>
                    <p class="mt-1 text-xs text-gray-500">{{ card.description }}</p>
                </a>
            </div>

            <section class="mt-6 grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-gray-500">Membership dues collected</p>
                    <p class="mt-2 text-2xl font-extrabold text-[#00351F]">{{ money(stats.dues_collected) }}</p>
                </div>
                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-gray-500">Fundraising donations received</p>
                    <p class="mt-2 text-2xl font-extrabold text-[#00351F]">{{ money(stats.donations_received) }}</p>
                </div>
                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-gray-500">Welfare assistance disbursed</p>
                    <p class="mt-2 text-2xl font-extrabold text-[#00351F]">{{ money(stats.welfare_disbursed) }}</p>
                </div>
            </section>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border bg-white p-5">
                    <div class="flex items-center justify-between"><div><h2 class="font-extrabold text-[#00351F]">Membership by Graduation Set</h2><p class="text-xs text-gray-500">Largest sets by registered member count</p></div><a href="/admin/membership-reports" class="text-xs font-bold text-[#006B3C]">REPORT</a></div>
                    <div class="mt-4 space-y-4">
                        <div v-for="item in bySet" :key="item.label">
                            <div class="mb-1 flex justify-between gap-3 text-sm"><span class="font-semibold text-gray-700">{{ item.label }}</span><strong>{{ Number(item.total).toLocaleString() }}</strong></div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full bg-[#16834B]" :style="{ width: `${Math.max(2, Math.min(100, Number(item.total) / Math.max(1, Number(bySet[0]?.total)) * 100))}%` }"></div></div>
                        </div>
                        <p v-if="!bySet.length" class="text-sm text-gray-500">No graduation-set data available.</p>
                    </div>
                </section>
                <section class="rounded-2xl border bg-white p-5">
                    <div class="flex items-center justify-between"><div><h2 class="font-extrabold text-[#00351F]">Membership by Branch</h2><p class="text-xs text-gray-500">Pending and approved branch affiliations</p></div><a href="/admin/membership-reports" class="text-xs font-bold text-[#006B3C]">REPORT</a></div>
                    <div class="mt-4 divide-y">
                        <div v-for="item in byBranch" :key="item.label" class="flex justify-between gap-3 py-3 text-sm"><span class="font-semibold text-gray-700">{{ item.label }}</span><strong>{{ Number(item.total).toLocaleString() }}</strong></div>
                        <p v-if="!byBranch.length" class="py-3 text-sm text-gray-500">No branch affiliation data available.</p>
                    </div>
                </section>
            </div>

            <section class="mt-6 rounded-2xl border bg-white p-5">
                <div class="flex items-center justify-between"><div><h2 class="font-extrabold text-[#00351F]">New Member Registrations</h2><p class="text-xs text-gray-500">Monthly registrations over the last six months</p></div><ChartNoAxesCombined class="text-[#16834B]" /></div>
                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    <div v-for="item in registrationTrend" :key="item.month" class="rounded-xl bg-[#F7F9F7] p-3">
                        <p class="text-xs text-gray-500">{{ item.month }}</p><p class="mt-1 text-xl font-extrabold text-[#006B3C]">{{ Number(item.total).toLocaleString() }}</p>
                    </div>
                </div>
            </section>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border bg-white">
                    <div class="flex items-center justify-between border-b border-gray-100 p-5"><div><h2 class="font-extrabold text-[#00351F]">Recent Members</h2><p class="text-xs text-gray-500">Latest registrations</p></div><a href="/admin/members" class="text-xs font-bold text-[#006B3C]">VIEW ALL</a></div>
                    <div v-if="!recentMembers.length" class="p-6 text-sm text-gray-500">No members yet.</div>
                    <div v-else class="divide-y divide-gray-100">
                        <div v-for="member in recentMembers" :key="member.id" class="flex items-center justify-between gap-3 p-4"><div><p class="text-sm font-bold text-[#00351F]">{{ member.name }}</p><p class="text-xs text-gray-500">{{ member.email }}</p></div><span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase text-amber-700">{{ member.member_status }}</span></div>
                    </div>
                </section>
                <section class="rounded-2xl border bg-white">
                    <div class="flex items-center justify-between border-b border-gray-100 p-5"><div><h2 class="font-extrabold text-[#00351F]">Upcoming Events</h2><p class="text-xs text-gray-500">Next association activities</p></div><a href="/admin/events" class="text-xs font-bold text-[#006B3C]">MANAGE</a></div>
                    <div v-if="!upcomingEvents.length" class="p-6 text-sm text-gray-500">No upcoming events.</div>
                    <div v-else class="divide-y divide-gray-100">
                        <div v-for="event in upcomingEvents" :key="event.id" class="p-4"><p class="text-sm font-bold text-[#00351F]">{{ event.title }}</p><p class="mt-1 flex items-center gap-2 text-xs text-gray-500"><Clock3 :size="13" /> {{ event.starts_at }} <span v-if="event.venue">· {{ event.venue }}</span></p></div>
                    </div>
                </section>
            </div>

            <section class="mt-6 rounded-2xl border bg-white">
                <div class="flex items-center justify-between border-b border-gray-100 p-5"><div><h2 class="font-extrabold text-[#00351F]">Recent News</h2><p class="text-xs text-gray-500">Latest content updates</p></div><a href="/admin/news" class="text-xs font-bold text-[#006B3C]">MANAGE</a></div>
                <div v-if="!recentNews.length" class="p-6 text-sm text-gray-500">No news articles.</div>
                <div v-else class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="item in recentNews" :key="item.id" class="rounded-xl bg-[#F7F9F7] p-4"><p class="font-bold text-[#00351F]">{{ item.title }}</p><p class="mt-2 text-xs text-gray-500">{{ item.published ? 'Published' : 'Draft' }} · {{ item.published_at || 'Not published' }}</p></div>
                </div>
            </section>
        </div>
    </main>
</PublicLayout>
</template>
