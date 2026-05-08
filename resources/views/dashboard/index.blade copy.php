@extends('layouts.master')

@section('css')
    {{-- Base dashboard CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />

    {{-- Font Awesome for icons --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkfAm3JPEY2oihcGQjv4zj1r5v8b7R8r4C2vHkrmN3V3J0Z6Q5x2f4Ykg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root{
          --primary:#4361ee; --secondary:#3f37c9; --success:#22c55e; --info:#0ea5e9;
          --warning:#f59e0b; --danger:#ef4444; --light:#f8fafc; --dark:#0f172a;
          --muted:#64748b; --card:#ffffff; --border:#e2e8f0; --background:#f1f5f9;
        }

        /* Page basics */
        body{background:var(--background);color:var(--dark)}
        .page-title{font-weight:800;color:var(--dark)}
        .page-title small{display:block;color:var(--muted);font-weight:500;font-size:.9rem;margin-top:.25rem}
        .modern-card{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.05);padding:1.5rem}

        /* Search */
        .search-bar{position:relative;max-width:420px}
        .search-bar input{width:100%;border-radius:.875rem;padding:.75rem 1rem .75rem 2.5rem;border:1px solid var(--border);background:#fff;transition:.3s}
        .search-bar input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(67,97,238,.1)}
        .search-bar i{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--muted)}

        /* Stats (unchanged look) */
        .stat-icon{width:60px;height:60px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-right:1rem;font-size:1.5rem}
        .stat-card{background:#fff;border-radius:1rem;padding:1.5rem;box-shadow:0 4px 20px rgba(0,0,0,.05);display:flex;align-items:center;transition:.3s}
        .stat-card:hover{transform:translateY(-5px);box-shadow:0 8px 30px rgba(0,0,0,.1)}
        .stat-value{font-size:1.75rem;font-weight:800;margin-bottom:.25rem}
        .stat-label{color:var(--muted);font-size:.9rem}
        .stat-change{display:flex;align-items:center;font-size:.85rem;margin-top:.25rem}
        .stat-change.positive{color:var(--success)} .stat-change.negative{color:var(--danger)}

        /* Card wrapper */
        .card{background:#fff;border-radius:1rem;padding:1.5rem;box-shadow:0 4px 20px rgba(0,0,0,.05);margin-bottom:1.5rem}
        .card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem}
        .card-title{font-weight:700;font-size:1.25rem;display:flex;align-items:center;gap:.5rem}
        .view-all{color:var(--primary);font-size:.9rem;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:.25rem}

        /* Quick Launch tiles (like your reference) */
        .ql-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem}
        .ql-tile{background:#fff;border-radius:.75rem;padding:1.25rem;text-align:center;transition:.3s;border:1px solid var(--border);cursor:pointer;position:relative;overflow:hidden}
        .ql-tile:hover{transform:translateY(-3px);box-shadow:0 10px 25px rgba(0,0,0,.1);border-color:var(--primary)}
        .ql-icon{width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;font-size:1.25rem;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}
        .ql-title{font-weight:600;margin-bottom:.25rem}
        .ql-desc{color:var(--muted);font-size:.85rem}

        /* Custom modal (no Bootstrap dependency) */
        .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);display:none;align-items:center;justify-content:center;z-index:2000}
        .modal-overlay.open{display:flex;animation:fadeIn .15s ease-out}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        .modal{width:min(1000px,92vw);max-height:85vh;background:#fff;border-radius:16px;box-shadow:0 30px 80px rgba(2,8,23,.35);display:flex;flex-direction:column;overflow:hidden;transform:scale(.98);animation:pop .18s ease-out forwards}
        @keyframes pop{to{transform:scale(1)}}
        .modal-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;color:#fff;background:linear-gradient(135deg,var(--primary),var(--secondary))}
        .modal-title-wrap{display:flex;align-items:center;gap:10px}
        .modal-icon{width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center}
        .modal-title{font-weight:800}
        .modal-close{border:0;background:transparent;color:#fff;font-size:1.5rem;line-height:1;cursor:pointer;padding:4px 8px;border-radius:8px}
        .modal-close:hover{background:rgba(255,255,255,.15)}
        .modal-toolbar{display:flex;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid var(--border);flex-wrap:wrap}
        .modal-breadcrumb{display:flex;align-items:center;gap:8px;font-size:.9rem;font-weight:600;color:#334155}
        .crumb{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;background:#eef2ff;border:1px solid #e0e7ff;cursor:pointer}
        .modal-search{margin-left:auto;position:relative;max-width:320px;flex:1}
        .modal-search input{width:100%;border:1px solid var(--border);border-radius:10px;padding:.55rem .9rem .55rem 2.1rem}
        .modal-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#64748b}
        .btn-back{border:1px solid #e2e8f0;background:#fff;color:#0f172a;border-radius:10px;padding:.45rem .75rem;font-weight:600}
        .btn-back.hidden{display:none}
        .modal-body{padding:16px;overflow:auto}
        .child-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px}
        .child-card{background:#fff;border:1px solid #eef2f7;border-radius:14px;padding:14px;text-align:center;box-shadow:0 6px 18px rgba(2,8,23,.06);transition:.2s;text-decoration:none;color:inherit;cursor:pointer}
        .child-card:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(2,8,23,.1)}
        .child-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;margin:0 auto 8px}
        .child-title{font-weight:800;margin:2px 0}
        .child-desc{color:#64748b;font-size:.9rem}
        .route-chip{display:inline-flex;align-items:center;gap:6px;font-size:.78rem;color:#334155;background:#eef2ff;border:1px solid #e0e7ff;border-radius:999px;padding:3px 8px;margin-top:8px}

        @media (max-width:768px){
          .ql-grid{grid-template-columns:1fr}
          .search-bar{max-width:none}
        }
    </style>
@endsection

@section('content')
<div class="main-content">
  <div class="main-content-inner">
    <div class="page-content">
      @include('layouts.includes.template_setting')

      {{-- Header --}}
      <div class="modern-card mb-3">
        <div class="d-flex justify-content-between align-items-center">
          <h1 class="page-title">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            <small>Modern Overview & Quick Access</small>
          </h1>
          <div class="header-actions d-flex align-items-center">
            @include('includes.flash_messages')
            @include('dashboard.includes.buttons')
          </div>
        </div>
        <div class="mt-3">
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input id="menu-search" type="search" placeholder="Search groups (press / to focus)…">
          </div>
        </div>
      </div>

      {{-- Example stats row (optional, keep your own stats) --}}
      {{-- <div class="row">
        <div class="col-md-3">
          <div class="stat-card">
            <div class="stat-icon" style="background-color:rgba(67,97,238,.1);color:var(--primary)"><i class="fas fa-users"></i></div>
            <div>
              <div class="stat-value">2,548</div>
              <div class="stat-label">Total Users</div>
              <div class="stat-change positive"><i class="fas fa-arrow-up"></i>&nbsp;12.5% from last week</div>
            </div>
          </div>
        </div>
        <!-- Add your other stat cards here -->
      </div> --}}

      {{-- Quick Launch (cards) --}}
      <div class="card">
        <div class="card-header">
          <h2 class="card-title"><i class="fas fa-rocket"></i> Quick Launch</h2>
          <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
        </div>

        <div class="ql-grid" id="qlGrid">
          @foreach (($data['menu_groups'] ?? []) as $group)
            <div class="ql-tile"
                data-group="{{ $group['group_slug'] }}"
                data-title="{{ $group['title'] }}"
                data-icon="{{ $group['icon'] }}"
                data-color="{{ $group['color'] ?? '#4361ee' }}"
                data-dark="{{ $group['dark_color'] ?? '#3f37c9' }}">
              <div class="ql-icon" style="background:linear-gradient(135deg, {{ $group['color'] ?? '#4361ee' }}, {{ $group['dark_color'] ?? '#3f37c9' }})">
                <i class="fa {{ $group['icon'] }}"></i>
              </div>
              <div class="ql-title">{{ $group['title'] }}</div>
              @if (!empty($group['desc']))
                <div class="ql-desc">{{ $group['desc'] }}</div>
              @endif
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</div>

{{-- Custom Modal (no Bootstrap) --}}
<div class="modal-overlay" id="qlModal" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="qlModalTitle">
    <div class="modal-header" id="qlModalHeader">
      <div class="modal-title-wrap">
        <div class="modal-icon" id="qlModalIcon"><i class="fas fa-layer-group"></i></div>
        <div class="modal-title" id="qlModalTitle">Group</div>
      </div>
      <button class="modal-close" id="qlModalClose" aria-label="Close">&times;</button>
    </div>

    <div class="modal-toolbar">
      <div class="modal-breadcrumb" id="qlModalBreadcrumb">
        <span class="crumb"><i class="fa fa-folder"></i> <span class="crumb-text">Root</span></span>
      </div>
      <div class="modal-search">
        <i class="fa fa-search"></i>
        <input type="search" id="qlModalSearch" placeholder="Filter items…">
      </div>
      <button class="btn-back hidden" id="qlModalBack"><i class="fa fa-arrow-left"></i>&nbsp;Back</button>
    </div>

    <div class="modal-body">
      <div class="child-grid" id="qlModalGrid"></div>
    </div>
  </div>
</div>
@endsection

@section('js')
    {{-- You can keep these if you use them elsewhere; modal below is vanilla JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      // Make PHP data available to JS
      window.MENU_GROUPS = @json($data['menu_groups'] ?? []);

      // Quick focus on search with '/'
      document.addEventListener('keydown', e => {
        if (e.key === '/') { e.preventDefault(); const el = document.getElementById('menu-search'); el?.focus(); el?.select(); }
      });

      // Build a group map
      const GROUP_MAP = {};
      (window.MENU_GROUPS || []).forEach(g => GROUP_MAP[g.group_slug] = g);

      // Filter tiles by header search
      const topSearch = document.getElementById('menu-search');
      topSearch?.addEventListener('input', () => {
        const q = (topSearch.value || '').trim().toLowerCase();
        document.querySelectorAll('.ql-tile').forEach(t => {
          const hay = `${t.dataset.title||''}`.toLowerCase();
          t.style.display = (!q || hay.includes(q)) ? '' : 'none';
        });
      });

      // --- Modal logic (vanilla) ---
      const modalOverlay = document.getElementById('qlModal');
      const modalTitle   = document.getElementById('qlModalTitle');
      const modalIcon    = document.getElementById('qlModalIcon');
      const modalHeader  = document.getElementById('qlModalHeader');
      const modalGrid    = document.getElementById('qlModalGrid');
      const modalSearch  = document.getElementById('qlModalSearch');
      const modalClose   = document.getElementById('qlModalClose');
      const modalBack    = document.getElementById('qlModalBack');
      const modalBreadcrumb = document.getElementById('qlModalBreadcrumb');

      const navStack = []; // { title, items, color, icon }

      // Utils
      function hexToRgb(hex){ let c = (hex||'#4361ee').replace('#',''); if (c.length===3) c = c.split('').map(x=>x+x).join(''); const n = parseInt(c,16); return {r:(n>>16)&255,g:(n>>8)&255,b:n&255}; }
      function darken(hex, amt=-40){ const {r,g,b} = hexToRgb(hex); const clamp=v=>Math.max(0,Math.min(255,v)); const rr=clamp(r+amt),gg=clamp(g+amt),bb=clamp(b+amt); return '#'+((1<<24)+(rr<<16)+(gg<<8)+bb).toString(16).slice(1); }
      function makeIconBG(color){ const d = darken(color||'#4361ee', -40); return `linear-gradient(135deg, ${color||'#4361ee'} 0%, ${d} 100%)`; }
      function normalizeList(list){ if (!list) return []; if (Array.isArray(list)) return list; if (typeof list==='object') return Object.values(list); return []; }
      function pick(v, f){ return v?.[f] ?? v?.[`data_${f}`] ?? ''; }
      function nodeTitle(n){ return n.title || n.data_title || 'Untitled'; }
      function nodeDesc(n){ return n.desc || n.data_desc || ''; }
      function nodeIcon(n){ return n.icon || 'fa-circle'; }
      function nodeUrl(n){ return n.url || null; }
      function nodeChildren(n){ return normalizeList(n.children); }
      function paletteFrom(n, fallback){ return { color: n.color || fallback?.color || '#4361ee', dark_color: n.dark_color || fallback?.dark_color || '#3f37c9' }; }

      function setHeader(title, iconClass, color){
        modalTitle.textContent = title || 'Group';
        modalIcon.innerHTML = `<i class="fa ${iconClass || 'fa-layer-group'}"></i>`;
        modalHeader.style.background = `linear-gradient(135deg, ${color || '#4361ee'}, ${darken(color || '#4361ee', -40)})`;
      }

      function setBreadcrumb(){
        modalBreadcrumb.innerHTML = '';
        navStack.forEach((lvl, idx) => {
          const span = document.createElement('span');
          span.className = 'crumb';
          span.innerHTML = `<i class="fa ${idx===0?'fa-folder':'fa-diagram-project'}"></i> <span class="crumb-text">${lvl.title}</span>`;
          span.addEventListener('click', () => {
            // Go to this level
            navStack.splice(idx+1);
            const top = navStack[navStack.length - 1];
            setHeader(top.title, top.icon, top.color);
            setBreadcrumb();
            renderCards(top.items, top.color);
          });
          modalBreadcrumb.appendChild(span);
        });
        modalBack.classList.toggle('hidden', navStack.length <= 1);
      }

      function renderCards(items, paletteColor){
        const list = normalizeList(items);
        const q = (modalSearch.value || '').trim().toLowerCase();
        const filtered = list.filter(n => {
          const hay = `${nodeTitle(n)} ${nodeDesc(n)} ${pick(n,'route')}`.toLowerCase();
          return !q || hay.includes(q);
        });

        modalGrid.innerHTML = filtered.map(n => {
          const pal = paletteFrom(n, { color: paletteColor });
          const bg = makeIconBG(pal.color);
          const kids = nodeChildren(n);
          const link = nodeUrl(n);

          return `
            <a class="child-card" ${kids.length ? 'data-action="drill"' : (link ? `href="${link}"` : 'href="javascript:void(0)"')}
               data-node='${JSON.stringify(n).replace(/'/g,"&#39;")}'
               data-palette='${JSON.stringify(pal).replace(/'/g,"&#39;")}'>
              <div class="child-icon" style="background:${bg}"><i class="fa ${nodeIcon(n)}"></i></div>
              <div class="child-title">${nodeTitle(n)}</div>
              ${nodeDesc(n) ? `<div class="child-desc">${nodeDesc(n)}</div>` : ''}
              ${link ? `<div class="route-chip"><i class="fa fa-link"></i> Open</div>` : (kids.length ? `<div class="route-chip"><i class="fa fa-layer-group"></i> Explore</div>` : '')}
            </a>`;
        }).join('') || '<div class="child-desc">No items.</div>';

        // drill handlers
        modalGrid.querySelectorAll('[data-action="drill"]').forEach(a => {
          a.addEventListener('click', e => {
            e.preventDefault();
            const node = JSON.parse(a.getAttribute('data-node'));
            const pal  = JSON.parse(a.getAttribute('data-palette'));
            const kids = nodeChildren(node);
            navStack.push({ title: nodeTitle(node), items: kids, color: pal.color, icon: nodeIcon(node) });
            setHeader(nodeTitle(node), nodeIcon(node), pal.color);
            setBreadcrumb();
            renderCards(kids, pal.color);
          });
        });
      }

      function openGroup(slug){
        const g = GROUP_MAP[slug];
        if (!g) return;
        const color = g.color || '#4361ee';

        navStack.length = 0;
        navStack.push({ title: g.title, items: normalizeList(g.items), color, icon: g.icon });
        setHeader(g.title, g.icon, color);
        setBreadcrumb();
        modalSearch.value = '';
        renderCards(g.items, color);

        modalOverlay.classList.add('open');
        modalOverlay.setAttribute('aria-hidden','false');
        setTimeout(() => modalSearch.focus(), 80);
      }
      function closeModal(){
        modalOverlay.classList.remove('open');
        modalOverlay.setAttribute('aria-hidden','true');
      }

      // Tile clicks
      document.querySelectorAll('.ql-tile').forEach(tile => {
        tile.addEventListener('click', () => openGroup(tile.dataset.group));
      });

      // Modal events
      modalClose.addEventListener('click', closeModal);
      modalOverlay.addEventListener('click', e => { if (e.target === modalOverlay) closeModal(); });
      document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

      modalBack.addEventListener('click', () => {
        if (navStack.length > 1){
          navStack.pop();
          const top = navStack[navStack.length - 1];
          setHeader(top.title, top.icon, top.color);
          setBreadcrumb();
          renderCards(top.items, top.color);
        }
      });

      modalSearch.addEventListener('input', () => {
        const top = navStack[navStack.length - 1];
        renderCards(top.items, top.color);
      });
    </script>
@endsection
