@extends('mail.layout')

@section('content')
	<table style="width:100%">
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<h2>HRIS</h2>
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td></br></br></td>
		</tr>

		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>@if($data['employee']['gender']=='male') Bpk @else Ibu @endif <strong>{{$data['employee']['name']}},</strong></p>

				<p>
					Anda telah terdaftar dalam sistem Human Resource. Silahkan Klik link <a href="{{route('employee.activate.link', ['activation_link' => $data['employee']['activation_link']])}}"> <strong>berikut</strong></a> untuk aktivasi password anda.
				</p>
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td></br></td>
		</tr>

		<tr>
			<td></br></br></td>
		</tr>
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>
					Regards, </br>
					HRD
				</p>
			</td>
			<td width="10%"></td>
		</tr>

	</table>
	</br>
	</br>
	</br>
@stop