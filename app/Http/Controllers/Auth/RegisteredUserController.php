<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\ValidNidRule;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Laratrust\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $showName = \App\Models\Lookup::where('name', 'reg_field_name')->value('value') ?? '1';
        $showDegree = \App\Models\Lookup::where('name', 'reg_field_degree')->value('value') ?? '1';
        $showSchool = \App\Models\Lookup::where('name', 'reg_field_school')->value('value') ?? '1';
        $showNid = \App\Models\Lookup::where('name', 'reg_field_nid')->value('value') ?? '1';
        $showPhone = \App\Models\Lookup::where('name', 'reg_field_phone')->value('value') ?? '1';
        $showCountry = \App\Models\Lookup::where('name', 'reg_field_country')->value('value') ?? '1';

        $rules = [
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['nullable', Rules\Password::defaults()],
            'category_id' => 'required|exists:categories,id',
        ];

        if ($showName == '1') {
            $rules['name'] = ['required', 'string', 'max:255'];
        } else {
            $rules['name'] = ['nullable', 'string', 'max:255'];
        }

        if ($showDegree == '1') {
            $rules['degree'] = ['required', 'numeric', 'min:0', 'max:490'];
        } else {
            $rules['degree'] = ['nullable', 'numeric', 'min:0', 'max:490'];
        }

        if ($showSchool == '1') {
            $rules['school'] = ['required'];
            $rules['school_name'] = ['nullable', 'required_if:school,other', 'string', 'max:255'];
        } else {
            $rules['school'] = ['nullable'];
            $rules['school_name'] = ['nullable', 'string', 'max:255'];
        }

        if ($showNid == '1') {
            $rules['nid'] = ['required', 'unique:'.User::class, 'digits:14', 'numeric', new ValidNidRule()];
        } else {
            $rules['nid'] = ['nullable', 'unique:'.User::class, 'digits:14', 'numeric', new ValidNidRule()];
        }

        if ($showPhone == '1') {
            $rules['phone'] = ['required', 'string', 'regex:/^(011|012|010|015)[0-9]{8}$/'];
        } else {
            $rules['phone'] = ['nullable', 'string', 'regex:/^(011|012|010|015)[0-9]{8}$/'];
        }

        if ($showCountry == '1') {
            $rules['country'] = ['required', 'string', 'max:255'];
        } else {
            $rules['country'] = ['nullable', 'string', 'max:255'];
        }

        $request->validate($rules);

        // birth_date and gender based on nid if available
        $birthDate = $request->nid ? getBirthDate($request->nid) : null;
        $gender = $request->nid ? getGender($request->nid) : null;

        $school_id = null;
        if ($request->school && $request->school != 'other') {
            $school_id = $request->school;
        } elseif (isset($request->school_name) && $request->school_name) {
            $checkSchool = School::where('name', $request->school_name)->first();
            if ($checkSchool) {
                $school_id = $checkSchool->id;
            } else {
                $school = School::create([
                    'name' => $request->school_name,
                ]);
                $school_id = $school->id;
            }
        }

        $user = User::create([
            'name' => $request->name ?? 'User_' . rand(1000, 9999),
            'category_id' => $request->category_id ?? null,
            'email' => $request->email ?? ($request->nid ? $request->nid.'@'.env('APP_DOMAIN', 'mgahed.com') : uniqid().'@'.env('APP_DOMAIN', 'mgahed.com')),
            'password' => $request->password ? Hash::make($request->password) : Hash::make($request->nid ?? uniqid()),
            'nid' => $request->nid,
            'birth_date' => $birthDate,
            'gender' => $gender,
            'school_id' => $school_id,
            'degree' => $request->degree ?? null,
            'phone' => $request->phone ?? null,
            'country' => $request->country ?? null,
            'email_verified_at' => now(),
        ]);

        $studentRole = Role::where('name', 'student')->first();
        $user->addRole($studentRole);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
