@extends('layouts.app')

@section('content')
<div style="background: red; color: white; padding: 50px; text-align: center; margin: 20px;">
    <h1>PAGE DE TEST</h1>
    <p>Si vous voyez ceci, le problème vient de home.blade.php</p>
    <p>Heure: {{ date('H:i:s') }}</p>
</div>
@endsection
