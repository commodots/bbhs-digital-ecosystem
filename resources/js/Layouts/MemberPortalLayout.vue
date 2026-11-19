<script setup>
import { computed, ref } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { LayoutDashboard, UserRound, IdCard, Users, CalendarDays, Newspaper, Image, Wallet, HeartHandshake, Receipt, MessageCircle, Bell, Settings, Globe, LogOut, Menu, X, ShieldCheck, ChevronRight } from 'lucide-vue-next';
const page = usePage();
const user = computed(() => page.props.auth?.user ?? {});
const mobileOpen = ref(false);
const profileMenuOpen = ref(false);
const logoutForm = useForm({});
const adminAccess = computed(() => !!(user.value.is_admin || user.value.is_global_executive || user.value.is_executive || page.props.executiveAccess));
const isWelfareTeam = computed(() => !!user.value.welfare_team);
const groups = computed(() => [
 { title:'MY DASHBOARD', links:[['Member Dashboard','/dashboard',LayoutDashboard]] },
 { title:'COMMUNITY', links:[['Member Directory','/alumni-directory',Users],['Events & Meetings','/events',CalendarDays],['News & Announcements','/news',Newspaper],['Photo Gallery','/gallery',Image],['Fundraising','/fundraising',HeartHandshake]] },
 { title:'FINANCE & SUPPORT', links:[['Membership Dues','/dues',Wallet],['My Contributions','/my-fundraising',HeartHandshake],['Welfare Support','/welfare',HeartHandshake], ...(isWelfareTeam.value ? [['Welfare Team Workspace','/admin/welfare',ShieldCheck],['Welfare Reports','/admin/welfare-reports',Receipt]] : [])] },
 { title:'MESSAGES', links:[['Member Messages','/messages',MessageCircle],['Notifications','/notifications',Bell]] },
]);
const active = href => page.url === href || (href !== '/dashboard' && page.url.startsWith(href + '/'));
const logout = () => logoutForm.post('/logout');
</script>
<template>
<div class="min-h-screen bg-[#F5F8F5] text-slate-800">
 <div v-if="mobileOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="mobileOpen=false"></div>
 <aside :class="['fixed inset-y-0 left-0 z-50 flex w-[270px] flex-col bg-gradient-to-b from-[#064B37] to-[#00351F] text-white shadow-xl transition-transform duration-200 lg:translate-x-0', mobileOpen?'translate-x-0':'-translate-x-full']">
  <div class="flex h-20 items-center justify-between border-b border-white/10 px-5">
   <a href="/dashboard" class="flex items-center gap-3"><img :src="'/images/bbhs/logo/bbhs-logo.png'" alt="BBHS" class="h-12 w-12 rounded-lg bg-white object-contain p-1"/><span><strong class="block text-sm tracking-wide">BBHS ALUMNI</strong><small class="text-[10px] uppercase tracking-[.2em] text-emerald-100">Member Portal</small></span></a>
   <button class="rounded-lg p-2 hover:bg-white/10 lg:hidden" @click="mobileOpen=false" aria-label="Close navigation"><X :size="20"/></button>
  </div>
  <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-5">
   <section v-for="group in groups" :key="group.title"><p class="mb-2 px-3 text-[10px] font-extrabold tracking-[.2em] text-emerald-200/70">{{group.title}}</p>
    <a v-for="link in group.links" :key="link[0]" :href="link[1]" @click="mobileOpen=false" :class="['mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition',active(link[1])?'bg-[#E8C65A] text-[#173D2D] shadow-sm':'text-white/85 hover:bg-white/10 hover:text-white']"><component :is="link[2]" :size="17"/><span class="flex-1">{{link[0]}}</span><ChevronRight v-if="active(link[1])" :size="14"/></a>
   </section>
  </nav>
  <div class="space-y-1 border-t border-white/10 p-3">
   
   <a v-if="adminAccess" href="/admin/dashboard" class="flex items-center gap-3 rounded-xl bg-white/10 px-3 py-2.5 text-sm font-semibold text-[#F3D879] hover:bg-white/15"><ShieldCheck :size="17"/>Administration</a>
   <a href="/" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-white/85 hover:bg-white/10"><Globe :size="17"/>Public Website</a>
   <button @click="logout" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm text-white/85 hover:bg-white/10"><LogOut :size="17"/>Sign Out</button>
  </div>
 </aside>
 <div class="min-h-screen lg:pl-[270px]">
  <header class="sticky top-0 z-30 flex h-[72px] items-center justify-between border-b border-slate-200 bg-white/95 px-4 shadow-sm backdrop-blur sm:px-7">
   <div class="flex items-center gap-3"><button @click="mobileOpen=true" class="rounded-lg border border-slate-200 p-2 text-[#07543F] hover:bg-emerald-50 lg:hidden" aria-label="Open navigation"><Menu :size="20"/></button><div><p class="text-[10px] font-bold uppercase tracking-[.18em] text-[#08734D]">BBHS Old Boys Association</p><p class="text-sm font-bold text-slate-700">Member Portal</p></div></div>
   <div class="flex items-center gap-2 sm:gap-3">
    <a href="/messages" class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100" aria-label="Messages"><MessageCircle :size="19"/><span v-if="page.props.messages?.unread_count" class="absolute right-1 top-1 rounded-full bg-emerald-600 px-1 text-[9px] font-bold text-white">{{ page.props.messages.unread_count }}</span></a>
    <a href="/notifications" class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100" aria-label="Notifications"><Bell :size="19"/><span v-if="page.props.notifications?.unread_count" class="absolute right-1 top-1 rounded-full bg-red-500 px-1 text-[9px] font-bold text-white">{{ page.props.notifications.unread_count }}</span></a>
    <div class="relative">
      <button type="button" @click="profileMenuOpen=!profileMenuOpen" class="flex items-center gap-2 rounded-full p-1.5 hover:bg-slate-100" aria-label="Open profile menu">
        <img v-if="user.profile_photo_url" :src="user.profile_photo_url" alt="Profile photograph" class="h-9 w-9 rounded-full border border-slate-200 object-cover"/>
        <span v-else class="flex h-9 w-9 items-center justify-center rounded-full bg-[#E8C65A] font-bold text-[#173D2D]">{{(user.name||'M').charAt(0).toUpperCase()}}</span>
        <span class="hidden text-left sm:block"><span class="block max-w-32 truncate text-xs font-bold text-slate-800">{{user.name || 'Member'}}</span><span class="block text-[10px] text-slate-500">My Profile</span></span>
      </button>
      <div v-if="profileMenuOpen" class="absolute right-0 top-full z-50 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl">
        <a href="/profile" class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-emerald-50">My Profile</a>
        <button @click="logout" class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-red-600 hover:bg-red-50">Sign out</button>
      </div>
    </div>
  </div>
  </header>
  <main class="min-h-[calc(100vh-72px)]"><slot/></main>
  <footer class="border-t border-slate-200 bg-white px-6 py-4 text-center text-xs text-slate-500">© {{new Date().getFullYear()}} BBHS Old Boys Association</footer>
 </div>
</div>
</template>
