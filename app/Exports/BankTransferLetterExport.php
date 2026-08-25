<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * The transfer letters as a workbook.
 *
 * Two sheets, because the college sends two letters: one for its own accounts and one for the
 * department accounts. That is not a presentation choice - the department money belongs to the
 * departments and the bank is asked to move it separately.
 *
 * A sheet is only added if it has something on it. An empty second letter would still look like a
 * letter, and somebody would eventually send it.
 */
class BankTransferLetterExport implements WithMultipleSheets
{
    use Exportable;

    protected $letter;
    protected $heading;

    public function __construct($letter, $heading)
    {
        $this->letter  = $letter;
        $this->heading = $heading;
    }

    public function sheets(): array
    {
        $lines = $this->letter->all ?? array_merge($this->letter->college, $this->letter->department);

        if (!count($lines)) {
            return [];
        }

        /*
         * One sheet.
         *
         * This was two - the college accounts on one, the department accounts on another - read
         * from a sample file whose second sheet turned out to be a different letter entirely, for
         * the second year rather than for the departments. The college has always sent one letter
         * per batch with every account on it, and that is what this is.
         */
        return [new BankTransferLetterSheet($this->letter, $lines, $this->heading, 'ব্যাংক স্থানান্তর')];
    }
}

/**
 * One letter.
 *
 * The blade only carries content - what belongs on which line, in what order. This class carries
 * the look: bold labels, a bordered table, a real number format on the money column, column
 * widths that fit the content, a page set up to print on one sheet. It finds what to style by
 * reading the text back off the built sheet rather than by counting rows, so a wording change in
 * the blade (a longer body paragraph, a signature line added) cannot silently point the styling at
 * the wrong row.
 */
class BankTransferLetterSheet implements FromView, WithTitle, WithEvents
{
    protected $letter;
    protected $lines;
    protected $heading;
    protected $title;
    protected $tail;

    public function __construct($letter, $lines, $heading, $title, array $tail = [])
    {
        $this->letter  = $letter;
        $this->lines   = array_values($lines);
        $this->heading = $heading;
        $this->title   = $title;
        $this->tail    = $tail;
    }

