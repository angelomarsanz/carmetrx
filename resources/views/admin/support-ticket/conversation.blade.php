@extends('admin.layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets\admin\css\support.css') }}">
@endsection
@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Conversation') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Support Tickets') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ $ticket->subject }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Conversation') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">
                        {{ __('Ticket') . ': #' . $ticket->ticket_number }}
                    </div>

                    <a class="btn btn-info btn-sm float-right d-inline-block" href="{{ route('admin.support_tickets') }}">
                        <span class="btn-label">
                            <i class="fas fa-backward" style="font-size: 12px;"></i>
                        </span>
                        {{ __('Back') }}
                    </a>
                </div>

                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12">
                            <h3 class="ticket-subject">{{ $ticket->subject }}</h3>

                            @if ($ticket->status != 3)
                                <form class="ticketForm d-inline-block"
                                    action="{{ route('admin.support_ticket.close', ['id' => $ticket->id]) }}"
                                    method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm mt-2 closeBtn">
                                        <span class="btn-label">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        {{ __('Close Ticket') }}
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>

                    <div class="row text-center mt-4">
                        <div class="col-12">
                            @if ($ticket->status == 1)
                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                            @elseif ($ticket->status == 2)
                                <span class="badge badge-success">{{ __('Open') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('Closed') }}</span>
                            @endif

                            <span
                                class="badge badge-secondary ml-2">{{ $ticket->created_at->format('M d, Y - h:i A') }}</span>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-4 msg">
                        <div class="col-8">
                            {!! replaceBaseUrl($ticket->description, 'summernote') !!}

                            @if (!is_null($ticket->attachment))
                                <div class="text-center mt-4">
                                    <a href="{{ asset('assets/file/ticket-files/' . $ticket->attachment) }}"
                                        class="btn btn-info btn-sm" download="file.zip">
                                        <span class="btn-label">
                                            <i class="fas fa-download" style="font-size: 12px;"></i>
                                        </span>
                                        {{ __('Attachment') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="{{ $ticket->status != 3 ? 'col-lg-6' : 'col-12' }}">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Conversations') }}</div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @if (count($conversations) == 0)
                                <h5>{{ __('No Conversation Found') }}</h5>
                            @else
                                <div class="messages-container">
                                    @foreach ($conversations as $conversation)
                                        @if ($conversation->person_type == 'admin')
                                            @php $admin = $conversation->admin()->first(); @endphp

                                            <div class="single-message ">
                                                <div class="user-details d-flex flex-row-reverse">
                                                    <div class="user-img">
                                                        <img src="{{ !is_null($admin->image) ? asset('assets/admin/img/propics/' . $admin->image) : asset('assets/img/blank-user.jpg') }}"
                                                            alt="{{ $admin->first_name . ' ' . $admin->last_name }}">
                                                    </div>

                                                    <div class="user-infos">
                                                        {{-- <h6 class="name text-white">
                                                            {{ $admin->first_name . ' ' . $admin->last_name }}</h6> --}}
                                                        <span
                                                            class="  text-small text-right mr-2">{{ is_null($admin->role_id) ? 'Super Admin' : $admin->role->name }}
                                                        </span>
                                                        <small
                                                            class=" text-small  mr-2 ">{{ $conversation->created_at->format('M d, Y - h:i A') }}</small>
                                                    </div>
                                                </div>

                                                <div class="message text-right mr-3">
                                                    {!! replaceBaseUrl($conversation->replay, 'summernote') !!}
                                                </div>

                                                @if (!is_null($conversation->attachment))
                                                    <a href="{{ asset('assets/file/ticket-files/' . $conversation->attachment) }}"
                                                        download="support.zip" class="btn btn-sm btn-info mt-3">
                                                        <span class="btn-label">
                                                            <i class="fas fa-download" style="font-size: 12px;"></i>
                                                        </span>
                                                        {{ __('Attachment') }}
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            @php $user = $conversation->user()->first(); @endphp

                                            <div class="single-message">
                                                <div class="user-details">
                                                    <div class="user-img">
                                                        <img class="  avatar-img rounde"
                                                            src="{{ !is_null($user->image) ? asset('assets/front/img/user/' . $user->image) : asset('assets/img/blank-user.jpg') }}"
                                                            alt="{{ $user->first_name . ' ' . $user->last_name }}">
                                                    </div>

                                                    <div class="user-infos">
                                                        <span class="text-capitalize name text-small ml-2">
                                                            {{ $user->first_name . ' ' . $user->last_name }}</span>

                                                        <span class="text-small ml-2">
                                                            {{ $conversation->created_at->format('M d, Y - h:i A') }}</span>
                                                    </div>
                                                </div>

                                                <div class="message ">
                                                    {!! replaceBaseUrl($conversation->replay, 'summernote') !!}
                                                </div>

                                                @if (!is_null($conversation->attachment))
                                                    <a href="{{ asset('assets/file/ticket-files/' . $conversation->attachment) }}"
                                                        download="support.zip" class="btn btn-sm btn-info mt-3">
                                                        <span class="btn-label">
                                                            <i class="fas fa-download" style="font-size: 12px;"></i>
                                                        </span>
                                                        {{ __('Attachment') }}
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($ticket->status != 3)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title d-inline-block">{{ __('Reply To Ticket') }}</div>
                    </div>

                    <div class="card-body">
                        <form id="replyForm" action="{{ route('admin.support_ticket.reply', ['id' => $ticket->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea class="form-control summernote" name="reply" placeholder="{{ __('Write your reply here') }}"
                                            data-height="200"></textarea>
                                        @error('reply')
                                            <p class="mt-1 mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="attachment"
                                                    data-url="{{ route('admin.support_tickets.store_temp_file') }}">
                                                <label class="custom-file-label">{{ __('Choose File') }}</label>
                                            </div>
                                        </div>

                                        <div class="progress mt-3 d-none">
                                            <div class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                        </div>

                                        <p id="attachment-info" class="mt-2 mb-0 text-warning">
                                            {{ '*' . __('Upload only .zip file') . ', ' . __('Max file size is 5mb') }}
                                        </p>

                                        @error('attachment')
                                            <p class="mt-1 mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success" form="replyForm">
                                    {{ __('Submit') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('assets/js/admin-partial.js') }}"></script>
@endsection
