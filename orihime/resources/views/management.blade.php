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
        <!-- ヘッダー -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/home') }}">
                    出荷管理表
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto header_input_list">
                        <li>
                            <select v-model="search.customer_code" v-on:blur="setSearchDeliveryList">
                                <option v-for="option in searchCustomerList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                            </select>
                        </li>
                        <li>
                            <select v-model="search.company_id">
                                <option v-for="option in searchDeliveryList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                            </select>
                        </li>
                        <li class="nav-item">
                            <button class="" v-on:click="getSearchProductList">しぼりこむ</button>
                        </li>
                        <li>
                            <select v-model="search.product_code" v-on:blur="setSearchMaterialList">
                                <option v-for="option in searchProductList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                        </select>
                        </li>
                        <li>
                            <select v-model="search.material_code" v-on:blur="setSearchColorList">
                                <option v-for="option in searchMaterialList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                        </select>
                        </li>
                        <li>
                            <select v-model="search.product_id">
                                <option v-for="option in searchColorList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                        </select>
                        </li>
                        <li>
                            <select v-model="search.delivery_date" >
                                <option v-for="option in searchDateList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
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
                            <td v-for="value in calenderInt" class="calendar_cell">
                                @{{ value }}
                            </td>
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
        </main>


    </div>
</body>
</html>
