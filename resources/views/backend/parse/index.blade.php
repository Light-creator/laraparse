@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ $module_title }} @endsection

@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection

@section('content')
<div class="container d-flex">
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
                            <input type="text" id="datepicker" name="date_from"/>
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
                                <input type="checkbox" checked="checked">
                                <span class="checkmark"></span>
                            </label>
                            <label class="__container mx-1">Тегах
                                <input type="checkbox" checked="checked">
                                <span class="checkmark"></span>
                            </label>
                            <label class="__container mx-1">КС
                                <input type="checkbox" checked="checked">
                                <span class="checkmark"></span>
                            </label>
                            <label class="__container mx-1">Тексте
                                <input type="checkbox" checked="checked">
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
        <div class="block-parse d-flex col-md-12">
            <div class="scrollbar col-md-3 style-3" >
                <div class="force-overflow">
                    <a class="btn btn-light col-md-12 link-choose main-choose" data-param="1">Источник 1</a>
                    <div class="block_dropdown_choose_link" id="dropdown-1" style="display: none;">
                        <a class="btn btn-light col-md-12 bg-white link-choose">Раздел</a>
                        <a class="btn btn-light col-md-12 bg-white link-choose">Раздел</a>
                        <a class="btn btn-light col-md-12 bg-white link-choose">Раздел</a>
                        <a class="btn btn-light col-md-12 bg-white link-choose">Раздел</a>
                        <a class="btn btn-light col-md-12 bg-white link-choose">Раздел</a>
                    </div>
                </div>
            </div>
            <div class="block-parse-article col-md-9">
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
                                      <th class="table__th">Текст</th>
                                      <th class="table__th"></th>
                                    </tr>
                                  </thead>
                                  <tbody class="table__tbody">
                                    <tr class="table__tr">
                                      <td class="table__td">
                                        <div class="table__value">Wedding day coverage</div>
                                      </td>
                                      <td class="table__td">
                                        <div class="table__value">6 hours</div>
                                      </td>
                                      <td class="table__td">
                                        <div class="table__value">8 hours</div>
                                      </td>
                                      <td class="table__td">
                                        <label class="__container mx-1" style="height: 100%; width: 100%;">
                                            <input type="checkbox">
                                            <span class="checkmark" style=""></span>
                                        </label>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="submit-parse-block col-md-12">
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

$('.main-choose').click(function() {
    if($(this).hasClass('choose-active')) {
        $('#dropdown-'+$(this).data('param')).css('display', 'none');
        $(this).removeClass('choose-active');
    } else {
        $('#dropdown-'+$(this).data('param')).css('display', 'block');
        $(this).addClass('choose-active');
    }
});

</script>
@endpush