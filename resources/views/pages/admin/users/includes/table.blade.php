@php
    $successPercentage = \App\Models\Lookup::where('name', 'success_percentage')->first()->value;
@endphp
<div class="table-responsive">
    <table id="usersTable"
           class="table table-striped table-row-bordered gy-5 gs-7 border rounded">
        <thead>
        <tr class="fw-semibold fs-6 text-gray-800">
            <th>{{__('#')}}</th>
            <th>{{__('admin.Role')}}</th>
            <th>{{__('admin.Name')}}</th>
            <th>{{__('admin.Nid')}}</th>
            <th>{{__('admin.Phone')}}</th>
            <th>{{__('admin.Country')}}</th>
            <th>{{__('admin.Serial')}}</th>
            <th>{{__('admin.Category')}}</th>
            <th>{{__('admin.Gender')}}</th>
            <th>{{__('admin.Birth date')}}</th>
            <th>{{__('admin.Preparatory school degree')}}</th>
            <th>{{__('admin.School')}}</th>
            <th>{{__('admin.Degree')}}</th>
            <th>{{__('admin.Actions')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->role}}</td>
                <td>{{@$item->name}}</td>
                <td>{{@$item->nid}}</td>
                <td>{{@$item->phone}}</td>
                <td>{{@$item->country}}</td>
                <td>{{@$item->serial}}</td>
                <td>{{@$item->category->name}}</td>
                <td>{{@$item->gender}}</td>
                <td>{{@$item->birth_date}}</td>
                <td>{{@$item->degree}}</td>
                <td>{{@$item->school->name}}</td>
                <td>
                    <span class="badge badge-light-{{is_numeric($item->gradeDegree) && $item->gradeDegree >= $successPercentage ? 'success' : 'danger'}}">
                    {{$item->gradeDegree}} %
                    </span>
                </td>
                <td>
                    <a href="{{route('admin.users.show', $item->id)}}"
                       class="btn btn-icon btn-primary btn-sm me-1">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
