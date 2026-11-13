<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access restricted | BBHS Old Boys Association</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#F7F9F7] text-gray-800">
    <header class="bg-[#00351F] text-white">
        <div class="mx-auto flex max-w-6xl items-center gap-4 px-5 py-5">
            <img src="{{ asset('images/bbhs/logo/bbhs-logo.png') }}" alt="BBHS" class="h-14 w-14 object-contain">
            <div>
                <p class="text-sm font-extrabold tracking-wide">BAPTIST BOYS' HIGH SCHOOL</p>
                <p class="text-xs uppercase tracking-widest text-white/70">Old Boys' Association · Abeokuta</p>
            </div>
        </div>
    </header>

    <main class="mx-auto flex min-h-[70vh] max-w-3xl items-center justify-center px-5 py-14">
        <section class="w-full rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm sm:p-12">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#F2F8F4] text-3xl font-black text-[#006B3C]">403</div>
            <p class="mt-6 text-xs font-extrabold uppercase tracking-[0.2em] text-[#B58A16]">Access restricted</p>
            <h1 class="mt-2 text-3xl font-extrabold text-[#00351F]">You don't have permission to view this page</h1>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-gray-600">
                This area is reserved for authorised administrators. You can still access your member dashboard and other areas available to your account.
            </p>
            @auth
                @php
                    $user = auth()->user();
                    $isGlobalAdmin = $user->isAdmin();
                    $hasExecutiveAccess = $user->leadershipAssignments()
                        ->where('is_active', true)
                        ->where(function ($q) { $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', now()->toDateString()); })
                        ->where(function ($q) { $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()->toDateString()); })
                        ->exists();
                @endphp
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ $isGlobalAdmin ? url('/admin/dashboard') : url('/dashboard') }}" class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white hover:bg-[#00351F]">
                        {{ $isGlobalAdmin ? 'Admin Dashboard' : 'Member Dashboard' }}
                    </a>
                    @if($hasExecutiveAccess && !$isGlobalAdmin)
                        <a href="{{ url('/admin/verifications') }}" class="rounded-lg border border-[#006B3C] px-5 py-3 text-sm font-bold text-[#006B3C] hover:bg-[#F2F8F4]">Review Verifications</a>
                    @endif
                    <a href="{{ url('/events') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50">Events</a>
                    <a href="{{ url('/') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50">Public Website</a>
                </div>
            @else
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ url('/login') }}" class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white">Member Login</a>
                    <a href="{{ url('/') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-sm font-bold text-gray-700">Public Website</a>
                </div>
            @endauth
            <p class="mt-8 text-xs text-gray-400">BBHS Old Boys' Association · Access is managed according to your assigned role.</p>
        </section>
    </main>
    <footer class="border-t border-gray-200 bg-white py-5 text-center text-xs text-gray-500">
        © {{ date('Y') }} BBHS Old Boys Association. All Rights Reserved.
    </footer>
</body>
</html>
