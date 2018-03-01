@extends('main.layouts.main')

@section('style')
@endsection
@section('content-wrapper')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Errors
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ auth()->user()->homeUrl() }}"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Errors</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
               @include('layouts.error')
            </div>
        </div>
    </section>

    



@endsection

@section('javascript')

@endsection







