@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ $module_title }} @endsection

@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection

@section('content')
<div class="container">
    <div class="block-parse">
        <div class="block-parse-sort">
            <div class="section-sort">
                <div class="row">
                    <div class="col-md-6 select-outline">
                  
                        <select class="selectpicker">
                            <optgroup label="Picnic">
                              <option>Mustard</option>
                              <option>Ketchup</option>
                              <option>Relish</option>
                            </optgroup>
                            <optgroup label="Camping">
                              <option>Tent</option>
                              <option>Flashlight</option>
                              <option>Toilet Paper</option>
                            </optgroup>
                          </select>
                          
                  
                    </div>
                  </div>
            </div>
            <div class="section-tags">

            </div>
        </div>
        <div class="block-parse-choose">

        </div>
        <div class="block-parse-article">

        </div>
    </div>
    <div class="block-lang">

    </div>
</div>

@endsection