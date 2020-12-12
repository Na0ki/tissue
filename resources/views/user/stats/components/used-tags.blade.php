<h5 class="mb-4">最も使用したタグ</h5>
<div class="row">
    <div class="col-md-6">
        <h6 class="mb-2 text-center"><span class="tis-stat-table-category-checkin">チェックインタグ</span></h6>
        <p class="mb-3 text-center"><small class="text-muted">直接入力されたタグのみ集計しています。</small></p>
        @if ($tags->isEmpty())
            <div class="alert alert-secondary">
                データがありません
            </div>
        @else
            <table class="table table-striped border">
                <tbody>
                @foreach ($tags as $tag)
                    <tr>
                        <td><a class="text-reset" href="{{ route('search', ['q' => $tag->name]) }}"><span class="oi oi-tag text-secondary mr-2"></span>{{ $tag->name }}</a></td>
                        <td class="text-right">{{ number_format($tag->count) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div class="col-md-6">
        <h6 class="mt-3 mt-md-0 mb-2 text-center"><span class="tis-stat-table-category-checkin">チェックインタグ</span> + <span class="tis-stat-table-category-metadata">オカズタグ</span></h6>
        <p class="mb-3 text-center"><small class="text-muted">オカズ自体のタグも含めた集計です。</small></p>
        @if ($tagsIncludesMetadata->isEmpty())
            <div class="alert alert-secondary">
                データがありません
            </div>
        @else
            <table class="table table-striped border">
                <tbody>
                @foreach ($tagsIncludesMetadata as $tag)
                    <tr>
                        <td><a class="text-reset" href="{{ route('search', ['q' => $tag->name]) }}"><span class="oi oi-tag text-secondary mr-2"></span>{{ $tag->name }}</a></td>
                        <td class="text-right">{{ number_format($tag->count) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
