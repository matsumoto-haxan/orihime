<style>
@font-face {
    font-family: ipag;
    font-style: normal;
    font-weight: normal;
    src:url('{{ storage_path("fonts/ipag.ttf")}}');
}
@font-face {
    font-family: ipag;
    font-style: bold;
    font-weight: bold;
    src:url('{{ storage_path("fonts/ipag.ttf")}}');
}
body {
font-family: ipag;
}
.title {
    text-align: center;
}
.titleDate {
    text-align: right;
}
table {
    border-collapse: collapse;
    border-spacing: 0;
}
td {
    text-align: center;
    border-right: 1px solid ;
    border-bottom: 1px solid ;
    font-size: 8px;
    margin: 0px;
}
td.calendarCell {
    width: 25px;
    border-bottom: 2px solid ;
}
td.nameCell {
    width: 40px;
    border-bottom: 1px solid ;
}
td.prdCell,
td.dlvCell {
    width: 120px;
    border-left: solid 1px;
    border-bottom: 2px solid ;
}
td.SeparateCell {
    border-bottom: 2px solid ;
}
tr:first-child {
  border-top: 1px solid ;
}
</style>

<div class="mngTableWrapper">
    <h2 class="title">出荷管理表</h2>
    <h4 class="titleDate">出力日：{{ $exportDate }}</h4>
    <table class="exportMngTable">
        <!-- ヘッダー行 -->
        <thead>
            <tr>
                <td class="prdCell">品番</td>
                <td class="dlvCell">出荷先</td>
                <td class="nameCell separateCell">項目</td>
                @foreach ($calendarInt as $elm)
                <td class="calendarCell">
                    {{ $elm }}
                </td>
                @endforeach
            </tr>
        </thead>

        <tbody>
        <!-- 1行目 -->
        @foreach ($results as $orderSet)
            <tr >
                <td class="separateCell prdCell" rowspan='4'> {{ $orderSet['product_code'] }}</td>
                <td class="separateCell dlvCell" rowspan='4'> {{ $orderSet['delivery_name'] }}</td>
                <td class="nameCell">発送日</td>
                @foreach ($orderSet['exp_ship_date'] as $elm)
                <td>
                    {{ $elm }}
                </td>
                @endforeach
            </tr>

            <!-- 2行目 -->
            <tr>
                <td class="nameCell">注文m</td>
                @foreach ($orderSet['order_length'] as $elm)
                <td >
                    {{ $elm }}
                </td>
                @endforeach
            </tr>

            <!-- 3行目 -->
            <tr>
                <td class="nameCell">結果m</td>
                @foreach ($orderSet['result_length'] as $elm)
                <td>
                    {{ $elm }}
                </td>
                @endforeach
            </tr>


            <!-- 4行目 -->
            <tr>
                <td class="nameCell separateCell">反数</td>
                @foreach ($orderSet['roll_amount'] as $elm)
                <td class="separateCell">
                    {{ $elm }}
                </td>
                @endforeach
            </tr>
        @endforeach
        

        </tbody>
    </table>
</div>