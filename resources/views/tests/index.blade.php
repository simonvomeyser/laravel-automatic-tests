@extends('automatic-tests::tests._layout')

@section('content')
    <h1>Test</h1>
    <p>Test</p>
    <a href="{{ route('page-1') }}">Page 1</a>
    <a href="{{ route('page-2') }}">Page 2</a>
    <a href="{{ route('page-2') . '?search=lorem' }}">Page 2 with query param</a>
    <a href="{{ route('page-2') . '#section-link' }}">Page 2 with anchor link</a>
    <a href="{{ route('page-2') . '?search=lorem#section-link' }}">Page 2 with query and anchor link</a>
    <a href="https://google.com" target="_blank">External link to Google</a>
@endsection
