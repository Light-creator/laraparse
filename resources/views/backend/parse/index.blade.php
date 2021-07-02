@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ $module_title }} @endsection

@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection

@section('content')
<div class="container d-flex col-lg-12">
    <div class="block-parse col-lg-9">
        <div class="block-parse-sort">
            <div class="section-sort">
                <div class="row">
                    <div class="col-md-12 select-outline d-flex" style="flex-wrap: wrap;">
                        <div class="form-group d-flex align-items-center mx-1">
                            <input type="text" class="form-control" placeholder="Поиск по тегам">
                        </div>
                        <div class="custom-select mx-1" style="width:200px;">
                            <select>
                              <option value="0">Select car:</option>
                              <option value="1">Audi</option>
                              <option value="2">BMW</option>
                            </select>
                        </div>
                        <div class="custom-select mx-1" style="width:200px;">
                            <select>
                              <option value="0">Select car:</option>
                              <option value="1">Audi</option>
                              <option value="2">BMW</option>
                            </select>
                        </div>
                        <div class="d-flex mx-1">
                            <p class="mt-2 mx-1">С</p>
                            <div class="block_icon">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <input type="text" class="date_from" id="datepicker" name="date_from"/>
                        </div>
                        <div class="d-flex mx-1">
                            <p class="mt-2 mx-1">По</p>
                            <div class="block_icon">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <input type="text" id="datepicker2" name="date_from"/>
                        </div>

                        <div class="d-flex align-items-center mx-1">
                            <p class="">Искать в:</p>
                            <label class="__container mx-1">Загаловке
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="__container mx-1">Тегах
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="__container mx-1">КС
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="__container mx-1">Тексте
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                  
                    </div>
                  </div>
            </div>
            <div class="section-tags">
                <div class="container">
                    <ul class="ks-cboxtags">
                      <li><input type="checkbox" id="checkboxOne" value="Rainbow Dash"><label for="checkboxOne">Rainbow Dash</label></li>
                      <li><input type="checkbox" id="checkboxTwo" value="Cotton Candy"><label for="checkboxTwo">Cotton Candy</label></li>
                      <li><input type="checkbox" id="checkboxThree" value="Rarity"><label for="checkboxThree">Rarity</label></li>
                      <li><input type="checkbox" id="checkboxFour" value="Moondancer"><label for="checkboxFour">Moondancer</label></li>
                      <li><input type="checkbox" id="checkboxFive" value="Surprise"><label for="checkboxFive">Surprise</label></li>
                    </ul>    
                  </div>
            </div>
        </div>
        <form action="" method="POST">
        @csrf
        <div class="block-parse d-flex col-md-12">
            <div class="scrollbar col-md-3 style-3" >
                <div class="force-overflow">
                    @foreach(Session::get('source_info') as $title => $sections)
                    <a class="btn btn-light col-md-12 link-choose main-choose" data-param="{{ $title }}">{{ $title }}</a>
                    <div class="block_dropdown_choose_link" id="dropdown-{{ $title }}" style="display: none;">
                        @foreach ($sections as $section => $val)
                        @if($val)
                         <a class="btn btn-light col-md-12 link-choose secondary-choose" data-param="{{ current($val) }}">{{ key($val) }}</a>
                        @endif
                         @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="block-parse-article col-md-9">
                <div class="scrollbar scroll col-md-12 style-3">
                    <div class="force-overflow">
                        <div class="page">
                            <div class="page__demo">
                              <div class="main-container page__container">
                                
                              </div>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="submit-parse-block col-md-12">
            <div class="block-search">
                <span><i class="fas fa-search"></i></span>
                <input type="text">
            </div>
            <button type="submit" class="parse-submit-button">Запустить парсинг выбранных статей</button>
        </div>
        </form>
    </div>
    <div class="block-lang col-lg-3">

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

$('.checkbox_article').click(function() {
    alert(1);
}); 

$('.main-choose').click(function() {
    if($(this).hasClass('choose-active')) {
        $('#dropdown-'+$(this).data('param')).css('display', 'none');
        $(this).removeClass('choose-active');
    } else {
        $('#dropdown-'+$('.main-choose.choose-active').data('param')).css('display', 'none');
        $('.main-choose.choose-active').removeClass('choose-active')
        $('#dropdown-'+$(this).data('param')).css('display', 'block');
        $(this).addClass('choose-active');
    }
});

$('.secondary-choose').click(function() {
    $('.secondary-choose').removeClass('choose-active');
    $(this).addClass('choose-active');
    if($('#datepicker').val() != '' && $('#datepicker2').val() != '') {
        $.ajax('{{ route('backend.parse.section_parse') }}', {
            type: 'POST', 
            data: { 
                url_section: $('.secondary-choose.choose-active').data('param'),
                date_from: $('#datepicker').val(),
                date_to: $('#datepicker2').val(),
                source_name: $('.main-choose.choose-active').text(),
            },  
            progress: function(data) {
                console.log(data);
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                console.log(data);
                $('.page__container').html(data);
            }
        });
    }
});

$('#foopicker-datepicker').click(function() {
    if($('.secondary-choose').hasClass('choose-active') && $('#datepicker2').val() != '') {
        $.ajax('{{ route('backend.parse.section_parse') }}', {
            type: 'POST', 
            data: { 
                url_section: $('.secondary-choose.choose-active').data('param'),
                date_from: $('#datepicker').val(),
                date_to: $('#datepicker2').val(),
                source_name: $('.main-choose.choose-active').text(),
            },  
            progress: function(data) {
                console.log(data);
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                console.log(data);
                $('.page__container').html(data);
            }
        });
    }
});

$('#foopicker-datepicker2').click(function() {
    if($('.secondary-choose').hasClass('choose-active') && $('#datepicker').val() != '') {
        $.ajax('{{ route('backend.parse.section_parse') }}', {
            type: 'POST', 
            data: { 
                url_section: $('.secondary-choose.choose-active').data('param'),
                date_from: $('#datepicker').val(),
                date_to: $('#datepicker2').val(),
                source_name: $('.main-choose.choose-active').text(),
            },  
            progress: function(data) {
                console.log(data);
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                console.log(data);
                $('.page__container').html(data);
            }
        });
    }
});


</script>
@endpush