<style>
@font-face{
    font-family: ipag;
    font-style: normal;
    font-weight: normal;
    src:url('{{ storage_path("fonts/ipag.ttf")}}');
}
@font-face{
    font-family: ipag;
    font-style: bold;
    font-weight: bold;
    src:url('{{ storage_path("fonts/ipag.ttf")}}');
}
body {
font-family: ipag;
}
td {
  border: 1px solid red !important;
}
</style>

<table>
  <tbody>
  @foreach ($calendars as $cd)
    <tr class="pdfFonts">
      <td>{{ $cd->id }}</td>
      <td>{{ $cd->date }}</td>
      <td>{{ $cd->month }}</td>
      <td>{{ $cd->day }}</td>
      <td>{{ $cd->weekday }}</td>
      </tr>
  @endforeach
  </tbody>
</table>