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
                                          <div class="table__value">{{$article->meta_tags_article->keyWords != '' ? implode(',', $article->meta_tags_article->keyWords) : ''}}</div>
                                        </td>
                                        <td class="table__td staus_mess-{{ $key }}">
                                          {{ $article->status == 1 ? 'В ожидании' : 'Успешно' }}
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
    <div class="block-lang col-lg-3">

    </div>
</div>
@endsection

@push('after-scripts')
<script type="text/javascript">

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
                    $('.staus_mess-'+'{{ $key }}').text('Успешно');
                    console.log(data);
                }
            });
            @endif
        @endforeach
        @endif
    });

</script>
@endpush