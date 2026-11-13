<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { Search, Phone, MessageCircle, Mail } from 'lucide-vue-next';

const props = defineProps({ members:Object, filters:Object, canExport:Boolean });
const search = computed(() => props.filters?.search || '');
function applySearch(event) {
    router.get('/admin/contacts', { search: event.target.value }, { preserveState:true, replace:true });
}
function wa(number) {
    const digits = String(number || '').replace(/\D/g, '');
    return digits ? `https://wa.me/${digits}` : '#';
}
</script>

<template>
<Head title="Member Contact Directory" />
<PublicLayout>
    <section class="bg-[#00351F]"><div class="mx-auto max-w-[1400px] px-5 py-8"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Administration</p><h1 class="mt-2 text-3xl font-extrabold text-white">Member Contact Directory</h1></div><AdminQuickLinks/></section><div class="mx-auto max-w-7xl px-5 py-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div><p class="mt-1 text-sm text-gray-600">Search authorised members and contact them using their preferred channels.</p></div>
            <Link href="/admin/dashboard" class="rounded-lg border px-4 py-2 text-sm font-semibold">Admin Dashboard</Link>
        </div>
        <div class="mb-5 rounded-xl border bg-white p-4">
            <label class="mb-2 block text-sm font-semibold text-gray-700">Search name, email, phone or city</label>
            <div class="flex gap-2"><div class="relative flex-1"><Search class="absolute left-3 top-3 h-4 w-4 text-gray-400"/><input :value="search" @change="applySearch" placeholder="Enter search term..." class="w-full rounded-lg border py-2.5 pl-9 pr-3"/></div></div>
            <p class="mt-2 text-xs text-gray-500">Phone numbers are private and visible only to authorised executives within their assigned scope.</p>
        </div>
        <div class="overflow-hidden rounded-xl border bg-white">
            <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Member</th><th class="px-4 py-3">Phone</th><th class="px-4 py-3">Set / Branch</th><th class="px-4 py-3">Location</th><th class="px-4 py-3">Contact</th></tr></thead>
                <tbody class="divide-y">
                    <tr v-for="member in members.data" :key="member.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3"><div class="font-bold text-gray-900">{{member.name}}</div><div class="text-xs text-gray-500">{{member.email}}</div><div class="mt-1 text-xs text-gray-500">Status: {{member.member_status || '—'}}</div></td>
                        <td class="px-4 py-3"><div>{{member.phone || 'Not provided'}}</div><div v-if="member.whatsapp_phone && member.whatsapp_phone !== member.phone" class="text-xs text-gray-500">WhatsApp: {{member.whatsapp_phone}}</div></td>
                        <td class="px-4 py-3"><div>{{member.graduation_set || '—'}}</div><div class="text-xs text-gray-500">{{member.branches.join(', ') || 'No approved branch'}}</div></td>
                        <td class="px-4 py-3">{{[member.city,member.country].filter(Boolean).join(', ') || '—'}}</td>
                        <td class="px-4 py-3"><div class="flex flex-wrap gap-2">
                            <a v-if="member.phone" :href="`tel:${member.phone}`" class="inline-flex items-center gap-1 rounded-lg border px-2.5 py-2 text-xs font-semibold"><Phone class="h-3.5 w-3.5"/> Call</a>
                            <a v-if="member.whatsapp_phone && member.whatsapp_consent" :href="wa(member.whatsapp_phone)" target="_blank" rel="noopener" class="inline-flex items-center gap-1 rounded-lg bg-green-700 px-2.5 py-2 text-xs font-semibold text-white"><MessageCircle class="h-3.5 w-3.5"/> WhatsApp</a>
                            <a :href="`mailto:${member.email}`" class="inline-flex items-center gap-1 rounded-lg border px-2.5 py-2 text-xs font-semibold"><Mail class="h-3.5 w-3.5"/> Email</a>
                        </div></td>
                    </tr>
                    <tr v-if="!members.data.length"><td colspan="5" class="px-4 py-10 text-center text-gray-500">No members found in your authorised scope.</td></tr>
                </tbody>
            </table></div>
            <div class="flex items-center justify-between border-t px-4 py-3 text-sm"><span class="text-gray-500">Showing {{members.from || 0}}–{{members.to || 0}} of {{members.total}}</span><div class="flex gap-2"><Link v-for="link in members.links" :key="link.label" :href="link.url || '#'" v-html="link.label" :class="['rounded border px-3 py-1.5',link.active?'bg-green-800 text-white':'',!link.url?'pointer-events-none opacity-40':'']"/></div></div>
        </div>
    </div>
</PublicLayout>
</template>
