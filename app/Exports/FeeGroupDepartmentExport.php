<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * The department list from the Fee Collection Head report, as a spreadsheet.
 *
 * The same rows the screen shows. It is built from a view rather than an array so the file
 * carries the fee's title and period at the top - a sheet of numbers with no heading cannot be
 * filed or checked a month later.
 */
class FeeGroupDepartmentExport implements FromView, ShouldAutoSize
{
    protected $rows;
    protected $title;
    protected $period;
    protected $collegeTotal;
    protected $departmentTotal;
    protected $grandTotal;

    public function __construct($rows, $title, $period, $collegeTotal, $departmentTotal, $grandTotal)
    {
        $this->rows            = $rows;
        $this->title           = $title;
        $this->period          = $period;
        $this->collegeTotal    = $collegeTotal;
        $this->departmentTotal = $departmentTotal;
        $this->grandTotal      = $grandTotal;
    }

    public function view(): View
    {
        return view('exports.fee-group-departments', [
            'rows'            => $this->rows,
            'title'           => $this->title,
            'period'          => $this->period,
            'collegeTotal'    => $this->collegeTotal,
            'departmentTotal' => $this->departmentTotal,
            'grandTotal'      => $this->grandTotal,
        ]);
    }
}
