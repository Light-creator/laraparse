@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ $module_title }} @endsection

@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection

@section('content')
<div class="container d-flex col-lg-12">
    <div class="block-parse col-lg-8">
        <div class="block-parse-sort">
            <div class="section-sort">
                <div class="row">
                    <div class="col-md-12 select-outline d-flex" style="flex-wrap: wrap;">
                  
                    </div>
                  </div>
            </div>
            <div class="section-tags">
                <div class="container tag_box">
   
                </div>
            </div>
        </div>
        <div class="block-parse d-flex col-md-12">
            <div class="block-parse-article col-md-12">
                <div class="scrollbar scroll col-md-12 style-3">
                    <div class="force-overflow">
                        <div class="page">
                            <div class="page__demo">
                              <div class="main-container page__container">
                                <table class="table">
                                    <thead class="table__thead">
                                      <tr class="table__head">
                                        <th class="table__th">Заголовок</th>
                                        <th class="table__th">Ключевые слова</th>
                                        <th class="table__th">Статус</th>
                                        <th class="table__th">Описание</th>
                                        <th class="table__th"></th>
                                      </tr>
                                    </thead>
                                    <tbody class="table__tbody">
                                        @if(Session::get('articles') !== null)
                                        @foreach(Session::get('articles') as $key => $article)

                                        @php
                                            $article = json_decode($article);
                                        @endphp

                                      <tr class="table__tr">
                                        <td class="table__td">
                                          <div class="table__value">{{ $article->title[0] }}</div>
                                        </td>
                                        <td class="table__td">
                                          <div class="table__value">{{$article->keyWords != '' ? implode(',', $article->keyWords) : ''}}</div>
                                        </td>
                                        <td class="table__td staus_mess-{{ $key }}">
                                          {!! $article->status == 1 ? 'В ожидании' : '<p class="p_success">Успешно</p>' !!}
                                        </td>
                                        <td class="table__td">
                                        </td>
                                        <td class="table__td">
                                        </td>
                                      </tr>
                                      @endforeach
                                      @endif
                                    </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block-lang col-lg-4 bg-white">
      <h5>Статистика парсинга</h5>
      <div class="block_date d-flex d-flex justify-content-center">
        <div class="d-flex mx-1">
          <p class="mt-2 mx-1">С</p>
          <div class="block_icon b_i_2">
              <i class="far fa-calendar-alt"></i>
          </div>
          <input type="text" class="date_from b_i_2" id="datepicker" name="date_from"/>
      </div>
      <div class="d-flex mx-1">
          <p class="mt-2 mx-1">По</p>
          <div class="block_icon b_i_2">
              <i class="far fa-calendar-alt"></i>
          </div>
          <input type="text" id="datepicker2" class="b_i_2" name="date_from"/>
      </div>
      </div>
      <div class="block_stat_ajax">

      </div>
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

    $(document).ready(function() {
        @if(Session::get('articles') !== null)
        @foreach(Session::get('articles') as $key => $article)
            @if(json_decode($article)->status == 1)
            $('.staus_mess-'+'{{ $key }}').text('В работе');

            $.ajax('{{ route('backend.parse.parse_article_ajax') }}', {
                type: 'POST', 
                data: { 
                    article: "{{ $key }}",
                },  
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (data) {
                    $('.staus_mess-'+'{{ $key }}').html('<p class="p_success">Успешно</p>');
                    console.log(data);
                }
            });
            @endif
        @endforeach
        @endif
    });


    $('#foopicker-datepicker').click(function() {
      if($('#datepicker2').val() != '') {
          $.ajax('{{ route('backend.parse.stats') }}', {
              type: 'POST', 
              data: { 
                  date_from: $('#datepicker').val(),
                  date_to: $('#datepicker2').val(),
              },  
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              success: function (data) {
                  console.log(data);
                  $('.block_stat_ajax').html(data);
              }
          });
      }
  });

  $('#foopicker-datepicker2').click(function() {
      if($('#datepicker').val() != '') {
          $.ajax('{{ route('backend.parse.stats') }}', {
              type: 'POST', 
              data: { 
                  date_from: $('#datepicker').val(),
                  date_to: $('#datepicker2').val(),
              },  
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              success: function (data) {
                  console.log(data);
                  $('.block_stat_ajax').html(data);
              }
          });
      }
  });

</script>
@endpush