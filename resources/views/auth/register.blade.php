@extends('main.auth.auth')
@section('pageContent')
    <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10">
        <!--begin::Form-->
        <form class="form w-100" id="kt_sign_in_form"
              action="{{route('register')}}" method="post">
            @include('general-includes.questions.choose')
            <!--begin::Form-->
            <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                <!--begin::Wrapper-->
                <div class="w-lg-500px p-10">
                    @csrf
                    <!--begin::Heading-->
                    <div class="text-center mb-11">
                        <!--begin::Title-->
                        <h1 class="text-dark fw-bolder mb-3">{{__('admin.Sign up')}}</h1>
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
                            <input type="text" placeholder="{{__('admin.Name')}}" name="name" id="name"
                                   autocomplete="off"
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
                            <span class="indicator-label">{{__('admin.Sign up')}}</span>
                            <!--end::Indicator label-->
                            <!--begin::Indicator progress-->
                            <span class="indicator-progress">
                            {{__('admin.Please wait')}}...
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
                </div>
                <!--end::Wrapper-->
            </div>
        </form>
        <!--end::Form-->
        <!--end::Form-->
        <!--begin::Footer-->
        <div class="d-flex flex-center flex-wrap px-5">
            <!--begin::Links-->
            <div class="d-flex fw-semibold text-primary fs-base">
                <a href="tel:+201067554823" class="px-5" target="_blank">
                    <i class="fa-solid fa-phone" style="color: #3297FF;"></i>
                </a>
                <a href="https://keenthemes.com" class="px-5" target="_blank">
                    <i class="fa-brands fa-facebook-f" style="color: #3297FF;"></i>
                </a>
                <a href="https://mgahed.com/"
                   class="px-5" target="_blank">
                    <i class="fa-solid fa-m" style="color: #3297FF;"></i>
                </a>
            </div>
            <!--end::Links-->
        </div>
        <!--end::Footer-->
    </div>
@endsection

@section('pageScripts')
    <script>
        const selectInput = document.getElementById('qType');
        selectInput.addEventListener('invalid', function () {
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
        // global variable to store the no of questions
        let noOfQs = 0;
        function getNoOfQs(type) {
            // ajax call to get no of questions
            $.ajax({
                url: "{{route('getNoOfQs')}}",
                type: 'GET',
                data: {
                    _token: "{{csrf_token()}}",
                    category: type
                },
                success: function (data) {
                    noOfQs = data.noOfQs;
                    $('#totalQs').text(noOfQs);
                }
            });
        }

        $('#qType').change(function () {
            let type = $(this).val();

            getNoOfQs(type);

            $('#count').html(0);
            if (type == '') {
                $('#sign_up_submit').addClass('disabled pointer-event-none');
                $('#count').html(0);
                $('#randomChoose').addClass('disabled pointer-event-none');
                $('input[type="checkbox"]').prop('checked', false);
                $('#checkBoxCards').addClass('d-none');
            } else {
                $('#sign_up_submit').removeClass('disabled pointer-event-none');
                $('#randomChoose').removeClass('disabled pointer-event-none');
                $('#checkBoxCards').removeClass('d-none');
            }
            let cnt = 0;
            $('.cards').html('');
            if (type == 2) { // ربع القران
                @php
                    $countQuestions = \App\Models\Question::where('category_id',2)->count();
                @endphp
                    cnt = {{$countQuestions}};
            } else if (type == 3) { // نصف القران
                @php
                    $countQuestions = \App\Models\Question::where('category_id',3)
                ->orWhere('category_id', 2)->count();
                @endphp
                    cnt = {{$countQuestions}};
            } else if (type == 4) { // ثلاث ارباع القران
                @php
                    $countQuestions = \App\Models\Question::where('category_id',4)
                    ->orWhere('category_id', 2)
                    ->orWhere('category_id', 3)->count();
                @endphp
                    cnt = {{$countQuestions}};
            } else if (type == 5) {
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
            if (n > noOfQs) {
                toastr.error("لا يمكنك اختيار اكثر من " + noOfQs + " سؤال");
                $(this).prop('checked', false);
                return false;
            }
            $('#count').html(n);
            toastr.info("لقد اخترت " + n + " من " + noOfQs + " سؤال");
            if (n == noOfQs && $('#qType').val() != '') {
                $('#sign_up_submit').removeClass('disabled pointer-event-none');
            } else {
                $('#sign_up_submit').addClass('disabled pointer-event-none');
            }
            return true;
        }
        function checkRandom() {
            // let all checkboxes not checked
            $('input[type="checkbox"]').prop('checked', false);

            // check 15 random checkboxes
            let n = 0;
            while (n < noOfQs) {
                let random = Math.floor(Math.random() * 30);
                if (!$(`#card${random}`).prop('checked')) {
                    $(`#card${random}`).prop('checked', true);
                    n++;
                }
            }
            $('#count').html(noOfQs);
        }
    </script>
@endsection
