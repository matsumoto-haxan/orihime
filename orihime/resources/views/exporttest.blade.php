<table>
  <tbody>
  @foreach ($calendars as $cd)
    <tr>
      <td>{{ $cd->id }}</td>
      <td>{{ $cd->date }}</td>
      <td>{{ $cd->month }}</td>
      <td>{{ $cd->day }}</td>
      </tr>
  @endforeach
  </tbody>
</table>