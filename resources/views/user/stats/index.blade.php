@extends('user.stats.base')

@section('title', $user->display_name . ' さんのグラフ')

@section('stats-content')
    <h5 class="my-4">年間チェックイン回数</h5>
    <canvas id="yearly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">時間別チェックイン回数</h5>
    <canvas id="hourly-graph" class="w-100"></canvas>
    <hr class="my-4">
    <h5 class="my-4">曜日別チェックイン回数</h5>
    <canvas id="dow-graph" class="w-100"></canvas>
    <hr class="my-4">
    @include('user.stats.components.used-tags')
@endsection

@push('script')
<script id="graph-data" type="application/json">@json($graphData)</script>
<script src="{{ mix('js/vendor/chart.js') }}"></script>
<script src="{{ mix('js/user/stats.js') }}"></script>
@endpush
