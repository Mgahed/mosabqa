<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lookup;

class RegistrationSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lookup-edit')->only('index', 'update');
    }

    public function index()
    {
        $fields = ['name', 'degree', 'school', 'nid', 'phone', 'country'];
        $settings = [];
        
        foreach ($fields as $field) {
            $lookup = Lookup::firstOrCreate(
                ['name' => 'reg_field_' . $field],
                ['value' => '1', 'record_state' => 1]
            );
            $settings[$field] = $lookup->value;
        }

        $result = [
            'settings' => $settings,
            'title' => __('admin.Registration Settings'),
        ];
        return view('pages.registration_settings.index', $result);
    }

    public function update(Request $request)
    {
        $fields = ['name', 'degree', 'school', 'nid', 'phone', 'country'];
        
        foreach ($fields as $field) {
            $val = $request->has('reg_field_' . $field) ? '1' : '0';
            Lookup::where('name', 'reg_field_' . $field)->update(['value' => $val]);
        }
        
        return redirect()->back()->with('success', __('admin.Settings updated successfully.'));
    }
}
