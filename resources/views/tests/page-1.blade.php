@extends('automatic-tests::tests._layout')

@section('content')
    <h1>Page 1</h1>
    <a href="{{ route('index') }}">Back to Index</a>
    <a href="https://google.com" target="_blank">External link to Google</a>
@endsection
