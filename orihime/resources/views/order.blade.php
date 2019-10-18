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
                        <select v-model="newOrderData.newCustomer_code" v-on:blur="setDeliveryList">
                            <option v-for="option in customer_list" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                    </p>
                    <p>
                        出荷先
                        <select v-model="newOrderData.newCompany_id" v-on:blur="getProductList">
                            <option v-for="option in delivery_list" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                    </p>
                    <p>
                        出荷指図No.
                        <input type="text" v-model="newOrderData.newOpt_order_no">
                    </p>
                    <p>
                        品番
                        <select v-model="newOrderData.newProduct_code" v-on:blur="setMaterialList">
                            <option v-for="option in product_list" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                    </p>
                    <p>
                        生番
                        <select v-model="newOrderData.newMaterial_code" v-on:blur="setColorList">
                            <option v-for="option in material_list" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                    </p>
                    <p>
                        色番
                        <select v-model="newOrderData.newProduct_id" v-on:blur="setProductDetail">
                            <option v-for="option in color_list" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                    </p>
                    <p>
                        納品日
                        <input type="date" v-model="newOrderData.newDelivery_date" v-on:blur="setExpShipDate">
                        （発送予定日：@{{ exp_ship_date }}）
                    </p>
                    <p>
                        メートル数
                        <input type="number" v-model="newOrderData.newOrder_length" v-on:blur="setRoll">m
                    </p>
                    <p>
                        反数
                        <input type="number" v-model="newOrderData.newRoll_amount">
                        （ 一反：@{{ roll_length }}m ）
                    </p>
                    <p>
                        備考
                        <input type="text" v-model="newOrderData.newRemarks">
                    </p>
                    <p>
                        アラート
                        <input type="checkbox" name="check" v-model="newOrderData.newLacking_flg">
                    </p>
                    <p>
                        <input type="button" value="送信" v-on:click="sendNewOrder">
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
                    <p>
                        契約先
                        @{{ detail.customer_name}}
                    </p>
                    <p>
                        出荷先
                        @{{ detail.delivery_name }}
                    </p>
                    <p>
                        出荷指図No.
                        @{{ detail.opt_order_no }}
                    </p>
                    <p>
                        品番
                        @{{ detail.product_code }}
                    </p>
                    <p>
                        生番
                        @{{ detail.material_code }}
                    </p>
                    <p>
                        色番
                        @{{ detail.color_code }}
                    </p>
                    <p>
                        納品日
                        <input type="date" v-model="detail.delivery_date" v-on:blur="updSetExpShipDate">
                        （発送予定日：@{{ detail.exp_ship_date }}）
                    </p>
                        発送日
                        <input type="date" v-model="detail.ship_date">
                    </p>
                    <p>
                        オーダーメートル数
                        <input type="number" v-model="detail.order_length" v-on:blur="updSetRoll">m
                    </p>
                    <p>
                        結果メートル数
                        <input type="number" v-model="detail.result_length">m
                    </p>
                    <p>
                        反数
                        <input type="number" v-model="detail.roll_amount">
                        （ 一反：@{{ detail.roll_length }}m ）
                    </p>
                    <p>
                        備考
                        <input type="text" v-model="detail.remarks">
                    </p>
                    <p>
                        アラート
                        <input type="checkbox" name="check" v-model="detail.lacking_flg">
                    </p>
                    <p>
                        <input type="button" value="更新" v-on:click="sendUpdateOrder">
                        <input type="button" value="削除" v-on:click="sendDeleteOrder">
                    </p>
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
