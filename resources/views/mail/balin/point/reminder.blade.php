@extends('mail.balin.layout')

@section('content')
	<table style="width:100%;">
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<img src="{{ $message->embed($data['balin']['logo']) }}" style="max-width:200px; text-align:left;">
			</td>
			<td width="10%"></td>
		</tr>
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>Dear Bpk/Ibu <strong>{{$data['point']['user']['name']}},</strong></p>

				<p>
					Anda Memiliki BALIN Point sebesar @thunder_mail_money_indo($data['point']['amount']) dari total point Anda yang akan expire tanggal {{date('d-m-Y H:i', strtotime($data['point']['expired_at']))}}.
				</p>

				<p>
					Silahkan gunakan point Anda.
				</p>
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td></br></td>
		</tr>

		<tr>
			<td width="10%"></td>
			<td style="width:90%; text-align:center;">
				<a href="{{$data['balin']['url']}}" class='btn'>LIHAT PENAWARAN KAMI</a>
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td><br></td>
		</tr>	

		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>
					Kind Regards, </br>
					Balin.id
				</p>
			</td>
			<td width="10%"></td>
		</tr>

	</table>
	</br>
	</br>
	</br>
@stop