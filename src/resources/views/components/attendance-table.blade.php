<div class="attendance-table-container">
  <table class="attendance-table">
    <thead class="attendance-table__head">
      <tr class="attendance-table__row">
        {{ $thead }}
      </tr>
    </thead>
    <tbody class="attendance-table__body">
      <!-- {{-- スロットを使って、各ページから送られてきた tr セットを流し込む --}} -->
      {{ $slot }}
    </tbody>
  </table>
</div>