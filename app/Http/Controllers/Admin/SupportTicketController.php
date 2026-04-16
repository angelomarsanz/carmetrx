<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\SupportTicket\ConversationRequest;
use App\Models\Admin;
use App\Models\BasicSetting;
use App\Models\SupportTicket;
use App\Models\SupportTicketConversation;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Mews\Purifier\Facades\Purifier;

class SupportTicketController extends Controller
{
    public function settings()
    {
        $status = BasicSetting::pluck('support_ticket_status')->first();

        return view('admin.support-ticket.settings', compact('status'));
    }
    public function updateSettings(Request $request)
    {
        $request->validate([
            'support_ticket_status' => 'required|numeric'
        ]);
        $bss = BasicSetting::all();

        foreach ($bss as  $bs) {

            $bs->update(
                ['support_ticket_status' => $request->support_ticket_status]
            );
        }

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
        $authAdmin = Auth::guard('admin')->user();

        $queryResult['tickets'] =  SupportTicket::when($ticketNumber, function (Builder $query, $ticketNumber) {
            return $query->where('ticket_number', 'like', '%' . $ticketNumber . '%');
        })
            ->when($ticketStatus, function (Builder $query, $ticketStatus) {
                if ($ticketStatus == 'pending') {
                    return $query->where('status', '=', 1);
                } elseif ($ticketStatus == 'open') {
                    return $query->where('status', '=', 2);
                } elseif ($ticketStatus == 'closed') {
                    return $query->where('status', '=', 3);
                } else {
                    return $query->where('status', '=', $ticketStatus);
                }
            })
            ->orderByDesc('id')
            ->paginate(10);

        $queryResult['admins'] = Admin::query()->where('status', '=', 1)->get();

        return view('admin.support-ticket.tickets', $queryResult);
    }
    public function conversation($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $queryResult['ticket'] = $ticket;
        $queryResult['conversations'] = $ticket->conversations()->get();

        return view('admin.support-ticket.conversation', $queryResult);
    }

    public function reply(ConversationRequest $request, $id)
    {

        // deleting temp files
        $tempFiles = glob('assets/file/temp/*');

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
        $conversation = new SupportTicketConversation();
        $conversation->support_ticket_id = $id;
        $conversation->person_id = Auth::guard('admin')->user()->id;
        $conversation->person_type = 'admin';
        $conversation->replay = Purifier::clean($request->reply);
        $conversation->attachment = isset($fileName) ? $fileName : NULL;

        $conversation->save();

        // changing ticket status
        $ticket = $conversation->ticket()->first();

        $ticket->update([
            'status' => 2
        ]);

        $request->session()->flash('success', __('Reply submitted successfully!'));

        return redirect()->back();
    }
    public function close($id)
    {
        $ticket = SupportTicket::query()->find($id);

        $ticket->update([
            'status' => 3
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

        session()->flash('success', 'Tickets has deleted!');

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
}
