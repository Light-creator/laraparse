<div class="pos_stat_count d-flex justify-content-center">
    <div class="stat_section_count">
      <p>Выбрано для парсинга всего</p>
      <p class="stat_count">{{ $count }}</p>
    </div>
  </div>
  <div class="pos_stat_count d-flex justify-content-center mt-3">
    <div class="progress_section">
        @foreach ($percent as $key => $val)
          <div class="progress">
            <p>{{ $key }}</p>
            <div class="progress-bar" role="progressbar" style="width: {{ $val }}%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        @endforeach
    </div>
  </div>