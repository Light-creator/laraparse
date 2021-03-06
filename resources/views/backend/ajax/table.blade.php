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
        @foreach($articles as $article)
      <tr class="table__tr">
        <td class="table__td">
          <div class="table__value table_title">{{ $article['title'][0] }}</div>
        </td>
        <td class="table__td">
          <div class="table__value table_keyWords">{{ $article['keyWords'] != '' ? implode(',', $article['keyWords']) : '' }}</div>
        </td>
        <td class="table__td">
          <div class="table__value table_text">{{ $article['meta_tags_article']['desc'][0] }}</div>
        </td>
        <td class="table__td">
          <label class="__container mx-1" style="height: 100%; width: 100%;">
              <input type="checkbox" class="checkbox_article" name="articles[]" value="{{ json_encode($article) }}">
              <span class="checkmark" style=""></span>
          </label>
        </td>
      </tr>
      @endforeach
    </tbody>
</table>