@extends('agent.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Messages') }} </h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('agent.dashboard', getParam()) }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Project Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Messages') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-title d-inline-block">{{ __('All Message') }}</div>
                        </div>
                        <div class="col-lg-3">
                            <form action="{{ route('agent.project_management.messages', getParam()) }}" method="get"
                                id="carSearchForm">
                                <div class="row">

                                    <input type="text" name="title" value="{{ request()->input('title') }}"
                                        class="form-control" placeholder="{{ __('search by property title') }}">
                                 
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($messages) == 0)
                                <h3 class="text-center mt-2">{{ __('NO MESSAGE FOUND') }}
                                </h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3" id="basic-datatables">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{ __('Project') }}</th>
                                                <th scope="col">{{ __('Name') }}</th>
                                                <th scope="col">{{ __('Email ID') }}</th>
                                                <th scope="col">{{ __('Phone') }}</th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($messages as $message)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="table-title">


                                                        <a href="{{ route('frontend.project.details', [getParam(), 'slug' => $message->slug]) }}"
                                                            target="_blank">
                                                            {{ strlen(@$message->title) > 100 ? mb_substr(@$message->title, 0, 100, 'utf-8') . '...' : @$message->title }}
                                                        </a>

                                                    </td>

                                                    <td>{{ $message->name }}</td>
                                                    <td><a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                                    </td>
                                                    <td> <a href="tel:{{ $message->phone }}">{{ $message->phone }}</a>
                                                    </td>

                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle btn-sm"
                                                                type="button" id="dropdownMenuButton"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                {{ __('Select') }}
                                                            </button>

                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                                <a class="dropdown-item editBtn" href="#"
                                                                    data-toggle="modal" data-target="#editModal"
                                                                    data-id="{{ $message->id }}"
                                                                    data-name="{{ $message->name }}"
                                                                    data-phone="{{ $message->phone }}"
                                                                    data-message="{{ $message->message }}"
                                                                    data-email="{{ $message->email }}">
                                                                    <span class="btn-label">
                                                                        <i class="fas fa-eye"></i> {{ __('View') }}
                                                                    </span>
                                                                </a>

                                                                <form class="deleteform d-inline-block dropdown-item"
                                                                    action="{{ route('agent.property_message.delete', getParam()) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    <input type="hidden" name="message_id"
                                                                        value="{{ $message->id }}">

                                                                    <button type="submit"
                                                                        class="p-0 deletebtn dropdown-item">
                                                                        <span class="btn-label">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                            {{ __('Delete') }}
                                                                        </span>
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

                <div class="card-footer"></div>
            </div>
        </div>
    </div>

    @include('agent.project.message-view')
@endsection
