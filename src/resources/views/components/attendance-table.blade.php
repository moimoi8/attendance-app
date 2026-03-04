<div class="attendance-table-container">
  <table class="attendance-table">
    <thead class="attendance-table__head">
      <tr class="attendance-table__row">
        <th class="attendance-table__header">{{ $th1 }}</th>
        <th class="attendance-table__header">出勤</th>
        <th class="attendance-table__header">退勤</th>
        <th class="attendance-table__header">休憩</th>
        <th class="attendance-table__header">合計</th>
        <th class="attendance-table__header">詳細</th>
      </tr>
    </thead>
    <tbody class="attendance-table__body">
      <!-- {{-- スロットを使って、各ページから送られてきた tr セットを流し込む --}} -->
      {{ $slot }}
    </tbody>
  </table>
</div>