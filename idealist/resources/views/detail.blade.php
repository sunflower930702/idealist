@section('pageContents')

<div class="pl-6 pt-4 border-b">
    <div class="text-3xl">{{ isset($target) ? $target->name : "" }}</div>
    @if ($parent['parent'] != null)
        @php

            $tmpHtml = "";
            $myParent = $parent;

            $sinbol = "";
            while ($myParent != null) {

                $tmpHtml = "<a class=\"text-blue-400 hover:text-blue-800 px-1 underline\" href=\"route('detail', '" . $myParent['info']->id . ")\">" . $myParent['info']->name . "</a> " . $sinbol . $tmpHtml;
                $myParent = ($myParent['parent'] == null) ? null : $myParent['parent'];

                $sinbol = ">";
            }

            echo $tmpHtml;
        @endphp
    @endif
</div>

<div class="pt-8">
    {{ $target->contents }}
</div>

<div class="pt-12">
    <div class="px-6 border-b text-xl">
        属性：
    </div>
    <div class="pt-4 pl-4">
        <table width="700px" class="pl-4">
            <tr>
                <th width="10%" class="bg-gray-100 border">
                    No.
                </th>
                <th width="30%" class="bg-gray-100 border">
                    属性名
                </th>
                <th width="60%" class="bg-gray-100 border">
                    値
                </th>
            </tr>
            @foreach($properties as $key => $item)
                <td class="border">
                    {{ $key }}
                </td>
                <td class="border">
                    {{{ $item->name }}}
                </td>
                <td class="border">
                    {{ $item->value }}
                </td>
            @endforeach
        </table>
    </div>
</div>

<div class="pt-12">
    <div class="px-6 border-b text-xl">
        行動：
    </div>
    <div class="pt-4 pl-4">
        <table width="700px" class="pl-4">
            <tr>
                <th width="10%" class="bg-gray-100 border">
                    No.
                </th>
                <th width="90%" class="bg-gray-100 border">
                    アクション名
                </th>
            </tr>
            @foreach($methods as $key => $item)
                <td class="border">
                    {{ $key }}
                </td>
                <td class="border">
                    {{{ $item->name }}}
                </td>
            @endforeach
        </table>
    </div>
</div>

<div class="pt-12">
    <div class="px-6 border-b text-xl">
        <div class="inline-flex">
            自分の継承先：
        </div>
    </div>
    <div class="pl-8">
        @foreach($child as $item)
            ・<a class="text-blue-400 hover:text-blue-800 px-1 underline" href="{{ route('detail', $item->id) }}">{{ $item->name }}</a>@if($deep > 1)（{{ $deep - $item->deep + 1 }}階層下）@endif</br>
        @endforeach
    </div>

    <div class="pt-12">
        <div class="px-6">
            表示条件
        </div>
        <div class="pl-10">
            <form method="GET" action="{{ route('detail', $id) }}">
                <input name="id" type="hidden">
                深さ： <input name="deep" style="max-width:40px;" class="border border-gray-400" type="text" value="{{ $deep }}"> <input class="border px-2 hover:bg-gray-300" type="submit" value="検索">
            </form>
        </div>
    </div>

</div>

@endsection

@include('layouts.main')