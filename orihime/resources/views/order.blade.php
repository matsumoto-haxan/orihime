<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/order.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/haxanstyle.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">

        <!-- モーダルウィンドウ -->
        <div class="modal_wrap">
            <input id="trigger" type="checkbox">
	        <div class="modal_overlay">
	            <label for="trigger" class="modal_trigger"></label>
	            <div class="modal_content">
		            <label for="trigger" class="close_button">&#x2716;&#xfe0f;</label>
		            <h2>注文新規登録</h2>
                    <p>
                        契約先
                        <select>
                            @foreach($customer_list as $index => $name)
                                <option value="{{ $index }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </p>
                    <p>
                        出荷先
                        <select>
                            @foreach($delivery_list as $index => $name)
                                <option value="{{ $index }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </p>
                    <p>
                        気付
                        <input type="text">
                    </p>
                    <p>
                        出荷指図No.
                        <input type="text">
                    </p>
                    <p>
                        品番
                        <select>
                            @foreach($material_list as $index => $name)
                                <option value="{{ $index }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </p>
                    <p>
                        色番
                        <select>
                            @foreach($color_list as $index => $name)
                                <option value="{{ $index }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </p>
                    <p>
                        納品日
                        <input type="date">
                    </p>
                    <p>
                        反数
                        <input type="number">
                    </p>
                    <p>
                        備考
                        <input type="text">
                    </p>
                    <p>
                        アラート
                        <input type="checkbox" name="check">
                    </p>
                    <p>
                        <input type="button" value="送信">
                    </p>
		        </div>
	        </div>
        </div>

        <!-- ヘッダー -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/home') }}">
                    注文管理
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto header_input_list">
                        <li>
                            <select>
                                @foreach($customer_list as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </li>

                        <li>
                            <select>
                                @foreach($delivery_list as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </li>

                        <li>
                            <select>
                                @foreach($material_list as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </li>

                        <li>
                            <select>
                                @foreach($color_list as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </li>

                        <li>
                            <select>
                                @foreach($date_list as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </li>

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        <li class="nav-item">
                            <button class="" v-on:click="searchOrders">表示</button>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">

        <div class="container order_table_container">
                <div class="row justify-content-center">
            <div>
                <table class="order_table">
                    <!-- ヘッダー行 -->
                    <thead>
                        <tr>
                            <td>品番</td>
                            <td>出荷先</td>
                            @foreach ($caldate as $day)
                                <td class="calendar_cell">{{ $day }}</td>
                            @endforeach
                        </tr>
                    </thead>

                    <!-- ２行目以降 -->
                    <tbody>
                    <tr v-for="order in orders">
                        <td>@{{order.product_code}}</td>
                        <td>@{{order.delivery_name}}</td>
                        <template v-for='dayInt in calenderInt'>
                            <template v-for='element in order.delivery_date'>
                                <template v-if="element.day == dayInt">
                                    <template v-if="element.order_id">
                                        <td v-on:click="showOrder(element.order_id)"
                                            :class="lackingColor(element.lacking_flg)" >
                                            @{{element.order_length}}
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td >
                                        </td>
                                    </template>
                                </template>
                            </template>
                        </template>
                    </tr>
                    </tbody>
                </table>
            </div>
            </div>
            </div>


            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card cardmargin">
                    <div class="card-header">発注内容</div>
                    <div class="card-body">
                        <p>{{ $message }}</p>
                    </div>
                </div>
            </div>

        </main>



        <div class="flex">
            <div class="rigthBox">
                <label for="trigger" class="open_button">
                <a class="fab" >
                    <i class="fas fa-plus"></i>
                </a>
                </label>
            </div>
        </div>


    </div>
</body>
</html>
