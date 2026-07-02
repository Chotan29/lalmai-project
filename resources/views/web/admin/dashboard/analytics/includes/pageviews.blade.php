<h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Top Pages Views</h4>
<div class="table-responsive">
    {{--@include('includes.data_table_header')--}}
   {{-- <div class="table-header">
        {{ $panel }}  Record list on table. Filter list using search box as your Wish.
    </div>--}}
    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>SN</th>
                <th>PageView</th>
                <th>URL</th>
                <th>PageTitle</th>
            </tr>
        </thead>
        <tbody>
        @if (isset($data['page_views']) && $data['page_views']->count() > 0)
            @php($i=1)
            @foreach($data['page_views'] as $row)
                <tr>
                    <td>{{$i}}</td>
                    <td>  {{ $row['pageViews'] }}</td>
                    <td>  {{ $row['url'] }}</td>
                    <td>  {{ $row['pageTitle']}}</td>
                </tr>
                @php($i++)
            @endforeach
        @else
            <tr><td colspan="7">No data fount.</td></tr>
        @endif
        </tbody>
    </table>
</div><!-- /.table-responsive -->
