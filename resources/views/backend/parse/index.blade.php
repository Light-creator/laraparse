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
            <div class="toast" role="alert" aria-live="polite" aria-atomic="true" data-delay="2500" style="z-index: 3000;">
                <div role="alert" aria-live="assertive" aria-atomic="true" class="toast" data-autohide="false">
                    <div class="toast-header">
                    <img src="..." class="rounded mr-2" alt="...">
                    <strong class="mr-auto">Оповещение</strong>
                    <small>{{ date('H:i') }}</small>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="toast-body">
                        Статья удалена из списка для парсинга.
                    </div>
                </div>
              </div>
            <div class="section-sort">
                <div class="row">
                    <div class="col-md-12 select-outline d-flex" style="flex-wrap: wrap;">
                        <div class="form-group d-flex align-items-center mx-1">
                            <input type="text" class="form-control search_tags" placeholder="Поиск по тегам">
                        </div>
                        {{-- <div class="custom-select mx-1" style="width:200px;">
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
                        </div> --}}
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
                            <p>Искать в:</p>
                            <label class="__container mx-1">Загаловке
                                <input type="checkbox" class="title_checkbox_tag">
                                <span class="checkmark"></span>
                            </label>
                            <label class="__container mx-1">КС
                                <input type="checkbox" class="cs_checkbox_tag">
                                <span class="checkmark"></span>
                            </label>
                            <label class="__container mx-1">Тексте
                                <input type="checkbox" class="text_checkbox_tag">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                  
                    </div>
                  </div>
            </div>
            <div class="section-tags">
                <div class="container tag_box">
                </div>
            </div>
        </div>
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
                                <h3>Пусто</h3>
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
            <a href="{{ route('backend.parse.parse_articles') }}" class="parse-submit-button">Запустить парсинг выбранных статей</a>
        </div>
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



$(document).on('change', '.checkbox_article', function() {
    let checked_val = 1;
    if($(this).is(':checked')) {
        checked_val = 1;
    } else {
        checked_val = 0;
    }
    $.ajax('{{ route('backend.parse.session_article') }}', {
            type: 'POST', 
            data: { 
                article: $(this).val(),
                is_checked: checked_val,
            },  
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                if(data['message'] == 1) {
                    $('.toast-body').text('Статья удалена из списка для парсинга.');
                } else if(data['message'] == 2) {
                    $('.toast-body').text('Статья добавлена в список для парсинга.');
                } else {
                    $('.toast-body').text('Статья уже в списке для парсинга.');
                }
                $('.toast').toast('show');
            }
    });
});


// $('.secondary-choose').click(function() {
//     $.ajax('{{ route('backend.parse.parse_tags') }}', {
//             type: 'POST', 
//             data: { 
//                 url_section: $(this).data('param'),
//                 source_name: $('.main-choose.choose-active').text(),
//             },  
//             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//             success: function (data) {
//                 console.log(data);
//                 $('.tag_box').html(data);
//             }
//         });
// });

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
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                console.log(data);
                $('.page__container').html(data['articles']);
                $('.tag_box').html(data['tags']);
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
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                console.log(data);
                $('.page__container').html(data['articles']);
                $('.tag_box').html(data['tags']);
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
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                console.log(data);
                $('.page__container').html(data['articles']);
                $('.tag_box').html(data['tags']);
            }
        });
    }
});

$('.search_tags').on('keyup', function() {
    let  value = $(this).val().toLowerCase();
    $('.checkbox_tags').each(function() {
        if($(this).val().toLowerCase().indexOf(value) >= 0) {
            $(this).parent().css('display', 'inline-block') 
        } else {
            $(this).parent().css('display', 'none')
        }
    });
});

$(document).on('click', '.checkbox_tags', function() {
    let value = $(this).val().toLowerCase();
    let iter = 0;
    $('.checkbox_tags').each(function() {
        if($(this).is(':checked')) {
            iter++;
            if($('.title_checkbox_tag').is(':checked')) {
                $('.table__tr').each(function() {
                    if($(this).find('div.table_title').text().toLowerCase().indexOf(value) >= 0) {
                        $(this).css('display', 'block');
                    } else {
                        $(this).css('display', 'none');
                    }
                });
            } 
            if($('.cs_checkbox_tag').is(':checked')) {
                $('.table__tr').each(function() {
                    if($(this).find('div.table_keyWords').text().toLowerCase().indexOf(value) >= 0) {
                        $(this).css('display', 'block');
                    } else {
                        $(this).css('display', 'none');
                    }
                });
            }
            if($('.text_checkbox_tag').is(':checked')) {
                $('.table__tr').each(function() {
                    if($(this).find('div.table_text').text().toLowerCase().indexOf(value) >= 0) {
                        $(this).css('display', 'block');
                    } else {
                        $(this).css('display', 'none');
                    }
                });
            }
        }
    });

    if(iter == 0) {
        $('.table__tr').css('display', '');
    }
});

// Alerts

</script>
@endpush