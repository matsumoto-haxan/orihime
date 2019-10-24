@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card cardmargin">
                <div class="card-header">アラート</div>

                <div class="card-body">
                    ※ここにアラート通知対象の発注情報が表示されます
                </div>
            </div>

            <div class="card">
                <div class="card-header">管理機能</div>

                <div class="card-body">
                    <button class="main_func_button" onclick="location.href='/order'">注文管理</button>
                    <button class="main_func_button" onclick="location.href='/delivery'">出荷管理表</button>
                    <button class="main_func_button" onclick="location.href='/shipping'">出荷依頼書</button>
                    <button class="main_func_button" onclick="location.href='/admin/bread'">マスタ編集</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
