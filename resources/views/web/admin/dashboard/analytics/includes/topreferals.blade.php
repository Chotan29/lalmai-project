<h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Top Referals</h4>
<div class="table-responsive">
    {{--@include('includes.data_table_header')--}}
   {{-- <div class="table-header">
        {{ $panel }}  Record list on table. Filter list using search box as your Wish.
    </div>--}}
    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="5%">SN</th>
                <th>URL</th>
                <th>PageView</th>
            </tr>
        </thead>
        <tbody>
        @if (isset($data['top_referrals']) && $data['top_referrals']->count() > 0)
            @php($i=1)
            @foreach($data['top_referrals'] as $row)
                <tr>
                    <td>{{$i}}</td>
                    <td>  {{ $row['url'] }}</td>
                    <td>  {{ $row['pageViews'] }}</td>
                </tr>
                @php($i++)
            @endforeach
        @else
            <tr><td colspan="7">No data fount.</td></tr>
        @endif
        </tbody>
    </table>
</div><!-- /.table-responsive -->
