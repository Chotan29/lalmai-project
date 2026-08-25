@extends('layouts.master')

@section('content')

<div class="page-content">
    <div class="row">
        <div class="col-xs-12">

            @include('includes.flash_messages')
            @include('includes.validation_error_messages')

            <div class="page-header">
                <h1>{{ $panel }}
                    <small><i class="ace-icon fa fa-angle-double-right"></i>
                        Where each fee head's money is transferred to
                    </small>
                </h1>
            </div>

            {{-- Said plainly, once, at the top. These numbers were read off two photographs of the
                 college's own paperwork; the printed list is clear enough, the handwritten note
                 much less so. Nothing here is in the database yet - the boxes are filled in as a
                 starting point and only what is saved on this page counts. A wrong digit sends
                 money to somebody else's account, and the bank will not send it back. --}}
            @if(!$data['has_any'])
                <div class="alert alert-warning">
                    <h4 class="alert-heading">Check every number before saving</h4>
                    The account numbers below were read from your two photographs and are
                    <strong>not saved yet</strong>. Look at each one against the bank's own paper,
                    correct anything that is wrong, then press Save.
                    The handwritten department numbers are the least certain.
                </div>
            @endif

            {!! Form::open(['route' => 'account.fees.fee-head-bank-account.store', 'method' => 'POST', 'id' => 'bank-account-form']) !!}

            @php
                /* One running index across every row on the page, so the rows come back as a
                   simple list rather than a nest the controller has to unpick. */
                $i = 0;
                $religionList = $data['religions']->toArray();
            @endphp

            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th style="width:20%">Fee Head</th>
                        {{-- Where this account appears on the letter. The bank reads each letter
                             against the last one, so the order has to hold from one to the next. --}}
                        <th style="width:6%">No. on letter</th>
                        <th style="width:16%">Department</th>
                        <th style="width:22%">Account Name</th>
                        <th style="width:16%">Account Number</th>
                        <th style="width:14%">Only for</th>
                        <th style="width:6%">Catch-all</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($data['fee_heads'] as $head)
                    @php
                        $saved = $data['accounts']->get($head->id, collect())->where('status', 1);
                        /* Show what is saved. Where nothing is saved yet, show what the paperwork
                           appears to say - clearly, so it can be checked rather than assumed. */
                        $lines = $saved->count()
                            ? $saved->map(function ($a) {
                                return [
                                    'id'             => $a->id,
                                    'serial'         => $a->getAttributes()['serial'] ?? 0,
                                    'faculty_id'     => $a->faculty_id,
                                    'account_name'   => $a->account_name,
                                    'account_no'     => $a->account_no,
                                    'match_religion' => $a->religions(),
                                    'is_default'     => $a->is_default,
                                    'note'           => $a->note,
                                    'fresh'          => false,
                                ];
                              })->values()->all()
                            : array_map(function ($s) {
                                return $s + ['id' => null, 'serial' => 0, 'faculty_id' => null, 'match_religion' => [],
                                             'is_default' => false, 'note' => null, 'fresh' => true];
                              }, $data['suggestions'][$head->id] ?? []);

                        /* Always leave one empty line so another account can be added without
                           anybody having to look for an Add button. */
                        $lines[] = ['id' => null, 'serial' => 0, 'faculty_id' => null, 'account_name' => '', 'account_no' => '',
                                    'match_religion' => [], 'is_default' => false, 'note' => null, 'fresh' => false];
                        $span = count($lines);
                    @endphp

                    @foreach($lines as $n => $line)
                        <tr class="{{ !empty($line['fresh']) ? 'warning' : '' }}">
                            @if($n === 0)
                                <td rowspan="{{ $span }}">
                                    <strong>{{ $head->fee_head_title }}</strong><br>
                                    <small class="text-muted">
                                        {{ ucfirst($head->collected_by) }} &middot; &#2547;{{ number_format($head->fee_head_amount, 2) }}
                                    </small>
                                </td>
                            @endif

                            <td>
                                <input type="hidden" name="rows[{{ $i }}][fee_head_id]" value="{{ $head->id }}">
                                <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $line['id'] }}">

                                {{-- Left at 0 the account still appears on the letter, at the end
                                     rather than in the college's numbered sequence. --}}
                                <input type="number" class="form-control input-sm" min="0" max="999"
                                       name="rows[{{ $i }}][serial]"
                                       value="{{ $line['serial'] ?: '' }}" placeholder="0">
                            </td>

                            <td>
                                {!! Form::select("rows[$i][faculty_id]",
                                        ['' => 'Every student'] + $data['faculties']->toArray(),
                                        $line['faculty_id'],
                                        ['class' => 'form-control input-sm']) !!}
                            </td>

                            <td>
                                <input type="text" class="form-control input-sm"
                                       name="rows[{{ $i }}][account_name]"
                                       value="{{ $line['account_name'] }}" maxlength="191">
                                @if(!empty($line['note']))
                                    <small class="text-muted">{{ $line['note'] }}</small>
                                @endif
                            </td>

                            <td>
                                <input type="text" class="form-control input-sm"
                                       name="rows[{{ $i }}][account_no]"
                                       value="{{ $line['account_no'] }}" maxlength="50"
                                       autocomplete="off">
                            </td>

                            <td>
                                {{-- Only used where a head splits by religion. Left empty the
                                     account takes anybody. --}}
                                <select name="rows[{{ $i }}][match_religion][]" class="form-control input-sm" multiple size="3">
                                    @foreach($religionList as $religion)
                                        <option value="{{ $religion }}"
                                            {{ in_array($religion, (array) $line['match_religion']) ? 'selected' : '' }}>
                                            {{ $religion }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="text-center">
                                {{-- The account that takes whatever the rules above did not claim.
                                     Without one, a student whose religion was never recorded would
                                     have money in no account at all and the letter would not add
                                     up to the head. --}}
                                <input type="checkbox" name="rows[{{ $i }}][is_default]" value="1"
                                       {{ !empty($line['is_default']) ? 'checked' : '' }}>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @endforeach
                </tbody>
            </table>

            <div class="form-group">
                <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Save these bank accounts? Check the numbers first - money follows them.');">
                    <i class="fa fa-save"></i> Save all accounts
                </button>
                <span class="text-muted" style="margin-left:10px">
                    Rows left empty are ignored. An account removed from this page is switched off, not deleted.
                </span>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

@endsection
