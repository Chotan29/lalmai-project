{{-- Shared tabulation styling. Included by the printable sheet (print/exam/tabulation-sheet)
     and by every screen that draws a single student's row - the admin student profile and
     the student's own panel - so the row always looks like the sheet it came from. --}}
<style>
    :root {
        --tab-brand: #0f5132;
        --tab-brand-dark: #0a3a24;
        --tab-brand-light: #e7f3ec;
        --tab-zebra: #f4f9f6;
        --tab-fail-bg: #fdecea;
    }

    /* Colours must survive both the browser "Print" dialog and PDF export - without
       this, Chrome/Firefox drop every background colour by default when printing. */
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }

    .tab-sheet-wrapper {
        width: 100%;
        margin: 0 auto;
        background: #fff;
        border: 2px solid var(--tab-brand);
        border-radius: 4px;
        padding: 10px 14px;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }
    .tab-sheet-scroll { width: 100%; overflow-x: auto; }
    .tab-sheet-head { width: 100%; border-collapse: collapse; margin-bottom: 6px; background: var(--tab-brand-light); border: 1px solid var(--tab-brand); border-radius: 3px; }
    .tab-sheet-head td { vertical-align: middle; padding: 4px 8px; }
    .tab-head-logo-cell { width: 70px; text-align: center; }
    .tab-head-logo { max-width: 60px; max-height: 60px; object-fit: contain; }
    .tab-scale-table { border-collapse: collapse; font-size: 11px; width: 100%; }
    .tab-scale-table th { border: 1px solid var(--tab-brand); padding: 1px 8px; text-align: center; background: var(--tab-brand); color: #fff; }
    .tab-scale-table td { border: 1px solid var(--tab-brand); padding: 1px 8px; text-align: center; background: #fff; }
    .tab-title { text-align: center; }
    .tab-title h2 { margin: 0; font-size: 22px; font-weight: bold; color: var(--tab-brand-dark); }
    .tab-title h4 { margin: 4px 0; font-size: 16px; text-decoration: underline; color: var(--tab-brand-dark); }
    .tab-meta { font-size: 14px; font-weight: bold; margin-top: 6px; color: var(--tab-brand-dark); }
    .tab-meta .group-box { border: 1px solid var(--tab-brand); background: #fff; padding: 2px 14px; display: inline-block; border-radius: 3px; }

    /* Appeared / passed / rate. Prints as part of the sheet, because the figure is the first
       thing anyone asks and the second thing anyone loses. */
    .tab-summary {
      margin-top: 6px;
      font-size: 12px;
      font-weight: normal;
      color: var(--tab-brand-dark);
    }
    .tab-summary .tab-sum-item {
      display: inline-block;
      border: 1px solid var(--tab-brand);
      background: #fff;
      padding: 1px 10px;
      margin-right: 5px;
      border-radius: 3px;
    }
    .tab-summary .tab-sum-item b { font-weight: bold; margin-right: 5px; }
    .tab-summary .tab-sum-rate { background: #eef6ef; }
    /* Said in words, not only implied by a shorter list. */
    .tab-summary .tab-sum-note { font-style: italic; }

    /* Many subjects x up to 5 component columns: every cell has to be tight. */
    .tab-main-table { width: 100%; border-collapse: collapse; font-size: 11px; table-layout: fixed; }
    .tab-main-table th, .tab-main-table td { border: 1px solid #9ec5ae; padding: 2px 1px; text-align: center; vertical-align: middle; word-wrap: break-word; }
    .tab-main-table thead th { font-weight: bold; background: var(--tab-brand); color: #fff; }
    .tab-main-table .text-left { text-align: left; }
    .tab-main-table .col-roll { width: 52px; }
    .tab-main-table .col-name { width: 130px; }
    .tab-main-table .col-gpa { width: 34px; }
    .tab-subject-head { white-space: nowrap; }
    .tab-fullmark-row th { font-weight: normal; font-style: italic; background: var(--tab-brand-dark); color: #e7f3ec; }
    .tab-main-table tbody tr:nth-child(even),
    .tab-main-table tbody tr.tab-row-even { background: var(--tab-zebra); }
    .tab-main-table tbody tr:hover { background: #e9f5ee; }
    .tab-fail { color: #c0392b; font-weight: bold; background: var(--tab-fail-bg); }
    .tab-subject-legend { margin-top: 8px; font-size: 10px; line-height: 1.7; color: #333; }
    .tab-subject-legend span { white-space: nowrap; }

    .is-wide .tab-main-table { font-size: 9px; }
    .is-wide .tab-main-table .col-name { width: 110px; }
    .is-tight .tab-main-table { font-size: 7.5px; }
    .is-tight .tab-main-table th, .is-tight .tab-main-table td { padding: 1px 0; }
    .is-tight .tab-main-table .col-roll { width: 40px; }
    .is-tight .tab-main-table .col-name { width: 92px; }
    .is-tight .tab-main-table .col-gpa { width: 26px; }

    /* One student on screen: the sheet is only ever a couple of rows tall, so it gets a
       result strip above it instead of the printed sheet's college letterhead. */
    .tab-one-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 6px; }
    .tab-one-head strong { color: var(--tab-brand-dark); font-size: 14px; }
    .tab-one-gpa { font-weight: bold; padding: 3px 12px; border-radius: 3px; }
    .tab-one-gpa.is-pass { background: var(--tab-brand-light); color: var(--tab-brand); }
    .tab-one-gpa.is-fail { background: var(--tab-fail-bg); color: #c0392b; }

    .tab-publish-box { display: inline-block; margin-right: 14px; padding-right: 14px; border-right: 1px solid #ddd; }
    .paper-picker { display: inline-block; margin-right: 10px; }
    .paper-picker .btn.active { font-weight: bold; }

    @media print {
        .no-print { display: none !important; }
        .tab-sheet-wrapper { border: 2px solid var(--tab-brand); }
        .tab-sheet-scroll { overflow: visible !important; }
        body { margin: 0; padding: 0; }
        .page-content { padding: 0 !important; border: none !important; }
    }
</style>
