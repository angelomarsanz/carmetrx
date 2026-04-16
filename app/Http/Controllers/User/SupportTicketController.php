<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\UserFrontend\ConversationRequest;
use App\Models\SupportTicket as AdminSupportTicket;
use App\Models\SupportTicketConversation;
use App\Models\User;
use App\Models\User\BasicSetting;
use App\Models\User\SupportTicket;
use App\Models\User\TicketConversation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;

class SupportTicketController extends Controller
{


    public function settings()
    {
        $status = BasicSetting::where([['user_id', Auth::guard('web')->user()->id]])->pluck('support_ticket_status')->first();

        return view('user.support-ticket.settings', compact('status'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'support_ticket_status' => 'required|numeric'
        ]);

        BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update(
            ['support_ticket_status' => $request->support_ticket_status]
        );

        session()->flash('success', __('Updated successfully!'));

        return redirect()->back();
    }


    public function tickets(Request $request)
    {
        $ticketNumber = $ticketStatus = null;

        if ($request->filled('ticket_no')) {
            $ticketNumber = $request['ticket_no'];
        }
        if ($request->filled('ticket_status')) {
            $ticketStatus = $request['ticket_status'];
        }

        $user = User::find(Auth::guard('web')->user()->id);

        $queryResult['tickets'] = $user->tickets()->when($ticketNumber, function (Builder $query, $ticketNumber) {
            return $query->where('ticket_number', 'like', '%' . $ticketNumber . '%');
        })
            ->when($ticketStatus, function (Builder $query, $ticketStatus) {
                return $query->where('status', '=', $ticketStatus);
            })
            ->orderByDesc('id')
            ->paginate(10);
        return view('user.support-ticket.tickets', $queryResult);
    }

    public function assignAdmin(Request $request, $id)
    {
        $rule = [
            'admin_id' => 'required'
        ];

        $message = [
            'admin_id.required' => __('Please, select an admin')
        ];

        $validator = Validator::make($request->all(), $rule, $message);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $ticket = SupportTicket::query()->find($id);

        $ticket->update([
            'admin_id' => $request->admin_id
        ]);

        $request->session()->flash('success', __('Admin assigned successfully!'));

        return Response::json(['status' => 'success', 200]);
    }

    public function conversation($id)
    {

        $ticket = SupportTicket::query()->find($id);
        $queryResult['ticket'] = $ticket;

        $queryResult['conversations'] = $ticket->conversations()->get();

        return view('user.support-ticket.conversation', $queryResult);
    }

    public function close($id)
    {
        $ticket = SupportTicket::query()->find($id);

        $ticket->update([
            'status' => 'closed'
        ]);

        return redirect()->back()->with('success', __('Ticket has been closed!'));
    }

    public function storeTempFile(Request $request)
    {
        // deleting other temp files
        $tempFiles = glob(public_path('assets/file/temp/*'));

        if (count($tempFiles) > 0) {
            foreach ($tempFiles as $file) {
                @unlink($file);
            }
        }

        // storing new file as a temp file
        $file = $request->file('attachment');
        UploadFile::store('assets/file/temp/', $file);

        return Response::json(['status' => 'success'], 200);
    }

    public function reply(ConversationRequest $request, $id)
    {
        // deleting temp files
        $tempFiles = glob(public_path('assets/file/temp/*'));

        if (count($tempFiles) > 0) {
            foreach ($tempFiles as $file) {
                @unlink($file);
            }
        }

        // storing new file
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = UploadFile::store('assets/file/ticket-files/', $file);
        }

        // storing data in db
        $conversation = new TicketConversation();
        $conversation->ticket_id = $id;
        $conversation->person_id = Auth::guard('web')->user()->id;
        $conversation->person_type = 'user';
        $conversation->reply = Purifier::clean($request->reply);
        $conversation->attachment = isset($fileName) ? $fileName : NULL;
        $conversation->save();

