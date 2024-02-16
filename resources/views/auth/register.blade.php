@extends('main.auth.auth')
@section('pageContent')
    <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10">
    @include('general-includes.questions.choose')
        <!--begin::Form-->
        <div class="d-flex flex-center flex-column flex-lg-row-fluid">
            <!--begin::Wrapper-->
            <div class="w-lg-500px p-10">
                <!--begin::Form-->
                <form class="form w-100" id="kt_sign_in_form"
                      action="{{route('register')}}" method="post">
                    @csrf
                    <!--begin::Heading-->
                    <div class="text-center mb-11">
                        <!--begin::Title-->
                        <h1 class="text-dark fw-bolder mb-3">{{__('admin.Sign Up')}}</h1>
                        <!--end::Title-->
                    </div>
                    <!--begin::Heading-->
                    <!--begin::Input group=-->
                    <div class="fv-row mb-8">
                        <!--begin::Nid-->
                        <div id="nid-group">
                            <label for="name">
                                {{__('admin.Name')}}
                            </label>
                            <input type="text" placeholder="{{__('admin.Name')}}" name="name" id="name" autocomplete="off"
                                   class="form-control bg-transparent @error('name') is-invalid @enderror" required
                                   value="{{old('name')}}"
                            />
                            @error('name')
                            <div class="fv-plugins message-container invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <!--end::Nid-->
                        <!--begin::Nid-->
                        <div id="nid-group" class="mt-5">
                            <label for="nid">
                                {{__('admin.Nid')}}
                            </label>
                            <input type="text" placeholder="{{__('admin.Nid')}}" name="nid" id="nid" autocomplete="off"
                                   class="form-control bg-transparent @error('nid') is-invalid @enderror" required
                                   value="{{old('nid')}}"
                            />
                            @error('nid')
                            <div class="fv-plugins message-container invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <!--begin::Submit button-->
                    <div class="d-grid mb-10">
                        <button type="submit" id="sign_up_submit" class="btn btn-primary disabled">
                            <!--begin::Indicator label-->
                            <span class="indicator-label">{{__('admin.Sign Up')}}</span>
                            <!--end::Indicator label-->
                            <!--begin::Indicator progress-->
                            <span class="indicator-progress">
                            {{__('admin.Please wait...')}}
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            <!--end::Indicator progress-->
                        </button>
                    </div>
                    <!--end::Submit button-->
                    <!--begin::Sign up-->
                    <div class="text-gray-500 text-center fw-semibold fs-6">
                        {{__('admin.Do you have an account')}}
                        <a href="{{route('login')}}"
                           class="link-primary">{{__('admin.Sign In')}}</a></div>
                    <!--end::Sign up-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Form-->
        <!--begin::Footer-->
        <div class="d-flex flex-center flex-wrap px-5">
            <!--begin::Links-->
            <div class="d-flex fw-semibold text-primary fs-base">
                <a href="https://keenthemes.com" class="px-5" target="_blank">Terms</a>
                <a href="https://devs.keenthemes.com" class="px-5" target="_blank">Plans</a>
                <a href="https://themes.getbootstrap.com/product/keen-the-ultimate-bootstrap-admin-theme/"
                   class="px-5" target="_blank">Contact Us</a>
            </div>
            <!--end::Links-->
        </div>
        <!--end::Footer-->
    </div>
@endsection

@section('pageScripts')
    <script>
        const selectInput = document.getElementById('qType');
        selectInput.addEventListener('invalid', function() {
            if (selectInput.validity.valueMissing) {
                selectInput.setCustomValidity('من فضلك اختر المستوى');
            } else {
                selectInput.setCustomValidity('');
            }
        });
        toastr.options = {
            "closeButton": true,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toastr-top-center",
            "preventDuplicates": false,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        let verifyOTPCode = true;
    </script>

    <script>
        $('#qType').change(function () {
            $('#count').html(0);
            let type = $(this).val();
            let cnt = 0;
            $('.cards').html('');
            if (type == 'ربع القران') {
                @php
                    $countQuestions = \App\Models\Question::where('type','ربع القران')->count();
                @endphp
                    cnt = {{$countQuestions}};
            } else if (type == 'نصف القران') {
                @php
                    $countQuestions = \App\Models\Question::where('type','نصف القران')
                ->orWhere('type','ربع القران')->count();
                @endphp
                    cnt = {{$countQuestions}};
            } else if (type == 'ثلاث ارباع القران') {
                @php
                    $countQuestions = \App\Models\Question::where('type','ثلاث ارباع القران')
                    ->orWhere('type','ربع القران')
                    ->orWhere('type','نصف القران')->count();
                @endphp
                    cnt = {{$countQuestions}};
            } else if (type == 'القران كاملا') {
                @php
                    $countQuestions = \App\Models\Question::get()->count();
                @endphp
                    cnt = {{$countQuestions}};
            } else {
                cnt = 30;
            }
            for (let i = 0; i < cnt; i++) {
                $('.cards').append(`
                        <div class="card col-md-4 col-6">
                        <div class="form-check form-check-custom form-check-solid form-check-lg">
                            <input onchange="check(this)" class="form-check-input" type="checkbox" value="" id="card${i}" name="cards[]"/>
                            <label class="form-check-label" for="card${i}">
                                {{__('admin.Choose')}}
                            </label>
                        </div>
                        <label for="card{i}" class="mt-3">
                            <div class="card-body">
                            </div>
                        </label>
                    </div>
                    `);
            }
        });

        function check(elem) {
            let data = changeCheckbox();
            if (!data) {
                $(elem).prop('checked', false);
            }
        }

        $('input[type="checkbox"]').change(function () {
            let data = changeCheckbox();
            if (!data) {
                $(this).prop('checked', false);
            }
        });

        function changeCheckbox() {
            let n = $("input:checked").length;
            if (n > 15) {
                toastr.error("لا يمكنك اختيار اكثر من 15");
                $(this).prop('checked', false);
                return false;
            }
            $('#count').html(n);
            toastr.info("لقد اخترت " + n + " من 15");
            if (n == 15 && verifyOTPCode) {
                $('#sign_up_submit').removeClass('disabled pointer-event-none');
            } else {
                $('#sign_up_submit').addClass('disabled pointer-event-none');
            }
            return true;
        }
    </script>
@endsection