    public function view(): View
    {
        return view('account.report.fee-collection-head.bank-letter', [
            'letter'  => $this->letter,
            'lines'   => $this->lines,
            'heading' => $this->heading,
            'tail'    => $this->tail,
        ]);
    }

    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->style($event->sheet->getDelegate());
            },
        ];
    }

    protected function style(Worksheet $sheet)
    {
        $last = $sheet->getHighestRow();

        /* A letter this short reads better in a slightly larger, plainer face than the workbook
           default - closer to what a typewriter or a word processor would have produced. */
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Nirmala UI')->setSize(11);
        $sheet->getStyle("A1:D{$last}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);

        /* Width by what the column actually holds - a narrow serial, a long account name, the
           account number, and the amount. */
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(48);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(15);

        /* The college's own name and address, set apart as a letterhead - the one thing every
           version of this letter, typed or handwritten, has led with. */
        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(22);

        /* Everything else is found by what it says, not by which row it happens to fall on - the
           body paragraph can grow a line and this still lands on the right place. */
        $tableTop = null;
        $tableBottom = null;
        $totalsTop = null;

        for ($r = 3; $r <= $last; $r++) {
            $a = trim((string) $sheet->getCell("A{$r}")->getValue());
            $c = trim((string) $sheet->getCell("C{$r}")->getValue());

            if (in_array($a, ['সূত্র:', 'প্রেরক:', 'প্রাপক:', 'জনাব,'], true)) {
                $sheet->getStyle("A{$r}")->getFont()->setBold(true);
            }
            if (mb_strpos($c, 'তারিখ:') === 0) {
                $sheet->getStyle("C{$r}")->getFont()->setBold(true);
            }
            if (mb_strpos($a, 'বিষয়') === 0) {
                $sheet->getStyle("A{$r}:D{$r}")->getFont()->setBold(true);
                $sheet->getRowDimension($r)->setRowHeight(32);
            }
            /* The body sentence is the one long unstyled paragraph on the page - give it room. */
            if ($a !== '' && mb_strlen($a) > 60 && mb_strpos($a, 'বিষয়') !== 0) {
                $sheet->getRowDimension($r)->setRowHeight(48);
            }

            if ($a === 'ক্রম') {
                $tableTop = $r;
                $sheet->getStyle("A{$r}:D{$r}")->getFont()->setBold(true);
                $sheet->getStyle("A{$r}:D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$r}:D{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E7E6E6');
            }

            /*
             * The totals block, which is one row on the department letter and three on the college
             * one. Found by where it starts and where the words line begins rather than by an exact
             * label: the first total is now called "সর্বমোট (এই পত্রে স্থানান্তরযোগ্য)" on one
             * sheet and plain "সর্বমোট" on the other, and matching the whole string would have
             * quietly styled neither.
             */
            $b = trim((string) $sheet->getCell("B{$r}")->getValue());
            if ($totalsTop === null && mb_strpos($b, 'সর্বমোট') === 0) {
                $totalsTop = $r;
            }
            if ($totalsTop !== null && mb_strpos($a, 'কথায়') === 0) {
                /* The table ends on the row before the words - everything between is a total. */
                $tableBottom = $r - 1;
            }

            /* The signature block: three lines, none of them labelled, so they are found by being
               the name the settings screen holds. */
            if ($c !== '' && $c === trim((string) $this->letter->settings->principal_name)) {
                $sheet->getStyle("C{$r}:D{$r}")->getFont()->setBold(true);
                $sheet->getStyle("C" . ($r + 1) . ":D" . ($r + 1))->getFont()->setBold(true);
            }
        }

        /* The table itself: a full box, a thin grid inside it, the serial column centred, the
           money column a real number - comma grouped, right aligned - not text that merely looks
           like one. */
        if ($tableTop && $tableBottom) {
            $range = "A{$tableTop}:D{$tableBottom}";
            $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($range)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM);

            $sheet->getStyle('A' . ($tableTop + 1) . ':A' . $tableBottom)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('D' . ($tableTop + 1) . ":D{$tableBottom}")
                ->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('D' . ($tableTop + 1) . ":D{$tableBottom}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            /* The account number is an identifier, not a quantity - but reading plain digits back
               out of html, PhpSpreadsheet cannot tell that apart from a very large number, and
               stores it as one. A thirteen digit number left as "General" is exactly what a
               spreadsheet turns into scientific notation the moment the column is not wide enough
               or the file is opened somewhere other than Excel - which is how a correct account
               number ends up on the page as 1.3359E+12. Forced back to text here, the digits are
               the only thing that can ever appear in the cell. */
            for ($r = $tableTop + 1; $r < $tableBottom; $r++) {
                $no = (string) $sheet->getCell("C{$r}")->getValue();
                if ($no !== '') {
                    $sheet->setCellValueExplicit("C{$r}", $no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
            }
            $sheet->getStyle('C' . ($tableTop + 1) . ":C" . ($tableBottom - 1))
                ->getNumberFormat()->setFormatCode('@');

            /*
             * The totals, set apart from the accounts above them.
             *
             * Done after the full-box pass rather than inside the row loop, because that pass
             * resets every edge to thin and would have wiped the heavier rule out. Bold runs over
             * the whole block - on the college letter that is three rows, not one.
             */
            if ($totalsTop) {
                $sheet->getStyle("A{$totalsTop}:D{$tableBottom}")->getFont()->setBold(true);
                $sheet->getStyle("A{$totalsTop}:D{$totalsTop}")
                    ->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);
            }
        }

        /* Set up to print - this is a letter that gets carried to a bank, not just opened on a
           screen. One page wide, A4, a normal margin. */
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.6)->setBottom(0.6)->setLeft(0.7)->setRight(0.7);
        $sheet->setShowGridlines(false);
    }
}
