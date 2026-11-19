<script setup>
import { computed, onBeforeUnmount, onMounted, ref, nextTick, watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import MemberPortalLayout from '../../Layouts/MemberPortalLayout.vue';
import { MessageCircle, Send, ArrowLeft, UserRound, RefreshCw, UserPlus, Check, X, Ban } from 'lucide-vue-next';

const props = defineProps({
    conversations: { type: Array, default: () => [] },
    activeConversation: { type: Object, default: null },
    messages: { type: Array, default: () => [] },
    incomingRequests: { type: Array, default: () => [] },
    outgoingRequests: { type: Array, default: () => [] },
});
const page = usePage();
const form = useForm({ body: '' });
const messageList = ref(null);
const activeId = computed(() => props.activeConversation?.id ?? null);
const refresh = () => router.reload({ only: ['conversations', 'messages', 'activeConversation'], preserveState: true, preserveScroll: true });
let poller = null;

const send = () => {
    if (!activeId.value || !form.body.trim()) return;
    form.post(`/messages/${activeId.value}/send`, {
        preserveScroll: true,
        onSuccess: () => { form.reset(); scrollToBottom(); },
    });
};
const scrollToBottom = async () => {
    await nextTick();
    if (messageList.value) messageList.value.scrollTop = messageList.value.scrollHeight;
};
const dateLabel = value => value ? new Date(value).toLocaleString() : '';
watch(() => props.messages.length, () => scrollToBottom());
onMounted(() => {
    scrollToBottom();
    poller = window.setInterval(refresh, 10000);
});
onBeforeUnmount(() => window.clearInterval(poller));
</script>

<template>
<MemberPortalLayout>
    <section class="bg-[#00351F]">
        <div class="mx-auto max-w-6xl px-5 py-9 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">BBHS Communication Centre</p>
            <h1 class="mt-2 flex items-center gap-3 text-3xl font-extrabold text-white"><MessageCircle :size="28"/> Member Messages</h1>
            <p class="mt-2 text-sm text-white/75">Private conversations between approved BBHS alumni.</p>
        </div>
    </section>

    <section class="min-h-[65vh] bg-[#F7F9F7] py-8">
        <div class="mx-auto grid max-w-6xl gap-5 px-5 lg:grid-cols-[320px_1fr] lg:px-8">
            <aside class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-100 p-4">
                    <h2 class="font-extrabold text-[#00351F]">Conversations</h2>
                    <button @click="refresh" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100" aria-label="Refresh"><RefreshCw :size="16"/></button>
                </div>
                <div v-if="incomingRequests.length" class="border-b border-gray-100 p-4"><h3 class="mb-3 text-xs font-extrabold uppercase tracking-wide text-[#00351F]">Connection requests</h3><div v-for="item in incomingRequests" :key="item.id" class="mb-3 rounded-xl bg-emerald-50 p-3"><p class="text-sm font-bold text-gray-800">{{ item.member_name }}</p><p class="mt-1 text-xs text-gray-500">{{ item.status === 'declined' ? 'Previously declined request — you may still accept it.' : 'Wants to connect with you.' }}</p><div class="mt-2 flex gap-2"><Link :href="`/messages/requests/${item.id}/accept`" method="post" as="button" class="rounded-lg bg-[#006B3C] px-3 py-1.5 text-xs font-bold text-white"><Check :size=13 class="mr-1 inline"/>Accept</Link><Link v-if="item.status === 'pending'" :href="`/messages/requests/${item.id}/decline`" method="post" as="button" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-bold text-gray-600"><X :size=13 class="mr-1 inline"/>Decline</Link></div></div></div><div v-if="outgoingRequests.length" class="border-b border-gray-100 p-4"><h3 class="mb-2 text-xs font-extrabold uppercase tracking-wide text-gray-500">Sent requests</h3><div v-for="item in outgoingRequests" :key="item.id" class="flex justify-between py-2 text-xs"><span class="font-semibold text-gray-700">{{ item.member_name }}</span><span :class="item.status === 'declined' ? 'text-red-600' : 'text-amber-600'">{{ item.status === 'declined' ? 'Declined' : 'Awaiting response' }}</span></div></div><div v-if="!conversations.length" class="p-7 text-center text-sm text-gray-500">
                    <UserRound class="mx-auto mb-3 text-gray-300" :size="34"/>
                    No conversations yet. Visit the alumni directory and select a member to start a chat.
                    <a href="/alumni-directory" class="mt-3 inline-block font-bold text-[#087A3A]">Browse alumni</a>
                </div>
                <a v-for="thread in conversations" :key="thread.id" :href="`/messages/${thread.id}`" class="flex items-start gap-3 border-b border-gray-100 p-4 hover:bg-emerald-50" :class="activeId === thread.id ? 'bg-emerald-50' : ''">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EEF8F2] font-bold text-[#006B3C]">{{ (thread.other_name || 'A').charAt(0).toUpperCase() }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center justify-between gap-2"><strong class="truncate text-sm text-[#00351F]">{{ thread.other_name }}</strong><span v-if="thread.unread_count" class="rounded-full bg-red-600 px-2 py-0.5 text-[10px] font-bold text-white">{{ thread.unread_count }}</span></span>
                        <span class="mt-1 block truncate text-xs text-gray-500">{{ thread.last_message || 'Conversation started' }}</span>
                        <span class="mt-1 block text-[10px] text-gray-400">{{ dateLabel(thread.last_message_at || thread.updated_at) }}</span>
                    </span>
                </a>
            </aside>

            <main class="flex min-h-[520px] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <template v-if="activeConversation">
                    <header class="flex items-center justify-between gap-3 border-b border-gray-100 p-4"><div class="flex items-center gap-3">
                        <a href="/messages" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden"><ArrowLeft :size="18"/></a>
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#EEF8F2] font-bold text-[#006B3C]">{{ (activeConversation.other_user?.name || 'A').charAt(0).toUpperCase() }}</span>
                        <div><h2 class="font-extrabold text-[#00351F]">{{ activeConversation.other_user?.name }}</h2><p class="text-xs text-gray-500">Private member conversation</p></div></div><Link :href="`/messages/members/${activeConversation.other_user?.id}/block`" method="post" as="button" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50"><Ban :size=14 class="mr-1 inline"/>Block</Link>
                    </header>
                    <div ref="messageList" class="flex-1 space-y-3 overflow-y-auto bg-[#FAFCFA] p-4 sm:p-6">
                        <div v-for="message in messages" :key="message.id" class="flex" :class="message.mine ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[85%] rounded-2xl px-4 py-3 sm:max-w-[70%]" :class="message.mine ? 'rounded-br-sm bg-[#006B3C] text-white' : 'rounded-bl-sm bg-white text-gray-700 shadow-sm ring-1 ring-gray-100'">
                                <p class="whitespace-pre-wrap break-words text-sm leading-6">{{ message.body }}</p>
                                <p class="mt-2 text-right text-[10px]" :class="message.mine ? 'text-white/65' : 'text-gray-400'">{{ dateLabel(message.created_at) }}</p>
                            </div>
                        </div>
                        <p v-if="!messages.length" class="py-12 text-center text-sm text-gray-400">Send the first message to start this conversation.</p>
                    </div>
                    <form @submit.prevent="send" class="border-t border-gray-100 p-3 sm:p-4">
                        <div class="flex items-end gap-2">
                            <textarea v-model="form.body" rows="2" maxlength="5000" placeholder="Write a message..." class="min-h-[48px] flex-1 resize-y rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-[#087A3A]" @keydown.ctrl.enter.prevent="send"></textarea>
                            <button type="submit" :disabled="form.processing || !form.body.trim()" class="inline-flex h-12 items-center gap-2 rounded-xl bg-[#006B3C] px-4 text-sm font-bold text-white hover:bg-[#00552F] disabled:opacity-50"><Send :size="16"/> Send</button>
                        </div>
                        <p v-if="form.errors.body" class="mt-2 text-xs text-red-600">{{ form.errors.body }}</p>
                        <p class="mt-2 text-[10px] text-gray-400">Messages refresh automatically. Ctrl + Enter to send.</p>
                    </form>
                </template>
                <div v-else class="flex flex-1 flex-col items-center justify-center p-10 text-center">
                    <MessageCircle :size="52" class="text-[#006B3C]/20"/>
                    <h2 class="mt-4 text-lg font-extrabold text-[#00351F]">Your BBHS conversations</h2>
                    <p class="mt-2 max-w-sm text-sm text-gray-500">Choose a conversation or find an alumnus in the directory to send a private message.</p>
                    <a href="/alumni-directory" class="mt-5 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white">Find an alumnus</a>
                </div>
            </main>
        </div>
    </section>
</MemberPortalLayout>
</template>
