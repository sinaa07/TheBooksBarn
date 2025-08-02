@php
    $username = 'lilboo';
@endphp

<h1> Welcome to my page {{$name}} </h1>
<a href="{{ route('welcome',['name'=> $username])}}">Welcome Page</a>
