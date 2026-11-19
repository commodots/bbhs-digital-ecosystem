<script setup>
import MemberPortalLayout from '../Layouts/MemberPortalLayout.vue';
import { usePage } from '@inertiajs/vue3';
import {
    CalendarDays,
    UserCircle,
    Newspaper,
    HeartHandshake,
    MessageCircle,
    ArrowRight,
    ClipboardCheck,
    ShieldCheck, Wallet,
} from 'lucide-vue-next';

const page = usePage();
const props = defineProps({
    member: { type: Object, default: () => ({}) },
    alumniProfile: { type: Object, default: null },
    executiveAccess: { type: Boolean, default: false },
    executiveAssignments: { type: Array, default: () => [] },
	myUpcomingEvents: { type: Array, default: () => [] },
});

const cards = [
    { icon: UserCircle, title: 'My Profile', text: 'Update your alumni information and contact details.', href: '/profile' },
    { icon: MessageCircle, title: 'Member Messages', text: 'Open your private conversations with other approved alumni.', href: '/messages' },
    { icon: CalendarDays, title: 'My Events', text: 'View events, reunions and activities you are attending.', href: '/events' },
    { icon: Newspaper, title: 'Latest News', text: 'Catch up with the latest BBHS and alumni stories.', href: '/news' },
    { icon: HeartHandshake, title: 'Fundraising & Donations', text: 'Support BBHS campaigns and review your donations, pledges and receipts.', href: '/my-fundraising' },
    { icon: Wallet, title: 'Pay', text: 'View your dues status, make payments and review your payment history.', href: '/dues' },
    { icon: HeartHandshake, title: 'Welfare Support', text: 'Submit and privately track a welfare assistance request.', href: '/welfare' },
    ...(page.props.auth?.user?.welfare_team ? [{ icon: HeartHandshake, title: 'Welfare Team Workspace', text: 'Review and manage confidential welfare applications.', href: '/admin/welfare' }] : []),
    { icon: HeartHandshake, title: 'Support a Project', text: 'Discover projects and initiatives you can support.', href: '/projects' },
];

const executiveScopeText = () => {
    const scopes = [...new Set(props.executiveAssignments.map(item => item.scope_type))];
    if (scopes.includes('global')) return 'Global executive access';
    if (scopes.includes('set') && scopes.includes('branch')) return 'Graduation set and branch executive access';
    if (scopes.includes('set')) return 'Graduation set executive access';
    if (scopes.includes('branch')) return 'Branch executive access';
    return 'Executive access';
};
</script>

<template>
<MemberPortalLayout>
<section class="bg-[#00351F]">
    <div class="mx-auto max-w-[1200px] px-5 py-12 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Member Portal</p>
        <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Welcome Back, Old Boy.</h1>
        <p class="mt-2 text-sm text-white/70">Your BBHS member dashboard.</p>
    </div>
</section>

<section class="bg-[#F7F9F7] py-12">
    <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
        <!-- Executive tools are visible only to users with an active appointment. -->
        <div v-if="executiveAccess" class="mb-8 overflow-hidden rounded-2xl border border-[#D4A72C]/40 bg-white shadow-sm">
            <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#FFF8E5] text-[#8A6812]">
                        <ShieldCheck :size="25" />
                    </div>
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#8A6812]">Sub-Admin Menu</p>
                        <h2 class="mt-1 text-xl font-extrabold text-[#00351F]">Executive Tools</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ executiveScopeText() }}. Access your assigned administration tasks here.</p>
                        <div v-if="executiveAssignments.length" class="mt-2 flex flex-wrap gap-2">
                            <span v-for="(assignment, index) in executiveAssignments" :key="index" class="rounded-full bg-[#EEF8F2] px-3 py-1 text-xs font-semibold text-[#006B3C]">
                                {{ assignment.position }} · {{ assignment.scope_type === 'set' ? 'Set' : assignment.scope_type === 'branch' ? 'Branch' : 'Global' }}
                            </span>
                        </div>
                    </div>
                </div>
                <a href="/admin/verifications" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#00351F]">
                    <ClipboardCheck :size="17" />
                    Open Verifications
                    <ArrowRight :size="16" />
                </a>
            </div>
            <div class="border-t border-gray-100 bg-[#FAFCFA] px-6 py-3 text-xs text-gray-500">
                Your access is limited to the graduation set or branch assigned to you.
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <a v-for="card in cards" :key="card.title" :href="card.href" class="rounded-2xl border border-gray-200 bg-white p-6 transition hover:-translate-y-1 hover:shadow-md">
                <component :is="card.icon" :size="24" class="text-[#006B3C]" />
                <h2 class="mt-5 font-extrabold text-[#00351F]">{{ card.title }}</h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">{{ card.text }}</p>
                <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]">Open <ArrowRight :size="15" /></span>
            </a>
        </div>

        <div v-if="myUpcomingEvents.length" class="mt-8 rounded-2xl border border-gray-200 bg-white p-7 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#006B3C]">Your event bookings</p>
                    <h2 class="mt-2 text-xl font-extrabold text-[#00351F]">Events you are attending</h2>
                </div>
                <a href="/events" class="text-sm font-bold text-[#006B3C]">Browse events</a>
            </div>
            <div class="mt-5 divide-y">
                <div v-for="item in myUpcomingEvents" :key="item.id" class="flex flex-wrap items-center justify-between gap-3 py-4">
                    <div>
                        <a :href="`/events/${item.event.slug}`" class="font-bold text-[#00351F] hover:text-[#006B3C]">{{ item.event.title }}</a>
                        <p class="mt-1 text-xs text-gray-500">{{ item.event.starts_at || 'Date to be confirmed' }} <span v-if="item.event.venue"> · {{ item.event.venue }}</span></p>
                    </div>
                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Registered</span>
                </div>
            </div>
        </div>

        <div v-if="duesSummary" class="mb-8 rounded-2xl border border-[#D4A72C]/40 bg-white p-6 shadow-sm"><div class="flex flex-wrap items-center justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-widest text-[#006B3C]">Pay</p><h2 class="mt-1 text-xl font-extrabold text-[#00351F]">{{ duesSummary.status_label }}</h2><p class="mt-1 text-sm text-gray-500">Next due: {{ duesSummary.next_due_at || "To be confirmed" }}</p></div><a href="/dues" class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white">Pay</a></div></div>
        <div class="mt-8 rounded-2xl bg-white p-7 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#006B3C]">Your Membership</p>
            <h2 class="mt-2 text-2xl font-extrabold text-[#00351F]">Member information</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-3">
                <div><p class="text-xs text-gray-400">Status</p><p class="mt-1 font-bold text-[#006B3C]">{{ member?.member_status ?? 'Active' }}</p></div>
                <div><p class="text-xs text-gray-400">Class / Set</p><p class="mt-1 font-bold text-[#00351F]">{{ alumniProfile?.class_set ?? 'Not yet configured' }}</p></div>
                <div><p class="text-xs text-gray-400">Member Since</p><p class="mt-1 font-bold text-[#00351F]">{{ member?.created_at ? new Date(member.created_at).toLocaleDateString() : '—' }}</p></div>
            </div>
        </div>
    </div>
</section>
</MemberPortalLayout>
</template>
