<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5"
         data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto"
         data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
         data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px">
        @php
            $currentRouteName = request()->route()->getName();
        @endphp
            <!--begin::Menu-->
        <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold px-3"
             id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
            @role('super-admin|admin')
            <!--begin:Menu item-->
            <div class="menu-item pt-5">
                <!--begin:Menu content-->
                <div class="menu-content">
                    <span class="menu-heading fw-bold text-uppercase fs-7">{{__('admin.Admin')}}</span>
                </div>
                <!--end:Menu content-->
            </div>
            <!--end:Menu item-->
            <!--begin:Menu item-->
            {{--<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                <!--begin:Menu link-->
                <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/layouts/lay008.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
														<path
                                                            d="M20 7H3C2.4 7 2 6.6 2 6V3C2 2.4 2.4 2 3 2H20C20.6 2 21 2.4 21 3V6C21 6.6 20.6 7 20 7ZM7 9H3C2.4 9 2 9.4 2 10V20C2 20.6 2.4 21 3 21H7C7.6 21 8 20.6 8 20V10C8 9.4 7.6 9 7 9Z"
                                                            fill="currentColor"/>
														<path opacity="0.3"
                                                              d="M20 21H11C10.4 21 10 20.6 10 20V10C10 9.4 10.4 9 11 9H20C20.6 9 21 9.4 21 10V20C21 20.6 20.6 21 20 21Z"
                                                              fill="currentColor"/>
													</svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Layout Options</span>
											<span class="menu-arrow"></span>
										</span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div class="menu-sub menu-sub-accordion">
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="../../demo1/dist/layouts/light-sidebar.html">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                            <span class="menu-title">Light Sidebar</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="../../demo1/dist/layouts/dark-sidebar.html">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                            <span class="menu-title">Dark Sidebar</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="../../demo1/dist/layouts/light-header.html">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                            <span class="menu-title">Light Header</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="../../demo1/dist/layouts/dark-header.html">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                            <span class="menu-title">Dark Header</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                </div>
                <!--end:Menu sub-->
            </div>--}}
            <!--end:Menu item-->
            <!--Category-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link @if(str_contains($currentRouteName, 'categories')){{'active'}}@endif"
                   href="{{route('categories.index')}}">
                    <span class="menu-icon">
                        <i class="fa fa-list"></i>
                    </span>
                    <span class="menu-title">{{__('admin.Categories')}}</span>
                </a>
                <!--end:Menu link-->
            </div>

            <!--Questions-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link @if(str_contains($currentRouteName, 'questions')){{'active'}}@endif"
                   href="{{route('questions.index')}}">
                    <span class="menu-icon">
                        <i class="fa fa-list"></i>
                    </span>
                    <span class="menu-title">{{__('admin.Questions')}}</span>
                </a>
                <!--end:Menu link-->
            </div>

            <!--Schools-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link @if(str_contains($currentRouteName, 'schools')){{'active'}}@endif"
                   href="{{route('schools.index')}}">
                    <span class="menu-icon">
                        <i class="fa fa-list"></i>
                    </span>
                    <span class="menu-title">{{__('admin.Schools')}}</span>
                </a>
                <!--end:Menu link-->
            </div>

            <!--Users-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link @if(str_contains($currentRouteName, 'admin.users')){{'active'}}@endif"
                   href="{{route('admin.users.index')}}">
                    <span class="menu-icon">
                        <i class="fa fa-list"></i>
                    </span>
                    <span class="menu-title">{{__('admin.Users')}}</span>
                </a>
                <!--end:Menu link-->
            </div>

            <!--Lookups-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link @if(str_contains($currentRouteName, 'lookups')){{'active'}}@endif"
                   href="{{route('lookups.index')}}">
                    <span class="menu-icon">
                        <i class="fa fa-list"></i>
                    </span>
                    <span class="menu-title">{{__('admin.Lookups')}}</span>
                </a>
                <!--end:Menu link-->
            </div>
            <!--end:Menu item-->

            <!--Registration Settings-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link @if(str_contains($currentRouteName, 'registration-settings')){{'active'}}@endif"
                   href="{{route('registration-settings.index')}}">
                    <span class="menu-icon">
                        <i class="fa fa-cogs"></i>
                    </span>
                    <span class="menu-title">{{__('admin.Registration Settings')}}</span>
                </a>
                <!--end:Menu link-->
            </div>
            <!--end:Menu item-->

            <!--Translations-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link @if(str_contains($currentRouteName, 'translation')){{'active'}}@endif"
                   href="{{route('translations.index')}}">
                    <span class="menu-icon">
                        <i class="fa fa-list"></i>
                    </span>
                    <span class="menu-title">{{__('admin.Translations')}}</span>
                </a>
                <!--end:Menu link-->
            </div>
            <!--end:Menu item-->

            @role('super-admin')
            <!--Laratrust-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link" target="_blank"
                   href="{{url('/laratrust')}}">
                    <span class="menu-icon">
                        <i class="fa fa-list"></i>
                    </span>
                    <span class="menu-title">{{__('admin.Roles Permissions')}}</span>
                </a>
                <!--end:Menu link-->
            </div>
            <!--end:Menu item-->
            @endrole
            @endrole
            <!--begin:Menu item-->
            <div class="menu-item pt-5">
                <!--begin:Menu content-->
                <div class="menu-content">
                    <span class="menu-heading fw-bold text-uppercase fs-7">{{__('admin.Student')}}</span>
                </div>
                <!--end:Menu content-->
            </div>
            <!--end:Menu item-->

            <!--Questions-->
            <div class="menu-item">
                @if(now() >= \App\Models\Lookup::where('name', 'exam_start_date')->first()->value)
                    <!--begin:Menu link-->
                    <a class="menu-link @if(request()->routeIs('student.questions')){{'active'}}@endif"
                       href="{{route('student.questions')}}">
                    <span class="menu-icon">
                        <i class="fa fa-list"></i>
                    </span>
                        <span class="menu-title">{{__('admin.Exam')}}</span>
                    </a>
                    <!--end:Menu link-->
                @endif

                @if(now() >= \App\Models\Lookup::where('name', 'exam_end_date')->first()->value)
                    <!--begin:Menu link-->
                    <a class="menu-link @if(request()->routeIs('student.answers')){{'active'}}@endif"
                       href="{{route('student.answers')}}">
                        <span class="menu-icon">
                            <i class="fa fa-list"></i>
                        </span>
                        <span class="menu-title">{{__('admin.Answers')}}</span>
                    </a>
                    <!--end:Menu link-->
                @endif
            </div>
            <!--end:Menu item-->
        </div>
        <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>
