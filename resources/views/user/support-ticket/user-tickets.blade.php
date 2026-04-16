@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">
            {{ __('All Tickets') }}
        </h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('user-dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Support Tickets') }}</a>
            </li>
            
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <form id="searchForm" action="{{ route('admin_user.support_tickets') }}" method="GET">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card-title">{{ __('All Tickets') }}</div>

                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group p-0">

                                            <input name="ticket_no" type="text" class="form-control"
                                                placeholder="{{ __('Search By Ticket Number') }}"
                                                value="{{ !empty(request()->input('ticket_no')) ? request()->input('ticket_no') : '' }}">
                                        </div>
                                    </div>


                                </div>

                            </form>
                        </div>



                        <div class="col-lg-3">
                            <button class=" mt-4 btn btn-danger btn-sm d-none bulk-delete float-lg-right card-header-button"
                                data-href="{{ route('admin_user.support_tickets.bulk_delete') }}">
                                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($tickets) == 0)
                                <h3 class="text-center mt-3"> {{ __('NO TICKET FOUND') }}
                                </h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-2">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">{{ __('Ticket No') }}</th>
                                                <th scope="col">{{ __('Email') }}</th>
                                                <th scope="col">{{ __('Subject') }}</th>
                                                <th scope="col">{{ __('Ticket Status') }}
                                                </th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tickets as $ticket)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $ticket->id }}">
                                                    </td>
                                                    <td>{{ '#' . $ticket->ticket_number }}</td>

                                                    <td>{{ $ticket->email }}</td>
                                                    <td>{{ $ticket->subject }}</td>
                                                    <td>
                                                        @if ($ticket->status == 1)
                                                            <span class="badge badge-warning">{{ __('Pending') }}</span>
                                                        @elseif ($ticket->status == 2)
                                                            <span class="badge badge-success">{{ __('Open') }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ __('Closed') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                                type="button" id="dropdownMenuButton"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                {{ __('Select') }}
                                                            </button>

                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">


                                                                <a href="{{ route('admin_user.support_ticket.conversation', ['id' => $ticket->id]) }}"
                                                                    class="dropdown-item">
                                                                    {{ __('Conversation') }}
                                                                </a>

                                                                <form class="deleteform"
                                                                    action="{{ route('admin_user.support_ticket.delete', ['id' => $ticket->id]) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    <button type="submit" class="deletebtn dropdown-item">
                                                                        {{ __('Delete') }}
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="mt-3 text-center">
                        <div class="d-inline-block mx-auto">
                            {{ $tickets->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
