@extends('automatic-tests::tests._layout')

@section('content')
    <h1>Page 2</h1>
    <a href="{{ route('page-1') }}">Back to Page 1</a>
    <a href="/page-3">To Page 3, but with a relative link</a>
@endsection
