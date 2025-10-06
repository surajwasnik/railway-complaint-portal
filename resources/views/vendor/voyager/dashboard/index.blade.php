@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid dashboard-cards">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row mb-4 cards-row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M31.2499 37.5H20.8333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M37.4999 29.1667H20.8333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8.33342 45.8333H41.6668C42.7718 45.8333 43.8316 45.3944 44.613 44.613C45.3944 43.8316 45.8334 42.7717 45.8334 41.6667V8.33334C45.8334 7.22827 45.3944 6.16846 44.613 5.38706C43.8316 4.60566 42.7718 4.16667 41.6668 4.16667H16.6667C15.5617 4.16667 14.5019 4.60566 13.7205 5.38706C12.9391 6.16846 12.5001 7.22827 12.5001 8.33334V41.6667C12.5001 42.7717 12.0611 43.8316 11.2797 44.613C10.4983 45.3944 9.43848 45.8333 8.33342 45.8333ZM8.33342 45.8333C7.22835 45.8333 6.16854 45.3944 5.38714 44.613C4.60573 43.8316 4.16675 42.7717 4.16675 41.6667V22.9167C4.16675 21.8116 4.60573 20.7518 5.38714 19.9704C6.16854 19.189 7.22835 18.75 8.33342 18.75H12.5001" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M35.4166 12.5H22.9166C21.766 12.5 20.8333 13.4327 20.8333 14.5833V18.75C20.8333 19.9006 21.766 20.8333 22.9166 20.8333H35.4166C36.5672 20.8333 37.4999 19.9006 37.4999 18.75V14.5833C37.4999 13.4327 36.5672 12.5 35.4166 12.5Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <h4>Total Complaints</h4>
                                        <p>{{ $totalComplaints }}</p>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="col-md-3">-->
                            <!--    <div class="card">-->
                            <!--        <div class="card-body">-->
                            <!--            <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                            <!--            <path d="M31.2499 25H20.8333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>-->
                            <!--            <path d="M31.2499 16.6667H20.8333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>-->
                            <!--            <path d="M39.5833 35.4167V10.4167C39.5833 9.3116 39.1443 8.25179 38.3629 7.47039C37.5815 6.68899 36.5217 6.25 35.4166 6.25H8.33325" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>-->
                            <!--            <path d="M16.6667 43.75H41.6668C42.7718 43.75 43.8316 43.311 44.613 42.5296C45.3944 41.7482 45.8334 40.6884 45.8334 39.5833V37.5C45.8334 36.9475 45.6139 36.4176 45.2232 36.0269C44.8325 35.6362 44.3026 35.4167 43.7501 35.4167H22.9167C22.3642 35.4167 21.8343 35.6362 21.4436 36.0269C21.0529 36.4176 20.8334 36.9475 20.8334 37.5V39.5833C20.8334 40.6884 20.3944 41.7482 19.613 42.5296C18.8316 43.311 17.7718 43.75 16.6667 43.75ZM16.6667 43.75C15.5617 43.75 14.5019 43.311 13.7205 42.5296C12.9391 41.7482 12.5001 40.6884 12.5001 39.5833V10.4167C12.5001 9.3116 12.0611 8.25179 11.2797 7.47039C10.4983 6.68899 9.43848 6.25 8.33342 6.25C7.22835 6.25 6.16854 6.68899 5.38714 7.47039C4.60573 8.25179 4.16675 9.3116 4.16675 10.4167V14.5833C4.16675 15.1359 4.38624 15.6658 4.77694 16.0565C5.16764 16.4472 5.69755 16.6667 6.25008 16.6667H12.5001" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>-->
                            <!--            </svg>-->
                            <!--            <h4>Pending Complaint</h4>-->
                            <!--            <p>{{ $pendingComplaints }}</p>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M33.3333 29.1667V33.75L36.6666 35.8333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M33.3333 8.33333H37.4999C38.605 8.33333 39.6648 8.77231 40.4462 9.55372C41.2276 10.3351 41.6666 11.3949 41.6666 12.5V14.2333" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16.6666 8.33333H12.4999C11.3948 8.33333 10.335 8.77231 9.55364 9.55372C8.77224 10.3351 8.33325 11.3949 8.33325 12.5V41.6667C8.33325 42.7717 8.77224 43.8315 9.55364 44.6129C10.335 45.3943 11.3948 45.8333 12.4999 45.8333H16.6666" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M33.3333 45.8333C40.2368 45.8333 45.8333 40.2369 45.8333 33.3333C45.8333 26.4298 40.2368 20.8333 33.3333 20.8333C26.4297 20.8333 20.8333 26.4298 20.8333 33.3333C20.8333 40.2369 26.4297 45.8333 33.3333 45.8333Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M31.2501 4.16667H18.7501C17.5995 4.16667 16.6667 5.09941 16.6667 6.25001V10.4167C16.6667 11.5673 17.5995 12.5 18.7501 12.5H31.2501C32.4007 12.5 33.3334 11.5673 33.3334 10.4167V6.25001C33.3334 5.09941 32.4007 4.16667 31.2501 4.16667Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <h4>In Progress Complaint</h4>
                                        <p>{{ $investigationComplaints }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M31.2501 4.16667H18.7501C17.5995 4.16667 16.6667 5.09941 16.6667 6.25001V10.4167C16.6667 11.5673 17.5995 12.5 18.7501 12.5H31.2501C32.4007 12.5 33.3334 11.5673 33.3334 10.4167V6.25001C33.3334 5.09941 32.4007 4.16667 31.2501 4.16667Z" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M33.3333 8.33333H37.4999C38.605 8.33333 39.6648 8.77231 40.4462 9.55372C41.2276 10.3351 41.6666 11.3949 41.6666 12.5V41.6667C41.6666 42.7717 41.2276 43.8315 40.4462 44.6129C39.6648 45.3943 38.605 45.8333 37.4999 45.8333H12.4999C11.3948 45.8333 10.335 45.3943 9.55364 44.6129C8.77224 43.8315 8.33325 42.7717 8.33325 41.6667V12.5C8.33325 11.3949 8.77224 10.3351 9.55364 9.55372C10.335 8.77231 11.3948 8.33333 12.4999 8.33333H16.6666" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.75 29.1667L22.9167 33.3333L31.25 25" stroke="#1C398E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <h4>Resolved Complaint</h4>
                                        <p>{{ $detectedRecoveredComplaints }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-heading-sec">
                            <h3>Recent Complaints</h3>
                            <a href="admin/complaints" class="btn-pr">View All</a>
                        </div>
                        <div class="table-container">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        @if(Auth::user()->role->id == 1)
                                            <th>Police Station</th>
                                        @endif
                                        <th>FIR Number</th>
                                        <th>FIR Date</th>
                                        <th>Name</th>
                                        <th>Mobile Number</th>
                                        <th>I/O Officer</th>
                                        <th>I/O Officer Number</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($complaints as $complaint)
                                        <tr>
                                            @if(Auth::user()->role->id == 1)
                                                <td>{{ $complaint->station->station_name }}</td>
                                            @endif
                                            <td><span class="fir-number">{{ $complaint->fir_number }}</span></td>
                                            <td>{{ \Carbon\Carbon::parse($complaint->fir_date)->format('d/m/Y') }}</td>
                                            <td>{{ $complaint->complainant_name }}</td>
                                            <td>{{ $complaint->complainant_number }}</td>
                                            <td class="officer-name">{{ $complaint->officer_name }}</td>
                                            <td>{{ $complaint->police_station_number }}</td>
                                            <td>
                                                @php
                                                    $statusMap = [
                                                        2 => 'Under Investigation',
                                                        3 => 'Detected & Property Recovered',
                                                        4 => 'Detected but Property Not Recovered',
                                                        5 => 'Mobile Recovered – Collect from PS',
                                                        6 => 'Not Detected – Closure Report Filed',
                                                    ];
                                                    
                                                    $statusClassMap = [
                                                        2 => 'status-under-investigation',
                                                        3 => 'status-detected-recovered',
                                                        4 => 'status-detected-not-recovered',
                                                        5 => 'status-mobile-recovered',
                                                        6 => 'status-not-detected-closed',
                                                    ];
                                                    
                                                    $statusText = $statusMap[$complaint->status] ?? 'Unknown';
                                                    $statusClass = $statusClassMap[$complaint->status] ?? 'status-pending';
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">
                                                    <i class="fa-regular fa-square-check"></i> {{ $statusText }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('voyager.complaints.edit', $complaint->id) }}" class="btn btn-sm btn-primary edit">
                                                    <i class="fa-regular fa-eye"></i> {{ __('voyager::generic.view') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="no-complaints">No complaints found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{-- <h4 class="modal-title">
                        <i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ $dataType->getTranslatedAttribute('display_name_singular') }}?
                    </h4> --}}
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <!-- DataTables -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "order": [],
                "language": {!! json_encode(__('voyager::datatable'), true) !!},
                "columnDefs": [{"targets": -1, "searchable":  false, "orderable": false}]
                @if(config('dashboard.data_tables.responsive')), responsive: true @endif
            });
        });


    </script>
@stop