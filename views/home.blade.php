@extends('layouts.app')
@section('page-title', 'Home')
@section('breadcrumb', '')

@section('content')
<style>
    * {
        margin: 0px;
        padding:0px;
    }
    header{
            background-color: rgb(16, 16, 145);
            /* background-image:url(aset/nusantara.jpg); */
            background-attachment: fixed;
            height: 200px;
            margin:0;
            padding-top:50px;
        }
    header > h1{
            margin:0;
            color:#fff;
            font-size: 5vw;
        }
    
</style>
<header>
        <h1 align="center" class="fw-bold">Welcome to <span class="text-brand"><span>My</span>Serti</span></h1>
    </header>
@endsection