@extends('layouts.app')

@section('content')


<div class="panel panel-default panel-shadow">
	<div class="panel panel-heading main-body">
				Users
			</div>
	<div class="panel-body">
		<table class="table table-hover">
	
	<thead>
		<th class="center-heading">Image</th>
		<th class="center-heading">User Name</th>
	
		<th class="center-heading">Permissions</th>
		<th class="center-heading">Delete</th>
	</thead>
	<tbody>
		@if($users->count()>0)
			
		@foreach($users as $user)
	
		<tr>
			<td class="center-body"><img src="{{asset($user->profile->avatar)}}" alt="" width="60px" height="60px" style="border-radius: 50%;"></td>
			<td class="center-body">{{$user->name}}</td>

			<td class="center-body">
				@if($user->admin)
				
					<a href="{{route('user.notadmin',['id'=>$user->id])}}" class="btn btn-xs btn-danger">Remove Permission</a>

				@else

					<a href="{{route('user.admin',['id'=>$user->id])}}" class="btn btn-xs btn-success">Make Admin</a>

				@endif
			</td>

			<td class="center-body">
				
			</td>
		</tr>


		@endforeach	

		@else
			<tr>
				<th colspan="50" class="text-center">No Users</th>
			</tr>
		@endif
	</tbody>

</table>
	</div>
</div>

@stop