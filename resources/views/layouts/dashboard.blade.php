@extends('layouts.layout')

@section('main-content')


    <div class="main-panel">
        <div class="content-wrapper">

            <div class="row">
                <div class="col-md-4 stretch-card grid-margin">
                    <div class="card bg-gradient-danger card-img-holder text-white">
                        <div class="card-body">
                            <img src="{{ asset('dist/assets/images/dashboard/circle.svg') }}"
                                 class="card-img-absolute" alt="circle-image"/>
                            <h2 class="font-weight-normal mb-3">Dashboard</h2>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
