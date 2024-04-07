<script>

    /**
     * 支払行を足す
     */
    function addPaymentRow() {

        var table = document.getElementById("paymentListTable");

        // -------------------
        // キーを取得
        // -------------------

        var rows = table.rows;

        var nextKey = 0;
        for (var i = 0; i < rows.length; i++) {

            if (nextKey < rows[i].getAttribute("key")) {
                nextKey = rows[i].getAttribute("key");
            }
        }
        nextKey++;

        // -------------------
        // 行オブジェクトの作成
        // -------------------

        var tr = document.createElement("tr");
        tr.setAttribute("class", 'odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700');
        tr.setAttribute("key", nextKey);

        // 年カラム
        var td1 = document.createElement("td");
        td1.setAttribute("class", 'px-6 py-4');
        tr.appendChild(td1);

        var inputYear = document.createElement("input");
        inputYear.setAttribute("type", "text");
        inputYear.setAttribute("name", "paymentList[" + nextKey + "][targetYear]");
        inputYear.setAttribute("class", "w-full h-full border border-gray-400");
        td1.appendChild(inputYear);

        // 「年」
        var td2 = document.createElement("td");
        td2.setAttribute("class", 'px-6 py-4');
        td2.textContent = "年";
        tr.appendChild(td2);

        // 月カラム
        var td3 = document.createElement("td");
        td3.setAttribute("class", 'px-6 py-4');
        tr.appendChild(td3);

        var inputMonth = document.createElement("input");
        inputMonth.setAttribute("type", "text");
        inputMonth.setAttribute("name", "paymentList[" + nextKey + "][targetMonth]");
        inputMonth.setAttribute("class", "w-full h-full border border-gray-400");
        td3.appendChild(inputMonth);

        // 「月」
        var td4 = document.createElement("td");
        td4.setAttribute("class", 'px-6 py-4');
        td4.textContent = "月";
        tr.appendChild(td4);

        // 支払フラグカラム
        var td5 = document.createElement("td");
        td5.setAttribute("class", 'px-6 py-4');
        tr.appendChild(td5);

        var inputPaymentFlg = document.createElement("input");
        inputPaymentFlg.setAttribute("type", "checkBox");
        inputPaymentFlg.setAttribute("name", "paymentList[" + nextKey + "][paymentFlg]");
        inputPaymentFlg.setAttribute("value", "1");
        td5.appendChild(inputPaymentFlg);

        // 削除ボタン
        var td6 = document.createElement("td");
        td6.setAttribute("class", 'px-6 py-4');
        tr.appendChild(td6);

        var deleteButton = document.createElement("a");
        deleteButton.setAttribute("class", "text-blue-400 hover:text-blue-800 px-1 underline");
        deleteButton.setAttribute("href", "javascript:removePaymentRow(" + nextKey + ")");
        deleteButton.textContent = "削除";
        td6.appendChild(deleteButton);


        // テーブルに追加
        table.appendChild(tr);
    }

    /**
     * 支払行を消す
     */
    function removePaymentRow() {

        var message = "{{ getLanguage('messages.checkDelete') }}";
        if (window.confirm(message)) {
            document.forms["deleteForm"].submit();
        }
    }
</script>


@section('pageContents')

<form method="POST" action="{{ route('update') }}">

<input type="hidden" name="id" value="{{ $id }}">

<div class="pl-6 pt-4 border-b">
    <div class="text-3xl">{{ isset($target) ? $target->name : "" }}（編集中）</div>
</div>

<div class="pt-8">
    <textarea name="contents" type="text" class="border border-gray-400" wrap="off">
        {{ old('contents') != '' ? old('contents') : $contents }}
    </textarea>
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
                <th width="45%" class="bg-gray-100 border">
                    値
                </th>
                <th width="15%" class="bg-gray-100 border">
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
                <td class="border">
                    <a class="text-blue-400 hover:text-blue-800 px-1 underline" href="javaScript:removePaymentRow({{$key}});">削除</a>
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
</div>

@endsection

@include('layouts.main')
