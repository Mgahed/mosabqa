@extends('main.master.master')
@section('pageContent')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            @include('main.master.includes.toolbar')
            <div class="app-container container-fluid">
                <!--begin::form-->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{__('admin.Registration Settings')}}</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{session('success')}}</div>
                        @endif
                        <form action="{{route('registration-settings.update')}}" method="post">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{__('admin.Field')}}</th>
                                            <th>{{__('admin.Visible')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['name', 'degree', 'school', 'nid', 'phone', 'country'] as $field)
                                            <tr>
                                                <td>{{__('admin.' . ucfirst($field))}}</td>
                                                <td>
                                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox" name="reg_field_{{$field}}" id="reg_field_{{$field}}" value="1" {{$settings[$field] == '1' ? 'checked' : ''}}/>
                                                        <label class="form-check-label" for="reg_field_{{$field}}">
                                                            {{__('admin.Show')}}
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">{{__('admin.Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end::form-->
            </div>
        </div>
        <!--end::Content wrapper-->
    </div>
@endsection
