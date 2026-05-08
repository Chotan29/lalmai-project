@extends('layouts.master')
@section('css')
<style>
    .license-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }
    .license-header {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        color: white;
        padding: 20px;
        text-align: center;
    }
    .license-body {
        padding: 25px;
        background: white;
    }
    .license-item {
        display: flex;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .license-item:last-child {
        border-bottom: none;
    }
    .license-label {
        font-weight: 600;
        color: #555;
    }
    .license-value {
        font-weight: 500;
        text-align: right;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 10px;
    }
    .status-active {
        background-color: #e6f7ee;
        color: #00a854;
    }
    .status-expired {
        background-color: #fff1f0;
        color: #f5222d;
    }
    .buyer-link {
        color: #1890ff;
        text-decoration: none;
        transition: all 0.3s;
    }
    .buyer-link:hover {
        color: #40a9ff;
        text-decoration: underline;
    }
    .time-ago {
        font-size: 12px;
        color: #888;
        margin-left: 5px;
    }
    .icon {
        margin-right: 5px;
    }
</style>
@endsection
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="license-card">
                    <div class="license-header">
                        <h2 style="margin: 0; font-weight: 700;">Unlimited Edu Firm License</h2>
                        <div style="margin-top: 10px;">
                            @if(env('APP_STATUS')!=0)
                                <span class="status-badge status-expired">
                                    <i class="ace-icon fa fa-certificate icon"></i> License Expired
                                </span>
                            @else
                                <span class="status-badge status-active">
                                    <i class="ace-icon fa fa-certificate icon"></i> License Active
                                </span>
                            @endif

                            @if(env('HELP_STATUS')!=0)
                                <span class="status-badge status-expired">
                                    <i class="ace-icon fa fa-phone icon"></i> Support Expired
                                </span>
                            @else
                                <span class="status-badge status-active">
                                    <i class="ace-icon fa fa-comment icon"></i> Support Active
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="license-body">
                        <div class="license-item">
                            <span class="license-label">License Key:</span>
                            <span class="license-value">{{$body->license}}</span>
                        </div>
                        
                        <div class="license-item">
                            <span class="license-label">Purchase Date:</span>
                            <span class="license-value">
                                {{\Carbon\Carbon::parse($body->sold_at)->format('M d, Y')}}
                                <span class="time-ago">({{\Carbon\Carbon::parse($body->sold_at)->diffForHumans()}})</span>
                            </span>
                        </div>
                        
                        <div class="license-item">
                            <span class="license-label">License Status:</span>
                            <span class="license-value">
                                @if(env('APP_STATUS')!=0)
                                    <span class="status-badge status-expired">Expired</span>
                                @else
                                    <span class="status-badge status-active">Active</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="license-item">
                            <span class="license-label">Buyer:</span>
                            <span class="license-value">
                                <a href="https://codecanyon.net/user/{{$body->buyer}}" class="buyer-link" target="_blank">
                                    {{$body->buyer}}
                                </a>
                            </span>
                        </div>
                        
                        <div class="license-item">
                            <span class="license-label">Total Purchases:</span>
                            <span class="license-value">{{$body->purchase_count}}</span>
                        </div>
                        
                        <div class="license-item">
                            <span class="license-label">Support Until:</span>
                            <span class="license-value">
                                {{\Carbon\Carbon::parse($body->supported_until)->format('M d, Y')}}
                                <span class="time-ago">({{\Carbon\arbon::parse($body->supported_until)->diffForHumans()}})</span>
                                @if(env('HELP_STATUS')!=0)
                                    <span class="status-badge status-expired">Expired</span>
                                @else
                                    <span class="status-badge status-active">Active</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p>Need help with your license? <a href="#" style="color: #1890ff;">Contact our support team</a></p>
                </div>
            </div><!-- /.page-content -->
        </div><!-- /.main-content-inner -->
    </div><!-- /.main-content -->
@endsection
@section('js')
@endsection