<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\User;
use App\Notifications\BbhsPlatformNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MemberChatController extends Controller
{
    private function ensureApprovedMember(Request $request): int
    {
        $user = $request->user();
        $approved = $user && $user->member_status === 'approved' && ($user->alumniProfile?->approved ?? false);
        abort_unless($user && ($user->isAdmin() || $approved), 403, 'Only approved alumni and administrators can use member chat.');
        return (int) $user->id;
    }

    private function profilesAreSameSet(int $firstId, int $secondId): bool
    {
        $profiles = AlumniProfile::whereIn('user_id', [$firstId, $secondId])->get()->keyBy('user_id');
        $a = $profiles->get($firstId);
        $b = $profiles->get($secondId);
        if (! $a || ! $b) return false;
        if ($a->graduation_set_id && $b->graduation_set_id) return (int) $a->graduation_set_id === (int) $b->graduation_set_id;
        $normalise = fn ($value) => strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
        return $normalise($a->class_set) !== '' && $normalise($a->class_set) === $normalise($b->class_set);
    }

    private function isBlockedEitherWay(int $a, int $b): bool
    {
        return DB::table('member_chat_blocks')->where(function ($q) use ($a, $b) {
            $q->where('blocker_id', $a)->where('blocked_id', $b);
        })->orWhere(function ($q) use ($a, $b) {
            $q->where('blocker_id', $b)->where('blocked_id', $a);
        })->exists();
    }

    private function conversationBetween(int $a, int $b): ?object
    {
        $one = min($a, $b); $two = max($a, $b);
        return DB::table('member_conversations')->where('user_one_id', $one)->where('user_two_id', $two)->first();
    }

    private function createConversation(int $a, int $b): object
    {
        $one = min($a, $b); $two = max($a, $b);
        $existing = $this->conversationBetween($a, $b);
        if ($existing) return $existing;
        try {
            $id = DB::table('member_conversations')->insertGetId(['user_one_id'=>$one,'user_two_id'=>$two,'created_at'=>now(),'updated_at'=>now()]);
            return (object) ['id'=>$id, 'user_one_id'=>$one, 'user_two_id'=>$two];
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->conversationBetween($a, $b) ?? throw $e;
        }
    }

    public function index(Request $request)
    {
        $userId = $this->ensureApprovedMember($request);
        return Inertia::render('Messages/Index', [
            'conversations' => $this->conversationList($userId),
            'activeConversation' => null,
            'messages' => [],
            'incomingRequests' => $this->requestList($userId, 'incoming'),
            'outgoingRequests' => $this->requestList($userId, 'outgoing'),
        ]);
    }

    public function start(Request $request)
    {
        $senderId = $this->ensureApprovedMember($request);
        $data = $request->validate(['recipient_profile_id' => ['required','integer','exists:alumni_profiles,id']]);
        $profile = AlumniProfile::with('user')->findOrFail($data['recipient_profile_id']);
        abort_unless($profile->approved && $profile->directory_visible, 404);
        abort_unless($profile->user && $profile->user->member_status === 'approved' && (int)$profile->user->id !== $senderId, 403);
        $recipientId = (int) $profile->user_id;
        abort_if($this->isBlockedEitherWay($senderId, $recipientId), 403, 'This member is unavailable for chat.');

        if ($this->profilesAreSameSet($senderId, $recipientId)) {
            return redirect()->route('messages.show', $this->createConversation($senderId, $recipientId)->id);
        }

        $accepted = DB::table('member_connection_requests')->where('requester_id',$senderId)->where('recipient_id',$recipientId)->where('status','accepted')->exists()
            || DB::table('member_connection_requests')->where('requester_id',$recipientId)->where('recipient_id',$senderId)->where('status','accepted')->exists();
        if ($accepted) return redirect()->route('messages.show', $this->createConversation($senderId, $recipientId)->id);

        $prior = DB::table('member_connection_requests')->where('requester_id',$senderId)->where('recipient_id',$recipientId)->first();
        if ($prior) {
            if ($prior->status === 'pending') return redirect()->route('messages.index')->with('success', 'Your connection request is already pending.');
            if ($prior->status === 'declined') return redirect()->route('messages.index')->with('error', 'This member declined your request. You cannot send another request unless they reopen the connection.');
        }
        // A request in the opposite direction is an invitation from the recipient; accept it as mutual consent.
        $reverse = DB::table('member_connection_requests')->where('requester_id',$recipientId)->where('recipient_id',$senderId)->first();
        if ($reverse && $reverse->status === 'pending') {
            DB::table('member_connection_requests')->where('id',$reverse->id)->update(['status'=>'accepted','responded_at'=>now(),'updated_at'=>now()]);
            return redirect()->route('messages.show', $this->createConversation($senderId, $recipientId)->id);
        }
        if ($reverse && $reverse->status === 'declined') return redirect()->route('messages.index')->with('error', 'This member has declined a previous connection request.');

        DB::table('member_connection_requests')->insert(['requester_id'=>$senderId,'recipient_id'=>$recipientId,'status'=>'pending','created_at'=>now(),'updated_at'=>now()]);
        User::find($recipientId)?->notify(new BbhsPlatformNotification('New alumni connection request', ($request->user()->name ?: 'A BBHS member').' wants to connect with you.','message','/messages',['database_only'=>true]));
        return redirect()->route('messages.index')->with('success', 'Connection request sent. You can chat when the member accepts.');
    }

    public function acceptRequest(Request $request, int $connectionRequest)
    {
        $userId = $this->ensureApprovedMember($request);
        $row = DB::table('member_connection_requests')->where('id',$connectionRequest)->where('recipient_id',$userId)->first();
        abort_unless($row && in_array($row->status, ['pending','declined'], true), 404);
        abort_if($this->isBlockedEitherWay((int)$row->requester_id, $userId), 403, 'Remove the chat block before accepting this request.');
        DB::table('member_connection_requests')->where('id',$row->id)->update(['status'=>'accepted','responded_at'=>now(),'updated_at'=>now()]);
        $conversation = $this->createConversation($userId, (int)$row->requester_id);
        User::find((int)$row->requester_id)?->notify(new BbhsPlatformNotification('Connection request accepted','Your BBHS connection request was accepted. You can now chat.','message','/messages/'.$conversation->id,['database_only'=>true]));
        return redirect()->route('messages.show',$conversation->id)->with('success','Connection accepted. You can now chat.');
    }

    public function declineRequest(Request $request, int $connectionRequest)
    {
        $userId = $this->ensureApprovedMember($request);
        $row = DB::table('member_connection_requests')->where('id',$connectionRequest)->where('recipient_id',$userId)->where('status','pending')->first();
        abort_unless($row,404);
        DB::table('member_connection_requests')->where('id',$row->id)->update(['status'=>'declined','responded_at'=>now(),'updated_at'=>now()]);
        return back()->with('success','Connection request declined. The requester cannot send another request unless you later accept it.');
    }

    public function blockMember(Request $request, int $member)
    {
        $userId = $this->ensureApprovedMember($request);
        abort_if($member === $userId, 422);
        User::whereKey($member)->firstOrFail();
        DB::table('member_chat_blocks')->updateOrInsert(['blocker_id'=>$userId,'blocked_id'=>$member],['created_at'=>now(),'updated_at'=>now()]);
        return redirect()->route('messages.index')->with('success','Member blocked. Chat and connection requests are disabled in both directions.');
    }

    public function unblockMember(Request $request, int $member)
    {
        $userId = $this->ensureApprovedMember($request);
        DB::table('member_chat_blocks')->where('blocker_id',$userId)->where('blocked_id',$member)->delete();
        return back()->with('success','Member unblocked. A new request may be sent if no declined request already exists.');
    }

    public function show(Request $request, int $conversation)
    {
        $userId = $this->ensureApprovedMember($request);
        $thread = $this->conversationForUser($conversation, $userId);
        $otherId = (int) ($thread->user_one_id == $userId ? $thread->user_two_id : $thread->user_one_id);
        abort_if($this->isBlockedEitherWay($userId,$otherId),403,'This conversation is unavailable.');
        if (! $this->profilesAreSameSet($userId,$otherId)) {
            $connected = DB::table('member_connection_requests')->where('status','accepted')->where(function($q) use($userId,$otherId){$q->where(function($inner) use($userId,$otherId){$inner->where('requester_id',$userId)->where('recipient_id',$otherId);})->orWhere(function($inner) use($userId,$otherId){$inner->where('requester_id',$otherId)->where('recipient_id',$userId);});})->exists();
            abort_unless($connected,403,'A connection request must be accepted before chatting across graduation sets.');
        }
        DB::table('member_messages')->where('conversation_id',$conversation)->where('sender_id','<>',$userId)->whereNull('read_at')->update(['read_at'=>now()]);
        $other = DB::table('users')->where('id',$otherId)->first(['id','name']);
        $messages = DB::table('member_messages as m')->join('users as u','u.id','=','m.sender_id')->where('m.conversation_id',$conversation)->orderBy('m.id')->limit(300)->get(['m.id','m.sender_id','m.body','m.read_at','m.created_at','u.name as sender_name'])->map(fn($m)=>['id'=>$m->id,'sender_id'=>$m->sender_id,'sender_name'=>$m->sender_name,'body'=>$m->body,'read_at'=>$m->read_at,'created_at'=>$m->created_at,'mine'=>(int)$m->sender_id===$userId]);
        return Inertia::render('Messages/Index', ['conversations'=>$this->conversationList($userId),'activeConversation'=>['id'=>$conversation,'other_user'=>$other],'messages'=>$messages,'incomingRequests'=>$this->requestList($userId,'incoming'),'outgoingRequests'=>$this->requestList($userId,'outgoing')]);
    }

    public function send(Request $request, int $conversation)
    {
        $senderId = $this->ensureApprovedMember($request);
        $thread = $this->conversationForUser($conversation,$senderId);
        $recipientId = (int)($thread->user_one_id == $senderId ? $thread->user_two_id : $thread->user_one_id);
        abort_if($this->isBlockedEitherWay($senderId,$recipientId),403,'Messaging is disabled for this member.');
        if (! $this->profilesAreSameSet($senderId,$recipientId)) {
            $connected = DB::table('member_connection_requests')->where('status','accepted')->where(function($q)use($senderId,$recipientId){$q->where(function($inner)use($senderId,$recipientId){$inner->where('requester_id',$senderId)->where('recipient_id',$recipientId);})->orWhere(function($inner)use($senderId,$recipientId){$inner->where('requester_id',$recipientId)->where('recipient_id',$senderId);});})->exists();
            abort_unless($connected,403,'Connection approval is required before messaging.');
        }
        $data = $request->validate(['body'=>['required','string','min:1','max:5000']]);
        $messageId = DB::table('member_messages')->insertGetId(['conversation_id'=>$conversation,'sender_id'=>$senderId,'body'=>trim($data['body']),'created_at'=>now(),'updated_at'=>now()]);
        DB::table('member_conversations')->where('id',$conversation)->update(['updated_at'=>now()]);
        User::find($recipientId)?->notify(new BbhsPlatformNotification('New BBHS message',($request->user()->name ?: 'A BBHS member').' sent you a private message.','message','/messages/'.$conversation,['conversation_id'=>$conversation,'message_id'=>$messageId,'database_only'=>true]));
        return redirect()->route('messages.show',$conversation);
    }

    private function requestList(int $userId, string $direction)
    {
        $column = $direction === 'incoming' ? 'recipient_id' : 'requester_id';
        $otherColumn = $direction === 'incoming' ? 'requester_id' : 'recipient_id';
        return DB::table('member_connection_requests as r')->join('users as u','u.id','=','r.'.$otherColumn)->where('r.'.$column,$userId)->whereIn('r.status',$direction === 'incoming' ? ['pending','declined'] : ['pending','declined'])->orderByDesc('r.updated_at')->get(['r.id','r.status','r.created_at','r.responded_at','u.id as member_id','u.name as member_name']);
    }

    private function conversationForUser(int $conversationId, int $userId): object
    {
        $thread = DB::table('member_conversations')->where('id',$conversationId)->first();
        abort_unless($thread && ((int)$thread->user_one_id===$userId || (int)$thread->user_two_id===$userId),404);
        return $thread;
    }

    private function conversationList(int $userId)
    {
        return DB::table('member_conversations as c')->join('users as u',function($join)use($userId){$join->on('u.id','=',DB::raw("CASE WHEN c.user_one_id = {$userId} THEN c.user_two_id ELSE c.user_one_id END"));})->leftJoin('member_messages as m',function($join){$join->on('m.id','=',DB::raw('(SELECT MAX(mm.id) FROM member_messages mm WHERE mm.conversation_id = c.id)'));})->where(fn($q)=>$q->where('c.user_one_id',$userId)->orWhere('c.user_two_id',$userId))->select(['c.id','c.updated_at','u.id as other_user_id','u.name as other_name','m.body as last_message','m.created_at as last_message_at'])->orderByDesc('c.updated_at')->get()->filter(function($row)use($userId){return ! $this->isBlockedEitherWay($userId,(int)$row->other_user_id);})->map(function($row)use($userId){$row->unread_count=DB::table('member_messages')->where('conversation_id',$row->id)->whereNull('read_at')->where('sender_id','<>',$userId)->count();return $row;})->values();
    }
}
