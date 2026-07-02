<div class="applications-container">
    <div class="applications-header">
        <h3><i class="fas fa-list-ul"></i> My Applications</h3>
        <div class="applications-filter">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search applications..." id="application-search">
            </div>
        </div>
    </div>
    
    <div class="applications-table-container">
        <table class="applications-table">
            <thead>
                <tr>
                    <th width="40px"></th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th width="100px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['application']) && $data['application']->count() > 0)
                    @foreach($data['application'] as $application)
                        <tr>
                            <td>
                                <label class="table-checkbox">
                                    <input type="checkbox" name="chkIds[]" value="{{ $application->id }}">
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td>{{ isset($application->application_type_id)?ViewHelper::getApplicationTypeById($application->application_type_id):'' }}</td>
                            <td>
                                <div class="date-cell">
                                    {{\Carbon\Carbon::parse($application->date)->format('M d, Y')}}
                                </div>
                            </td>
                            <td>{{ $application->subject }}</td>
                            <td>
                                <span class="status-badge {{ $application->status == 'active' ? 'approved' : 'pending' }}">
                                    {{ $application->status == 'active' ? 'Approved' : 'Pending' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    @if($application->status != 'active')
                                        <a href="{{ route('user-student.application.edit', ['id' => encrypt($application->id)]) }}" 
                                           class="action-btn edit" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="{{ route('user-student.application.delete', ['id' => encrypt($application->id)]) }}" 
                                           class="action-btn delete bootbox-confirm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="no-data">
                            <div class="no-data-content">
                                <i class="fas fa-file-alt"></i>
                                <p>No applications found</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    @if (isset($data['application']) && method_exists($data['application'], 'links'))
        <div class="table-footer">
            <div class="table-info">
                Showing {{ $data['application']->firstItem() }} to {{ $data['application']->lastItem() }} of {{ $data['application']->total() }} entries
            </div>
            <div class="table-pagination">
                {{ $data['application']->links() }}
            </div>
        </div>
    @endif
</div>
