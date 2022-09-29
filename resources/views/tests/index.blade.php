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
    <a href="/img/test.png" target="_blank">Link to file</a>
    <a href="/img/test.jpg" target="_blank">Link to file</a>
    <a href="/img/test.pdf" target="_blank">Link to file</a>
    <a href="/img/test.php" target="_blank">Link to file</a>
    <a href="/img/.config" target="_blank">Link to file</a>
@endsection
