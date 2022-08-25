
@if($type==1)
    <a href="/admin/food/takeOutPool/import" class="btn btn-sm btn-twitter" title="导入记录">
        <i class="fa fa-download"></i><span class="hidden-xs">&nbsp;&nbsp;导入记录</span>
    </a>
@elseif($type==2)
    <a href="/admin/food/reservePool/import" class="btn btn-sm btn-twitter" title="导入记录">
        <i class="fa fa-download"></i><span class="hidden-xs">&nbsp;&nbsp;导入记录</span>
    </a>
@else
    <a href="/admin/food/marketFoodPool/import" class="btn btn-sm btn-twitter" title="导入记录">
        <i class="fa fa-download"></i><span class="hidden-xs">&nbsp;&nbsp;导入记录</span>
    </a>
@endif