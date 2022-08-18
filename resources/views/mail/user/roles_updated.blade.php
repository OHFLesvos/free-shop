@component('mail::message')
# Your user roles have been updated

Hello {{ $name }}

Your user roles has been updated.

@if($roles->isNotEmpty())
Your new roles are:
@foreach($roles as $role)
* {{ $role }}
@endforeach
@else
_You don't have any roles at the moment._
@endif

@component('mail::button', ['url' => route('backend')])
Open backend
@endcomponent

Thanks,<br>
{{ setting()->get('brand.name', config('app.name')) }}
@endcomponent