        // changing ticket status
        $ticket = $conversation->ticket()->first();

        $ticket->update([
            'status' => 'open'
        ]);

        session()->flash('success', 'Reply submitted successfully.');

        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->deleteTicket($id);

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $this->deleteTicket($id);
        }

        session()->flash('success', __('Deleted successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function deleteTicket($id)
    {
        $ticket = SupportTicket::query()->find($id);

        // delete ticket conversations
        $conversations = $ticket->conversations()->get();

        if (count($conversations) > 0) {
            foreach ($conversations as $conversation) {
                @unlink(public_path('assets/file/ticket-files/') . $conversation->attachment);

                $conversation->delete();
            }
        }

        // delete ticket
        @unlink(public_path('assets/file/ticket-files/') . $ticket->attachment);

        $ticket->delete();
    }

    public function userTickets()
    {

        $authUser = Auth::guard('web')->user();

        $queryResult['tickets'] = $authUser->user_tickets()->orderByDesc('id')->paginate(10);

        return view('user.support-ticket.user-tickets', $queryResult);
    }

    public function userCreateTicket()
    {
        return view('user.support-ticket.add-ticket');
    }
    public function userStoreTicket(Request $request)
    {
        $rules = [
            'email' => 'required',
            'subject' => 'required',
        ];

        if ($request->hasFile('attachment')) {
            $rules['attachment'] = 'mimes:zip';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $in = $request->all();
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $filename = uniqid() . '.' . $attachment->getClientOriginalExtension();
            $attachment->move(public_path('assets/file/ticket-files/'), $filename);
            $in['attachment'] = $filename;
        }
        $in['user_id'] = Auth::guard('web')->user()->id;
        $in['status'] = 1;

        $in['ticket_number'] = uniqid();
        $in['description'] = Purifier::clean($request->description, 'youtube');
        AdminSupportTicket::create($in);

        Session::flash('success', 'Support Ticket Created Successfully..!');
        return back();
    }

    public function userConversation($id)
    {
        $ticket = AdminSupportTicket::findOrFail($id);
        $queryResult['ticket'] = $ticket;

        $queryResult['conversations'] = $ticket->conversations()->get();

        return view('user.support-ticket.user-conversation', $queryResult);
    }
    public function userTicketReply(ConversationRequest $request, $id)
    {
        // deleting temp files
        $tempFiles = glob(public_path('assets/file/temp/*'));

        if (count($tempFiles) > 0) {
            foreach ($tempFiles as $file) {
                @unlink($file);
            }
        }

        // storing new file
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = UploadFile::store('assets/file/ticket-files/', $file);
        }

        $conversation = new SupportTicketConversation();
        $conversation->support_ticket_id = $id;
        $conversation->person_id = Auth::guard('web')->user()->id;
        $conversation->person_type = 'user';
        $conversation->replay = Purifier::clean($request->reply);
        $conversation->attachment = isset($fileName) ? $fileName : NULL;
        $conversation->save();

        session()->flash('success', __('Reply submitted successfully!'));

        return redirect()->back();
    }
    public function userStoreTempFile(Request $request)
    {
        // deleting other temp files
        $tempFiles = glob(public_path('assets/file/temp/*'));

        if (count($tempFiles) > 0) {
            foreach ($tempFiles as $file) {
                @unlink($file);
            }
        }

        // storing new file as a temp file
        $file = $request->file('attachment');
        UploadFile::store('assets/file/temp/', $file);

        return Response::json(['status' => 'success'], 200);
    }

    public function userDestroy($id)
    {
        $ticket = AdminSupportTicket::findOrFail($id);
        $ticket->deleteTicket();

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    public function userBulkDestroy(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $ticket = AdminSupportTicket::findOrFail($id);
            $ticket->deleteTicket();
        }

        session()->flash('success', 'Tickets has deleted!');

        return Response::json(['status' => 'success'], 200);
    }
}
