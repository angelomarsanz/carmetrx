@extends('user.layout')

@includeIf('user.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{   __('Edit Keyword') }}</h4>
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
                <a href="#">{{ __('Settings') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Language') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{  __('Edit Keyword') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-title d-inline-block">
                                {{   __('Edit Language Keyword') }}</div>
                        </div>
                        <div class="col-lg-6">
                          
                            <a class="btn btn-info btn-sm float-right d-inline-block "
                                href="{{ route('user.language.index') }}">
                                <span class="btn-label">
                                    <i class="fas fa-backward"></i>
                                </span>
                                {{   __('Back') }}
                            </a>
                        </div>
                    </div>

                </div>
                <div class="card-body   pb-5" id="app">
                    <div class="row">
                        <div class="col-lg-12">
                            <form method="post" action="{{ route('user.language.updateKeyword', $la->id) }}"
                                id="langForm">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-md-4 mt-2" v-for="(value, key) in datas" :key="key">
                                        <div class="form-group">
                                            <label class="control-label">@{{ key }}</label>
                                            <div class="input-group">
                                                <input type="text" :value="value" :name="'keys[' + key + ']'"
                                                    class="form-control form-control-lg">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form">
                        <div class="form-group from-show-notify row">
                            <div class="col-12 text-center">
                                <button id="langBtn" type="button" class="btn btn-success">{{ __('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    @includeIf('user.language.addKeywords')
@endsection


@section('vuescripts')
    <script src="{{ asset('assets/admin/js/plugin/vue/vue.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugin/vue/axios.js') }}"></script>
    <script>
        "use strict";
        window.app = new Vue({
            el: '#app',
            data: {
                datas: @json($keywords) ,
            }
        })
    </script>
@endsection
