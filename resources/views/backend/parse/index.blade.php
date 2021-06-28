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
                    <div class="col-md-12 select-outline d-flex">
                        <div class="form-group d-flex align-items-center mx-3">
                            <input type="text" class="form-control" placeholder="Поиск по тегам">
                        </div>
                        <div class="custom-select mx-3" style="width:200px;">
                            <select>
                              <option value="0">Select car:</option>
                              <option value="1">Audi</option>
                              <option value="2">BMW</option>
                            </select>
                        </div>
                        <div class="custom-select mx-3" style="width:200px;">
                            <select>
                              <option value="0">Select car:</option>
                              <option value="1">Audi</option>
                              <option value="2">BMW</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center mx-3">
                            С
                            <div class="block_icon">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <input type="text" id="datepicker" name="date_from"/>
                        </div>
                        <div class="d-flex align-items-center mx-1">
                            По
                            <div class="block_icon">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <input type="text" id="datepicker2" name="date_from"/>
                        </div>

                        <div class="d-flex align-items-center mx-3">
                            Искать в: 
                            <div class="form">
                                <input type="checkbox" id="check" />
                                <label for="check" style="--d: 20px">
                                    <svg viewBox="0,0,50,50">
                                        <path d="M5 30 L 20 45 L 45 5"></path>
                                    </svg>
                                </label>
                                Тексте
                            </div>
                            <div class="form">
                                <input type="checkbox" id="check" />
                                <label for="check" style="--d: 20px">
                                    <svg viewBox="0,0,50,50">
                                        <path d="M5 30 L 20 45 L 45 5"></path>
                                    </svg>
                                </label>
                                Заголовке
                            </div>
                            <div class="form">
                                <input type="checkbox" id="check" />
                                <label for="check" style="--d: 20px">
                                    <svg viewBox="0,0,50,50">
                                        <path d="M5 30 L 20 45 L 45 5"></path>
                                    </svg>
                                </label>
                                Тегах
                            </div>
                            <div class="form">
                                <input type="checkbox" id="check" />
                                <label for="check" style="--d: 20px">
                                    <svg viewBox="0,0,50,50">
                                        <path d="M5 30 L 20 45 L 45 5"></path>
                                    </svg>
                                </label>
                                КС
                            </div>
                        </div>
                  
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

@push('after-scripts')
<script type="text/javascript">

var foopicker = new FooPicker({
    id: 'datepicker'
});

var foopicker = new FooPicker({
    id: 'datepicker2'
});

</script>
@endpush