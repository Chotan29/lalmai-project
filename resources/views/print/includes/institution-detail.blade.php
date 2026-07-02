<div class="institution-header">
    <div class="institution-header-grid">
        <div class="logo-container">
            @if(isset($generalSetting->logo))
                <img class="institution-logo" 
                     src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" 
                     alt="{{ isset($generalSetting->institute) ? $generalSetting->institute : 'Institution Logo' }}">
            @endif
        </div>
        
        <div class="institution-details">
            @if(isset($generalSetting->salogan) && $generalSetting->salogan)
                <div class="institution-slogan">{{ $generalSetting->salogan }}</div>
            @endif
            
            <h1 class="institution-name">
                {{ isset($generalSetting->institute) ? $generalSetting->institute : 'Institution Name' }}
            </h1>
            
            <div class="institution-contact">
                @if(isset($generalSetting->address) && $generalSetting->address)
                    <span class="contact-item">
                        <i class="ace-icon fa fa-map-marker-alt"></i> {{ $generalSetting->address }}
                    </span>
                @endif
                
                @if(isset($generalSetting->phone) && $generalSetting->phone)
                    <span class="contact-item">
                        <i class="ace-icon fa fa-phone"></i> {{ $generalSetting->phone }}
                    </span>
                @endif
                
                @if(isset($generalSetting->email) && $generalSetting->email)
                    <span class="contact-item">
                        <i class="ace-icon fa fa-envelope"></i> {{ $generalSetting->email }}
                    </span>
                @endif
                
                @if(isset($generalSetting->website) && $generalSetting->website)
                    <span class="contact-item">
                        <i class="ace-icon fa fa-globe"></i> {{ $generalSetting->website }}
                    </span>
                @endif
            </div>
        </div>

        <div class="header-spacer"></div>
    </div>
</div>

<style>
    .institution-header {
        padding: 15px 0;
        margin-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .institution-header-grid {
        display: grid;
        grid-template-columns: 140px 1fr 140px;
        align-items: center;
    }

    .logo-container {
        text-align: center;
        padding: 10px;
    }
    
    .institution-logo {
        max-width: 100%;
        height: auto;
        max-height: 100px;
        object-fit: contain;
    }
    
    .institution-details {
        text-align: center;
    }

    .header-spacer {
        min-height: 1px;
    }
    
    .institution-slogan {
        font-size: 14px;
        color: #666;
        font-style: italic;
        margin-bottom: 5px;
    }
    
    .institution-name {
        font-family: 'Bowlby One SC', sans-serif;
        font-weight: 600;
        font-size: 28px;
        margin: 5px 0;
        color: #2a6496;
        text-transform: uppercase;
    }
    
    .institution-contact {
        font-size: 14px;
        color: #555;
        margin-top: 10px;
    }
    
    .contact-item {
        display: inline-block;
        margin: 0 10px 5px 0;
    }
    
    .contact-item i {
        margin-right: 5px;
        color: #428bca;
    }
    
    @media (max-width: 768px) {
        .institution-header-grid {
            grid-template-columns: 1fr;
        }

        .institution-name {
            font-size: 22px;
        }
        
        .institution-contact {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .contact-item {
            margin: 0 5px 5px 0;
        }
    }
    
    @media print {
        .institution-header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .institution-name {
            font-size: 24pt;
        }
        
        .institution-contact {
            font-size: 12pt;
        }
    }
</style>